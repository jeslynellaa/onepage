<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentPdfService
{
    public function generate($doc)
    {
        $doc->load(['steps.interfaces', 'section.processOwner', 'section.reviewer', 'section.approver', 'definitionOfTerms']);
        $steps = $doc->steps;
        $definitionOfTerms = $doc->definitionOfTerms;

        $submitted = $doc->logs->firstWhere('action', 'submitted for review');
        $passed = $doc->logs->firstWhere('action', 'review passed');
        $approved = $doc->logs->firstWhere('action', 'approved');

        $color = $doc->company->hex_code;
        $text_color = $this->getTextColorForBackground($color);
        $font = $doc->company->pdf_font;
        $size = $doc->company->paper_size;

        // Path Logic

        if (app()->environment('production')) {
            $logo = $doc->company->logo_path
                ? Storage::disk('public')->path($doc->company->logo_path)
                : null;
            $owner_sign = $doc->section->processOwner->signature_path
                ? Storage::disk('public')->path($doc->section->processOwner->signature_path)
                : null;
            $reviewer_sign = $doc->section->reviewer->signature_path
                ? Storage::disk('public')->path($doc->section->reviewer->signature_path)
                : null;
            $approver_sign = $doc->section->approver->signature_path
                ? Storage::disk('public')->path($doc->section->approver->signature_path)
                : null;
            $connector = public_path('img/flowchart-connector.png');
        } else {
            $logo = public_path('storage/' . $doc->company->logo_path);
            $owner_sign = public_path('storage/' . $doc->section->processOwner->signature_path);
            $reviewer_sign = public_path('storage/' . $doc->section->reviewer->signature_path);
            $approver_sign = public_path('storage/' . $doc->section->approver->signature_path);
            $connector = realpath(base_path()).'\public\img\flowchart-connector.png';
        }
        
        // ... Load other signatures similarly ...


        // Flatten all interfaces into one collection
        $allInterfaces = $doc->steps->flatMap(function ($step) {
            return $step->interfaces;
        });

        // Separate and deduplicate by type and title
        $uniqueInputs = $allInterfaces
            ->where('type', 'input')
            ->unique('title')
            ->values();

        $uniqueOutputs = $allInterfaces
            ->where('type', 'output')
            ->unique('title')
            ->values();

        $layout = $this->calculatePageLayout($steps, $definitionOfTerms, $uniqueInputs, $uniqueOutputs, $doc->objective, $doc->scope, $size);
        $stepBreakpoints = $layout['stepBreakpoints'];
        $forceSignatoryBreak = $layout['forceSignatoryBreak'];

        // 1️⃣ Load your Blade view into Dompdf
        $pdf = Pdf::loadView('pdf.system_procedure', compact('doc', 'steps', 'definitionOfTerms', 'stepBreakpoints', 'forceSignatoryBreak', 'uniqueInputs', 'uniqueOutputs', 'connector', 'submitted', 'passed', 'approved', 'owner_sign', 'reviewer_sign', 'approver_sign', 'logo', 'color', 'text_color', 'font'))->setPaper($size, 'portrait');

        // 3. Render and Page Counting
        $pdf->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        
        // Update total pages in DB
        $doc->update(['pages' => $canvas->get_page_count()]);

        // 4. Page Numbering Script
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($font, $size) {
            $selectedFont = $fontMetrics->get_font($font, "normal");
            $x = 455;
            $y = 111;
            if($font == 'Times-Roman'){
                if($size == 'letter'){
                    $x = 467; //ok
                }else if($size == 'a4'){
                    $x = 455; //ok
                }
            }else if($font == 'Helvetica'){
                if($size == 'letter'){
                    $x = 467; //ok
                }else if($size == 'a4'){
                    $x = 455; // ok
                }
            }else if($font == 'Courier'){
                if($size == 'letter'){
                    $x = 467;
                    $y = 120; // ok
                }else if($size == 'a4'){
                    $x = 454;
                    $y = 120; // ok
                }
            }
            $canvas->text($x, $y, "Page $pageNumber of $pageCount", $selectedFont, 11);
        });

        return $pdf;
    }

    /**
     * Decide (a) which step indices should start a new flowchart page, and
     * (b) whether the signatory block needs to be forced onto its own page.
     *
     * Dompdf has no live reflow/measurement API and no "last page" selector,
     * so it can't natively reserve space for the signatory table only on
     * whichever page turns out to be last — that's why the body CSS used to
     * pad every page's bottom by a full signatory-table's worth of space.
     * This simulates the whole document flow (objectives/scope, definition
     * of terms, the flowchart, notes, then the interfaces table) using the
     * same wrapped-line estimation technique throughout, tracking how much
     * room is left on whatever page content naturally ends on. Only if the
     * signatory block wouldn't fit there does it get forced onto a fresh
     * page — see forceSignatoryBreak in pdf/system_procedure.blade.php.
     *
     * This is still a best-effort estimate, not exact layout — a safety
     * margin is applied so a slightly-off estimate under-fills a page rather
     * than overflowing it. If real documents still break wrong, these are
     * the values to re-tune (in order of what usually needs adjusting):
     *   - $activitiesCharsPerLine / $bodyCharsPerLine: wrapping estimate
     *   - $safetyMargin: raise if pages still overflow, lower if too empty
     *   - $signatoryHeightPt: only if the signatory table markup changes
     *   - the reserved-space math: only if the header/footer CSS changes
     */
    private function calculatePageLayout($steps, $definitionOfTerms, $uniqueInputs, $uniqueOutputs, ?string $objectiveHtml, ?string $scopeHtml, string $paperSize): array
    {
        $pageHeightPt = strtolower($paperSize) === 'a4' ? 841.89 : 792.0; // portrait

        // @page margins (top+bottom only matter for height) plus body padding
        // reserved for the fixed header (150px) and a small buffer above the
        // fixed footer (30px) — NOT sized for the signatory block anymore,
        // since forceSignatoryBreak below handles that per-document instead.
        $reservedPt = (75 + 50 + 150 + 30) * 0.75; // = 228.75pt
        $safetyMargin = 0.85; // leave headroom for estimation error
        $usablePerPagePt = ($pageHeightPt - $reservedPt) * $safetyMargin;

        $lineHeightPt = 12;           // 10pt font, ~1.2 line-height in the process table
        $bodyLineHeightPt = 13.2;     // 11pt body font, ~1.2 line-height (objective/scope/notes/interfaces)
        $activitiesPaddingPt = 7.5;   // 5px top + 5px bottom, inline-styled on the activities cell
        $arrowSpacerPt = 31.5;        // arrow-line + arrow-down + margins between same-page steps
        $connectorInPt = 37.5 + $arrowSpacerPt; // connector bubble + spacer arrow atop the next page
        $signatoryHeightPt = 90;      // Prepared/Reviewed/Approved By table (70px signature row + label/date rows)

        $activitiesCharsPerLine = 34; // ~200px column at 10pt font
        $bodyCharsPerLine = 90;       // full-width rows at 11pt body font

        $estimateLines = function (?string $html, int $charsPerLine): int {
            if (empty($html)) {
                return 0;
            }

            $withBreaks = preg_replace('/<\/(p|li|div)>|<br\s*\/?>/i', "\n", $html);
            $plain = trim(strip_tags($withBreaks ?? $html));

            if ($plain === '') {
                return 0;
            }

            $lines = 0;
            foreach (preg_split('/\n+/', $plain) as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $lines += max(1, (int) ceil(mb_strlen($line) / $charsPerLine));
            }

            return $lines;
        };

        // --- Page 1 header content: objectives/scope + definition of terms ---
        $objScopeLines = $estimateLines($objectiveHtml, $bodyCharsPerLine)
            + $estimateLines($scopeHtml, $bodyCharsPerLine);
        $headerHeightPt = $objScopeLines * $bodyLineHeightPt + 30; // + table borders/label row overhead

        if ($definitionOfTerms->isNotEmpty()) {
            $headerHeightPt += 20; // "DEFINITION OF TERMS" title row
            foreach ($definitionOfTerms as $term) {
                $termLines = max(
                    $estimateLines($term->term, 20),      // ~30%-width term column
                    $estimateLines($term->definition, 60) // ~70%-width definition column
                );
                $headerHeightPt += max($termLines, 1) * $bodyLineHeightPt + 10; // + cell padding/border
            }
        }

        // --- Flowchart: estimate-and-fill loop, one page budget at a time ---
        $breakpoints = [];
        $remaining = $usablePerPagePt - $headerHeightPt;
        $stepCount = $steps->count();

        foreach ($steps as $key => $step) {
            $activityLines = max(1, (int) ceil(mb_strlen($step->activities ?? '') / $activitiesCharsPerLine));
            $interfaceLines = $step->interfaces->count();
            $rowHeightPt = max($activityLines, $interfaceLines, 1) * $lineHeightPt + $activitiesPaddingPt;

            $hasNext = ($key + 1) < $stepCount;
            $stepCostPt = $rowHeightPt + ($hasNext ? $arrowSpacerPt : 0);

            if ($key > 0 && $stepCostPt > $remaining) {
                $breakpoints[] = $key;
                $remaining = $usablePerPagePt - $connectorInPt;
            }

            $remaining -= $stepCostPt;
        }

        // --- Keep simulating past the flowchart to see where content actually ends ---
        // Notes render right after the flowchart, one block per step that has one.
        // These flow onto new pages automatically via dompdf, so this just needs
        // to track how much room is left, not decide anything.
        foreach ($steps as $step) {
            if (empty($step->note)) {
                continue;
            }

            $noteHeightPt = (1 + $estimateLines($step->note, $bodyCharsPerLine)) * $bodyLineHeightPt; // "Note N:" title + body

            if ($noteHeightPt > $remaining) {
                $remaining = $usablePerPagePt;
            }

            $remaining -= $noteHeightPt;
        }

        // The "Documented Information Generated / References" table doesn't split
        // (page-break-inside: avoid in the CSS) — it either fits here or moves
        // whole to a fresh page.
        $estimateListHeightPt = function ($items) use ($estimateLines, $bodyLineHeightPt) {
            $lines = 0;
            foreach ($items as $item) {
                $lines += max(1, $estimateLines($item->title ?? '', 55));
            }
            return $lines * $bodyLineHeightPt;
        };
        $interfacesHeightPt = max($estimateListHeightPt($uniqueOutputs), $estimateListHeightPt($uniqueInputs)) + 25; // header row + padding

        if ($interfacesHeightPt > $remaining) {
            $remaining = $usablePerPagePt;
        }

        $remaining -= $interfacesHeightPt;

        // --- Does the signatory block fit where content naturally ends? ---
        $forceSignatoryBreak = $signatoryHeightPt > $remaining;

        return [
            'stepBreakpoints' => $breakpoints,
            'forceSignatoryBreak' => $forceSignatoryBreak,
        ];
    }

    private function getTextColorForBackground($hex) {
        $hex = ltrim($hex, '#');

        // Support shorthand hex (#fff)
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;

        return $brightness < 128 ? '#ffffff' : '#000000';
    }
}