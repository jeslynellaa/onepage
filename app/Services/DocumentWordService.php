<?php

namespace App\Services;

use App\Models\Document;
use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Exception\Exception as PhpWordException;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\SimpleType\NumberFormat;
use PhpOffice\PhpWord\Style\Table as TableStyle;

/**
 * Builds an editable .docx of a System Procedure, with the same content in the same order as
 * pdf/system_procedure.blade.php. It doesn't copy the PDF's look exactly, and anything added to
 * that template has to be added here too.
 *
 * Signature images are deliberately left out: in an editable file they could stay attached to
 * content the signatory never approved, so signing is recorded as "(e-signed)" text instead.
 */
class DocumentWordService
{
    private const FONTS = [
        'Times-Roman' => 'Times New Roman',
        'Helvetica' => 'Arial',
        'Courier' => 'Courier New',
    ];

    // 65px page margins, matching @page in the PDF template.
    private const MARGIN_TWIPS = 975;

    private const PAGE_WIDTH_TWIPS = ['a4' => 11906, 'letter' => 12240];

    private const BORDER = ['borderSize' => 6, 'borderColor' => '000000'];

    private const OUTLINED_TABLE = [
        'borderTopSize' => 6, 'borderTopColor' => '000000',
        'borderRightSize' => 6, 'borderRightColor' => '000000',
        'borderBottomSize' => 6, 'borderBottomColor' => '000000',
        'borderLeftSize' => 6, 'borderLeftColor' => '000000',
    ];

    private PhpWord $word;

    private int $contentWidth;

    private int $listCount = 0;

    /**
     * Returns the path of a temp .docx file; the caller is responsible for deleting it.
     */
    public function generate(Document $doc): string
    {
        $doc->load(['steps.interfaces', 'section.processOwner', 'section.reviewer', 'section.approver', 'definitionOfTerms', 'company']);

        // Without this PhpWord writes text unescaped, so a "&" in a title would corrupt the file.
        Settings::setOutputEscapingEnabled(true);

        $paper = strtolower((string) $doc->company->paper_size) === 'letter' ? 'letter' : 'a4';
        $this->contentWidth = self::PAGE_WIDTH_TWIPS[$paper] - 2 * self::MARGIN_TWIPS;
        $this->listCount = 0;

        $this->word = new PhpWord;
        $this->word->setDefaultFontName(self::FONTS[$doc->company->pdf_font] ?? 'Arial');
        $this->word->setDefaultFontSize(11);
        // Otherwise Word's own default gap after each paragraph makes every table row too tall.
        $this->word->setDefaultParagraphStyle(['spaceBefore' => 0, 'spaceAfter' => 0]);
        $this->word->getDocInfo()
            ->setTitle($this->clean($doc->title))
            ->setCreator($this->clean($doc->company->name))
            ->setCompany($this->clean($doc->company->name));

        $section = $this->word->addSection([
            'paperSize' => $paper === 'letter' ? 'Letter' : 'A4',
            'marginLeft' => self::MARGIN_TWIPS,
            'marginRight' => self::MARGIN_TWIPS,
            'marginTop' => 720,
            'marginBottom' => 720,
            'headerHeight' => 400,
            'footerHeight' => 300,
        ]);

        [$background, $foreground] = $this->brandColors($doc->company->hex_code);

        $this->addHeader($section, $doc, $background, $foreground);
        $this->addFooter($section);
        $this->addObjectiveAndScope($section, $doc);
        $this->addDefinitionOfTerms($section, $doc);
        $this->addProcessTable($section, $doc, $background, $foreground);
        $this->addNotes($section, $doc);
        $this->addInterfaces($section, $doc);
        $this->addSignatories($section, $doc);

        $path = tempnam(sys_get_temp_dir(), 'sp-word-');
        IOFactory::createWriter($this->word, 'Word2007')->save($path);

        return $path;
    }

    private function addHeader($section, Document $doc, ?string $background, string $foreground): void
    {
        $header = $section->addHeader();
        $width = $this->contentWidth;
        $labelWidth = (int) ($width * 0.2);
        $logoWidth = $width - 2 * $labelWidth;

        $table = $header->addTable($this->tableStyle([$logoWidth, $labelWidth, $labelWidth]) + self::BORDER);

        $table->addRow();
        $table->addCell($width, ['gridSpan' => 3, 'bgColor' => $background])
            ->addText('SYSTEM PROCEDURES MANUAL', ['bold' => true, 'size' => 10, 'color' => $foreground], ['alignment' => Jc::CENTER]);

        $rows = [
            'Section No.:' => $doc->section?->section_number,
            'Revision No.:' => $doc->revision_number ?? '-',
            'Document No.:' => $doc->code,
            'Effective Date:' => $doc->effective_date ? date('m/d/Y', strtotime($doc->effective_date)) : '-',
            'Page Number:' => null,
        ];

        $first = true;
        foreach ($rows as $label => $value) {
            $table->addRow();
            $logoCell = $table->addCell($logoWidth, ['vMerge' => $first ? 'restart' : 'continue', 'valign' => 'center']);
            if ($first) {
                $this->addLogo($logoCell, $doc);
            }
            $first = false;

            $table->addCell($labelWidth)->addText($label);
            $valueCell = $table->addCell($labelWidth);
            if ($label === 'Page Number:') {
                $valueCell->addPreserveText('Page {PAGE} of {NUMPAGES}');
            } else {
                $valueCell->addText($this->clean($value));
            }
        }

        $table->addRow();
        $table->addCell($width, ['gridSpan' => 3])
            ->addText($this->clean($doc->title), ['bold' => true, 'size' => 14, 'allCaps' => true], ['alignment' => Jc::CENTER]);

        $notice = $header->addTextRun(['alignment' => Jc::CENTER, 'spaceBefore' => 40, 'spaceAfter' => 120]);
        $notice->addText('STRICTLY CONFIDENTIAL', ['bold' => true, 'italic' => true, 'size' => 9]);
        $notice->addText(
            ' - For use of '.mb_strtoupper($this->clean($doc->company->name)).' only. Unauthorized reproduction is strictly prohibited.',
            ['italic' => true, 'size' => 9]
        );
    }

    private function addLogo($cell, Document $doc): void
    {
        $path = $this->storageFilePath($doc->company->logo_path);

        if ($path && is_file($path) && ($size = @getimagesize($path)) && $size[1] > 0) {
            // 55pt tall like the PDF's header, but narrow enough that a wide logo stays in its cell.
            $height = min(55, 200 * $size[1] / max($size[0], 1));

            try {
                $cell->addImage($path, ['height' => $height, 'alignment' => Jc::CENTER]);

                return;
            } catch (PhpWordException) {
                // Not a format Word files can embed (e.g. WebP): fall through to the company name.
            }
        }

        $cell->addText($this->clean($doc->company->name), ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
    }

    private function addFooter($section): void
    {
        $section->addFooter()->addText(
            'Except for the MASTER COPY AND CONTROLLED COPIES, printed and downloaded copies of this documented information are considered uncontrolled.',
            ['bold' => true, 'italic' => true, 'size' => 8],
            ['alignment' => Jc::CENTER]
        );
    }

    private function addObjectiveAndScope($section, Document $doc): void
    {
        $labelWidth = (int) ($this->contentWidth * 0.2);
        $valueWidth = $this->contentWidth - $labelWidth;
        $table = $section->addTable($this->tableStyle([$labelWidth, $valueWidth]) + self::BORDER);

        foreach (['OBJECTIVE/S' => $doc->objective, 'SCOPE' => $doc->scope] as $label => $html) {
            $table->addRow();
            $table->addCell($labelWidth)->addText($label, ['bold' => true]);
            $this->addHtml($table->addCell($valueWidth), $html);
        }
    }

    private function addDefinitionOfTerms($section, Document $doc): void
    {
        if ($doc->definitionOfTerms->isEmpty()) {
            return;
        }

        $section->addText('DEFINITION OF TERMS', ['bold' => true], ['spaceBefore' => 200, 'spaceAfter' => 80, 'keepNext' => true]);

        $termWidth = (int) ($this->contentWidth * 0.3);
        $definitionWidth = $this->contentWidth - $termWidth;
        $table = $section->addTable($this->tableStyle([$termWidth, $definitionWidth]) + self::BORDER);

        $table->addRow(null, ['tblHeader' => true]);
        $table->addCell($termWidth)->addText('Term', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell($definitionWidth)->addText('Definition', ['bold' => true], ['alignment' => Jc::CENTER]);

        foreach ($doc->definitionOfTerms as $item) {
            $table->addRow(null, ['cantSplit' => true]);
            $table->addCell($termWidth)->addText($this->clean($item->term), ['bold' => true]);
            $table->addCell($definitionWidth)->addText($this->clean($item->definition), null, ['alignment' => Jc::BOTH]);
        }
    }

    /**
     * Word tables can't carry the PDF's drawn arrows and page connectors, so steps are joined by
     * an arrow character in their own row, and a page break just repeats the header row.
     */
    private function addProcessTable($section, Document $doc, ?string $background, string $foreground): void
    {
        $widths = [
            (int) ($this->contentWidth * 0.2),
            (int) ($this->contentWidth * 0.4),
            (int) ($this->contentWidth * 0.1),
        ];
        $widths[] = $this->contentWidth - array_sum($widths);

        $section->addTextBreak(1, ['size' => 6]);
        $table = $section->addTable($this->tableStyle($widths) + self::OUTLINED_TABLE);

        $headerFont = ['bold' => true, 'size' => 10, 'color' => $foreground];
        $center = ['alignment' => Jc::CENTER];
        $font = ['size' => 10];

        $table->addRow(null, ['tblHeader' => true]);
        foreach (['Responsibility', 'Activities', '', 'Interfaces'] as $i => $heading) {
            $table->addCell($widths[$i], ['bgColor' => $background])->addText($heading, $headerFont, $center);
        }

        $note = 1;
        $last = $doc->steps->count() - 1;

        foreach ($doc->steps->values() as $key => $step) {
            $table->addRow(null, ['cantSplit' => true]);
            $table->addCell($widths[0], ['valign' => 'center'])->addText($this->clean($step->responsibility), $font, $center);
            $table->addCell($widths[1], self::BORDER + ['valign' => 'center'])->addText($this->clean($step->activities), $font, $center);
            $table->addCell($widths[2], ['valign' => 'center'])->addText($step->note ? 'Note '.$note++ : '', $font, $center);

            $interfaces = $table->addCell($widths[3], ['valign' => 'center']);
            foreach ($step->interfaces as $interface) {
                $interfaces->addText($this->clean($interface->title), $font, $center);
            }

            if ($key !== $last) {
                $table->addRow(null, ['cantSplit' => true]);
                $table->addCell($widths[0]);
                $table->addCell($widths[1])->addText('↓', ['size' => 14, 'bold' => true], $center + ['keepNext' => true]);
                $table->addCell($widths[2]);
                $table->addCell($widths[3]);
            }
        }
    }

    private function addNotes($section, Document $doc): void
    {
        $note = 1;

        foreach ($doc->steps as $step) {
            if (empty($step->note)) {
                continue;
            }

            $section->addText('Note '.$note++.':', ['bold' => true], ['spaceBefore' => 160, 'keepNext' => true]);
            $this->addHtml($section, $step->note);
        }
    }

    private function addInterfaces($section, Document $doc): void
    {
        $interfaces = $doc->steps->flatMap(fn ($step) => $step->interfaces);
        $outputs = $interfaces->where('type', 'output')->unique('title')->values();
        $inputs = $interfaces->where('type', 'input')->unique('title')->values();

        $half = (int) ($this->contentWidth / 2);
        $widths = [$half, $this->contentWidth - $half];

        $section->addTextBreak(1, ['size' => 6]);
        $table = $section->addTable($this->tableStyle($widths) + self::BORDER);

        // Moves to the next page as a whole rather than splitting, like the PDF's page-break-inside: avoid.
        $heading = ['alignment' => Jc::CENTER, 'keepNext' => true];
        $table->addRow(null, ['cantSplit' => true]);
        $table->addCell($widths[0])->addText('Documented Information Generated', ['bold' => true], $heading);
        $table->addCell($widths[1])->addText('References', ['bold' => true], $heading);

        $table->addRow(null, ['cantSplit' => true]);
        $this->addInterfaceList($table->addCell($widths[0]), $outputs, '- No Document Outputs -');
        $this->addInterfaceList($table->addCell($widths[1]), $inputs, '- No References -');
    }

    private function addInterfaceList($cell, $items, string $empty): void
    {
        if ($items->isEmpty()) {
            $cell->addText($empty, null, ['alignment' => Jc::CENTER]);

            return;
        }

        $style = $this->listStyle(true);

        foreach ($items as $item) {
            $cell->addListItemRun(0, $style)->addText($this->clean($item->category.': '.$item->title));
        }
    }

    private function addSignatories($section, Document $doc): void
    {
        // $doc->logs isn't limited to System Procedures, and ids overlap with the other document types.
        $logs = $doc->logs()->where('document_type', 'system_procedure')->get();
        $sectionRoles = $doc->section;

        $columns = [
            [
                'heading' => 'Prepared By:',
                'user' => $sectionRoles?->processOwner,
                'position' => 'Process Owner',
                'log' => $logs->firstWhere('action', 'submitted for review'),
            ],
            [
                'heading' => 'Reviewed By:',
                'user' => $sectionRoles?->reviewer,
                'position' => $sectionRoles?->reviewer?->position ?? 'Reviewer',
                'log' => $logs->firstWhere('action', 'review passed'),
            ],
            [
                'heading' => 'Approved By:',
                'user' => $sectionRoles?->approver,
                'position' => $sectionRoles?->approver?->position ?? 'Approver',
                'log' => $logs->firstWhere('action', 'approved'),
            ],
        ];

        $third = (int) ($this->contentWidth / 3);
        $widths = [$third, $third, $this->contentWidth - 2 * $third];

        $section->addTextBreak(1, ['size' => 6]);
        $table = $section->addTable($this->tableStyle($widths) + self::BORDER);
        $font = ['size' => 10];
        $keep = ['keepNext' => true];

        $table->addRow(null, ['cantSplit' => true]);
        foreach ($columns as $i => $column) {
            $table->addCell($widths[$i])->addText($column['heading'], $font, $keep);
        }

        $table->addRow(1000, ['cantSplit' => true]);
        foreach ($columns as $i => $column) {
            $cell = $table->addCell($widths[$i], ['valign' => 'bottom']);

            if ($column['log'] && $column['user']?->signature_path) {
                $cell->addText('(e-signed)', $font + ['italic' => true], $keep);
            }

            $cell->addText($this->clean($column['user']?->fullname() ?? 'Not set'), $font + ['bold' => true], $keep);
            $cell->addText($this->clean($column['position']), $font, $keep);
        }

        $table->addRow(null, ['cantSplit' => true]);
        foreach ($columns as $i => $column) {
            $table->addCell($widths[$i])->addText('Date: '.$this->signatoryDate($doc, $column['log']), $font);
        }
    }

    private function signatoryDate(Document $doc, $log): string
    {
        if ($log) {
            return $log->performed_at->format('F j, Y');
        }

        return $doc->status === 'Active' && $doc->effective_date
            ? date('m/d/Y', strtotime($doc->effective_date))
            : '-';
    }

    /**
     * Objectives, scopes and notes are Quill HTML. PhpWord's own HTML reader needs well-formed XML
     * and doesn't know Quill's list markup (data-list / ql-indent-N), so this walks the DOM itself.
     */
    private function addHtml($container, ?string $html): void
    {
        $dom = new DOMDocument;
        $dom->loadHTML(
            '<html><head><meta charset="utf-8"></head><body>'.$html.'</body></html>',
            LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
        );

        $this->addBlocks($container, $dom->getElementsByTagName('body')->item(0));
    }

    private function addBlocks($container, DOMNode $parent, int $depth = 0): void
    {
        $paragraph = ['alignment' => Jc::BOTH];
        $run = null;

        foreach ($parent->childNodes as $node) {
            if ($this->isList($node)) {
                $run = null;
                $this->addList($container, $node, $depth);
            } elseif ($this->isBlock($node)) {
                $run = null;
                $this->addBlocks($container, $node, $depth);
            } elseif ($this->hasContent($node)) {
                // Loose inline content between blocks shares one paragraph.
                $run ??= $container->addTextRun($paragraph);
                $this->addInline($run, $node);
            }
        }

        // e.g. Quill's empty line, <p><br></p>, or an empty cell that still needs its paragraph.
        if (! $parent->hasChildNodes() && $parent->nodeName !== 'body') {
            $container->addTextRun($paragraph);
        }
    }

    private function addList($container, DOMElement $list, int $depth, array &$styles = []): void
    {
        foreach ($list->childNodes as $item) {
            if (! $item instanceof DOMElement || $item->nodeName !== 'li') {
                if ($this->isList($item)) {
                    $this->addList($container, $item, $depth + 1, $styles);
                }

                continue;
            }

            // Quill always wraps items in <ol> and flags the real type on each <li>.
            $ordered = match ($item->getAttribute('data-list')) {
                'ordered' => true,
                'bullet' => false,
                default => $list->nodeName === 'ol',
            };
            $styles[$ordered ? 'ordered' : 'bullet'] ??= $this->listStyle($ordered);

            preg_match('/ql-indent-(\d+)/', $item->getAttribute('class'), $indent);
            $level = min(8, $depth + (int) ($indent[1] ?? 0));

            $run = $container->addListItemRun($level, $styles[$ordered ? 'ordered' : 'bullet'], ['alignment' => Jc::BOTH]);

            foreach ($item->childNodes as $child) {
                if ($this->isList($child)) {
                    $this->addList($container, $child, $level + 1, $styles);
                } elseif ($this->isBlock($child)) {
                    foreach ($child->childNodes as $grandchild) {
                        $this->addInline($run, $grandchild);
                    }
                } else {
                    $this->addInline($run, $child);
                }
            }
        }
    }

    private function addInline($run, DOMNode $node, array $font = []): void
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $text = preg_replace('/\s+/u', ' ', $this->clean($node->nodeValue));

            if ($text !== '') {
                $run->addText($text, $font);
            }

            return;
        }

        if (! $node instanceof DOMElement || str_contains($node->getAttribute('class'), 'ql-ui')) {
            return;
        }

        if ($node->nodeName === 'br') {
            $run->addTextBreak();

            return;
        }

        $font += match ($node->nodeName) {
            'strong', 'b' => ['bold' => true],
            'em', 'i' => ['italic' => true],
            'u' => ['underline' => 'single'],
            's', 'strike', 'del' => ['strikethrough' => true],
            'sup' => ['superScript' => true],
            'sub' => ['subScript' => true],
            default => [],
        };

        foreach ($node->childNodes as $child) {
            $this->addInline($run, $child, $font);
        }
    }

    private function isList(DOMNode $node): bool
    {
        return $node instanceof DOMElement && in_array($node->nodeName, ['ol', 'ul'], true);
    }

    private function isBlock(DOMNode $node): bool
    {
        return $node instanceof DOMElement
            && in_array($node->nodeName, ['p', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre'], true);
    }

    private function hasContent(DOMNode $node): bool
    {
        return $node->nodeType !== XML_TEXT_NODE || trim($node->nodeValue) !== '';
    }

    /**
     * Each list gets its own numbering style, otherwise Word carries the numbers on from the
     * previous list in the document instead of restarting at 1.
     */
    private function listStyle(bool $ordered): string
    {
        $name = 'list'.(++$this->listCount);
        $formats = [NumberFormat::DECIMAL, NumberFormat::LOWER_LETTER, NumberFormat::LOWER_ROMAN];
        $levels = [];

        for ($level = 0; $level < 9; $level++) {
            $levels[] = [
                'format' => $ordered ? $formats[$level % 3] : NumberFormat::BULLET,
                'text' => $ordered ? '%'.($level + 1).'.' : '•',
                'left' => 360 * ($level + 1),
                'hanging' => 360,
                'tabPos' => 360 * ($level + 1),
            ];
        }

        $this->word->addNumberingStyle($name, ['type' => 'multilevel', 'levels' => $levels]);

        return $name;
    }

    private function tableStyle(array $columnWidths): array
    {
        return [
            'alignment' => JcTable::CENTER,
            'layout' => TableStyle::LAYOUT_FIXED,
            'width' => array_sum($columnWidths),
            'cellMarginLeft' => 80,
            'cellMarginRight' => 80,
        ];
    }

    /**
     * Returns [background, foreground] without the "#" PhpWord doesn't accept. Companies that never
     * set a colour get no shading.
     */
    private function brandColors(?string $hex): array
    {
        $hex = ltrim((string) $hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return [null, '000000'];
        }

        $brightness = (hexdec(substr($hex, 0, 2)) * 299 + hexdec(substr($hex, 2, 2)) * 587 + hexdec(substr($hex, 4, 2)) * 114) / 1000;

        return [$hex, $brightness < 128 ? 'FFFFFF' : '000000'];
    }

    /**
     * Drops characters that aren't legal in XML (stray control characters from pasted text), which
     * would otherwise leave Word unable to open the file.
     */
    private function clean(?string $text): string
    {
        return preg_replace(
            '/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u',
            '',
            mb_scrub((string) $text, 'UTF-8')
        ) ?? '';
    }

    private function storageFilePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return app()->environment('production')
            ? Storage::disk('public')->path($path)
            : public_path('storage/'.$path);
    }
}
