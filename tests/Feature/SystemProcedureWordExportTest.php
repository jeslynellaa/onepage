<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\ClientUser;
use App\Models\Company;
use App\Models\DefinitionOfTerm;
use App\Models\Document;
use App\Models\ProcedureSteps;
use App\Models\Section;
use App\Models\StepDocuments;
use App\Models\User;
use App\Services\DocumentWordService;
use DOMDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpWord\IOFactory;
use Tests\TestCase;
use ZipArchive;

class SystemProcedureWordExportTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    private User $owner;

    private User $approver;

    private User $controller;

    private Document $doc;

    protected function setUp(): void
    {
        parent::setUp();

        Company::factory()->create(['id' => 1]); // the host company
        $this->company = Company::factory()->create(['name' => 'Acme & Sons', 'hex_code' => '#1e72b8']);
        $this->owner = User::factory()->create([
            'company_id' => $this->company->id,
            'first_name' => 'Olivia',
            'last_name' => 'Owner',
            'signature_path' => 'signatures/owner.png',
        ]);
        $this->approver = User::factory()->create([
            'company_id' => $this->company->id,
            'first_name' => 'Avery',
            'last_name' => 'Approver',
            'signature_path' => 'signatures/approver.png',
        ]);
        $this->controller = User::factory()->role('Document Controller')->create(['company_id' => $this->company->id]);

        $section = Section::factory()->create([
            'company_id' => $this->company->id,
            'process_owner_id' => $this->owner->id,
            'reviewer_id' => null,
            'approver_id' => $this->approver->id,
        ]);

        $this->actingAs($this->owner);

        $this->doc = Document::create([
            'type' => 'System Procedure',
            'title' => 'Purchasing & Receiving',
            'code' => 'SP-PUR-01',
            'section_id' => $section->id,
            'revision_number' => '2',
            'objective' => '<ol><li>To buy <strong>approved</strong> goods.</li><li>To pay on time.</li></ol>',
            'scope' => '<p>All purchases &amp; receipts.</p>',
            'status' => 'Active',
        ]);
    }

    /** @return array{document: string, header: string, zip: list<string>} */
    private function export(): array
    {
        $path = app(DocumentWordService::class)->generate($this->doc->fresh());

        // Reading it back proves it's a structurally valid .docx, not just a zip with the right name.
        IOFactory::load($path);

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($path));

        $names = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $names[] = $zip->getNameIndex($i);
        }

        foreach ($names as $name) {
            if (str_ends_with($name, '.xml')) {
                $this->assertTrue((new DOMDocument)->loadXML($zip->getFromName($name)), "$name is not well-formed");
            }
        }

        $result = [
            'document' => $zip->getFromName('word/document.xml'),
            'header' => $zip->getFromName('word/header1.xml'),
            'zip' => $names,
        ];

        $zip->close();
        unlink($path);

        return $result;
    }

    private function addStep(string $activity, ?string $note = null): ProcedureSteps
    {
        return ProcedureSteps::create([
            'document_id' => $this->doc->id,
            'responsibility' => 'Purchasing Officer',
            'activities' => $activity,
            'note' => $note,
        ]);
    }

    public function test_document_controller_downloads_a_docx(): void
    {
        $this->actingAs($this->controller)
            ->get(route('document.system_procedures.download_word', $this->doc))
            ->assertOk()
            ->assertDownload('Purchasing  Receiving.docx')
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    }

    public function test_regular_staff_cannot_download_and_dont_see_the_button(): void
    {
        $staff = User::factory()->create(['company_id' => $this->company->id]);

        $this->actingAs($staff)->get(route('document.system_procedures.download_word', $this->doc))->assertForbidden();

        $this->actingAs($staff)->get(route('document.system_procedures.view_pdf', $this->doc))
            ->assertOk()
            ->assertDontSee('Download Word');

        $this->actingAs($this->controller)->get(route('document.system_procedures.view_pdf', $this->doc))
            ->assertOk()
            ->assertSee('Download Word');
    }

    public function test_another_companys_admin_cannot_reach_the_document(): void
    {
        $other = Company::factory()->create();
        $admin = User::factory()->role('Admin')->create(['company_id' => $other->id]);

        $this->actingAs($admin)->get(route('document.system_procedures.download_word', $this->doc))->assertNotFound();
    }

    public function test_only_a_consultant_with_a_live_assignment_can_export(): void
    {
        $consultant = User::factory()->create(['company_id' => 1]);

        $this->assertFalse($consultant->can('exportWord', $this->doc));

        ClientUser::create(['user_id' => $consultant->id, 'company_id' => $this->company->id, 'status' => 'active']);

        $this->assertTrue($consultant->can('exportWord', $this->doc));
    }

    public function test_content_matches_the_procedure_and_special_characters_are_escaped(): void
    {
        DefinitionOfTerm::create(['document_id' => $this->doc->id, 'term' => 'PO', 'definition' => 'Purchase Order']);
        $step = $this->addStep('Raise <PO> & send', '<p>Check the <em>budget</em> first.</p>');
        StepDocuments::create(['procedure_step_id' => $step->id, 'type' => 'output', 'category' => 'Form', 'title' => 'PO Form']);
        StepDocuments::create(['procedure_step_id' => $step->id, 'type' => 'input', 'category' => 'Guide', 'title' => 'Buying Guide']);

        $xml = $this->export()['document'];

        foreach ([
            'To buy ', 'approved', 'All purchases &amp; receipts.', 'PO', 'Purchase Order',
            'Raise &lt;PO&gt; &amp; send', 'Note 1', 'budget', 'Form: PO Form', 'Guide: Buying Guide',
        ] as $expected) {
            $this->assertStringContainsString($expected, $xml);
        }
    }

    public function test_quill_lists_keep_their_type_and_indent_and_each_list_restarts(): void
    {
        $this->addStep('Step one', '<ol>'
            .'<li data-list="ordered"><span class="ql-ui" contenteditable="false"></span>First</li>'
            .'<li data-list="ordered" class="ql-indent-1"><span class="ql-ui" contenteditable="false"></span>Nested</li>'
            .'<li data-list="bullet"><span class="ql-ui" contenteditable="false"></span>Dot</li>'
            .'</ol>');

        $export = $this->export();
        $xml = new DOMDocument;
        $xml->loadXML($export['document']);
        $xpath = new \DOMXPath($xml);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $paragraph = fn (string $text) => $xpath->query("//w:p[.//w:t[text()='$text']]")->item(0);
        $level = fn (string $text) => $xpath->query('.//w:numPr/w:ilvl/@w:val', $paragraph($text))->item(0)?->value;
        $numId = fn (string $text) => $xpath->query('.//w:numPr/w:numId/@w:val', $paragraph($text))->item(0)?->value;

        $this->assertSame('0', $level('First'));
        $this->assertSame('1', $level('Nested'));
        $this->assertSame('0', $level('Dot'));
        $this->assertNotSame($numId('First'), $numId('Dot'), 'Bullets share a numbering definition with numbers');

        // The objective's list is a separate list, so it must not carry on from this one.
        $this->assertNotSame($numId('To pay on time.'), $numId('First'));
        $this->assertStringNotContainsString('ql-ui', $export['document']);
    }

    public function test_signatures_are_text_only_and_only_for_those_who_actually_signed(): void
    {
        ActivityLog::create([
            'document_id' => $this->doc->id,
            'document_type' => 'system_procedure',
            'action' => 'submitted for review',
            'description' => 'Submitted',
            'user_id' => $this->owner->id,
            'performed_at' => '2026-03-12 09:00:00',
        ]);

        $export = $this->export();

        $this->assertEmpty(preg_grep('#^word/media/#', $export['zip']), 'A signature image was embedded');
        $this->assertSame(1, substr_count($export['document'], '(e-signed)'));
        $this->assertStringContainsString('Owner, Olivia', $export['document']);
        $this->assertStringContainsString('Approver, Avery', $export['document']);
        $this->assertStringContainsString('Not set', $export['document']);
        $this->assertStringContainsString('March 12, 2026', $export['document']);
    }

    public function test_another_document_types_log_with_the_same_id_is_not_treated_as_a_signature(): void
    {
        ActivityLog::create([
            'document_id' => $this->doc->id,
            'document_type' => 'form',
            'action' => 'approved',
            'description' => 'Approved',
            'user_id' => $this->approver->id,
            'performed_at' => '2026-03-12 09:00:00',
        ]);

        $xml = $this->export()['document'];

        $this->assertStringNotContainsString('(e-signed)', $xml);
        $this->assertStringNotContainsString('March 12, 2026', $xml);
    }

    public function test_handles_a_missing_logo_no_brand_colour_and_empty_fields(): void
    {
        $this->company->update(['logo_path' => 'client_logos/does-not-exist.png', 'hex_code' => null]);
        $this->doc->update(['objective' => '', 'scope' => '']);

        $export = $this->export();

        // The company name stands in for the logo, on top of its mention in the confidentiality line.
        $this->assertStringContainsString('>Acme &amp; Sons<', $export['header']);
        $this->assertEmpty(preg_grep('#^word/media/#', $export['zip']));
        $this->assertStringNotContainsString('w:fill', $export['header']);
    }

    public function test_control_characters_from_pasted_text_dont_corrupt_the_file(): void
    {
        $this->addStep("Pasted\x0B text\x01 here", '<p>Note with stray '."\x0C".'characters</p>');

        $this->assertStringContainsString('Pasted text here', $this->export()['document']);
    }
}
