<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Document;
use App\Models\Section;
use App\Models\SupportDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupportDocumentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $reviewer;

    private User $approver;

    private User $controller;

    private Section $section;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $company = Company::factory()->create();
        $this->owner = User::factory()->create(['company_id' => $company->id]);
        $this->reviewer = User::factory()->create(['company_id' => $company->id]);
        $this->approver = User::factory()->create(['company_id' => $company->id]);
        $this->controller = User::factory()->role('Document Controller')->create(['company_id' => $company->id]);

        $this->section = Section::factory()->create([
            'company_id' => $company->id,
            'process_owner_id' => $this->owner->id,
            'reviewer_id' => $this->reviewer->id,
            'approver_id' => $this->approver->id,
        ]);
    }

    private function makeDoc(array $attributes = []): SupportDocument
    {
        $this->actingAs($this->owner);

        return SupportDocument::create(array_merge([
            'title' => 'Supplier Accreditation Guide',
            'code' => 'WI-PUR-01',
            'section_id' => $this->section->id,
            'pages' => 1,
            'objective' => '<p>Objective</p>',
            'scope' => '<p>Scope</p>',
            'status' => 'Draft',
            'justification' => 'Initial issue',
            'file_path' => 'manuals/original.pdf',
        ], $attributes));
    }

    private function updatePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Supplier Accreditation Guide',
            'section_id' => $this->section->id,
            'code' => 'WI-PUR-01',
            'pages' => 1,
            'objective' => 'Objective',
            'scope' => 'Scope',
            'justification' => 'Clarified accreditation steps',
        ], $overrides);
    }

    public function test_section_listing_returns_real_workflow_urls(): void
    {
        $doc = $this->makeDoc();

        $item = $this->actingAs($this->owner)
            ->getJson('/section/sp/documents?sectionId='.$this->section->id)
            ->assertOk()
            ->json('items.0');

        $this->assertSame(route('document.support_document.forReview', $doc->id), $item['sendForReviewUrl']);
        $this->assertSame(route('document.support_document.destroy', $doc->id), $item['deleteUrl']);
        $this->assertTrue($item['can']['send']);
        $this->assertArrayHasKey('setCode', $item['can']);
        $this->assertNotEmpty($item['assignCodeUrl']);
    }

    public function test_only_the_process_owner_can_send_for_review(): void
    {
        $doc = $this->makeDoc();

        $this->actingAs($this->reviewer)
            ->put(route('document.support_document.forReview', $doc))
            ->assertForbidden();
        $this->assertSame('Draft', $doc->fresh()->status);

        $this->actingAs($this->owner)
            ->put(route('document.support_document.forReview', $doc))
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $this->assertSame('For Review', $doc->fresh()->status);
        $this->assertTrue(ActivityLog::where('document_type', 'support_document')
            ->where('document_id', $doc->id)
            ->where('action', 'submitted for review')
            ->exists());
    }

    public function test_full_workflow_from_review_to_active(): void
    {
        $doc = $this->makeDoc(['status' => 'For Review']);

        $this->actingAs($this->owner)
            ->put(route('document.support_document.reviewPassOrFail', $doc), ['decision' => 'pass'])
            ->assertForbidden();

        $this->actingAs($this->reviewer)
            ->put(route('document.support_document.reviewPassOrFail', $doc), ['decision' => 'pass'])
            ->assertRedirect();
        $this->assertSame('For Approval', $doc->fresh()->status);

        $this->actingAs($this->reviewer)
            ->put(route('document.support_document.approveOrNot', $doc), ['decision' => 'pass'])
            ->assertForbidden();

        $this->actingAs($this->approver)
            ->put(route('document.support_document.approveOrNot', $doc), ['decision' => 'pass'])
            ->assertRedirect();
        $doc->refresh();
        $this->assertSame('Pending Code', $doc->status);
        $this->assertSame('0', (string) $doc->revision_number);

        $this->actingAs($this->owner)
            ->put(route('document.support_document.assignCode', $doc), ['document_code' => 'WI-PUR-001', 'revision_number' => '0'])
            ->assertForbidden();

        $this->actingAs($this->controller)
            ->put(route('document.support_document.assignCode', $doc), ['document_code' => 'WI-PUR-001', 'revision_number' => '0'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $doc->refresh();
        $this->assertSame('Active', $doc->status);
        $this->assertSame('WI-PUR-001', $doc->code);
    }

    public function test_failed_review_can_be_edited_and_resent(): void
    {
        $doc = $this->makeDoc(['status' => 'For Review']);

        $this->actingAs($this->reviewer)
            ->put(route('document.support_document.reviewPassOrFail', $doc), ['decision' => 'fail']);
        $this->assertSame('Review not Passed', $doc->fresh()->status);

        $this->actingAs($this->owner)
            ->put(route('document.support_document.update', $doc), $this->updatePayload(['title' => 'Supplier Accreditation Guide v2']))
            ->assertSessionHasNoErrors();

        $doc->refresh();
        $this->assertSame('Draft', $doc->status);
        $this->assertSame('Supplier Accreditation Guide v2', $doc->title);
        $this->assertSame('manuals/original.pdf', $doc->file_path);

        $this->actingAs($this->owner)
            ->put(route('document.support_document.forReview', $doc))
            ->assertSessionHasNoErrors();
        $this->assertSame('For Review', $doc->fresh()->status);
    }

    public function test_documents_under_review_cannot_be_edited(): void
    {
        $doc = $this->makeDoc(['status' => 'For Approval']);

        $this->actingAs($this->owner)
            ->put(route('document.support_document.update', $doc), $this->updatePayload(['title' => 'Changed']))
            ->assertSessionHasErrors('error');
        $this->assertSame('Supplier Accreditation Guide', $doc->fresh()->title);
        $this->assertSame('For Approval', $doc->fresh()->status);
    }

    public function test_editing_an_active_document_creates_a_revision_that_supersedes_it_on_approval(): void
    {
        $active = $this->makeDoc(['status' => 'Active', 'revision_number' => '0']);

        $this->actingAs($this->owner)
            ->put(route('document.support_document.update', $active), $this->updatePayload([
                'code' => null,
                'file' => UploadedFile::fake()->create('rev1.pdf', 50, 'application/pdf'),
            ]))
            ->assertSessionHasNoErrors();

        $active->refresh();
        $this->assertSame('Active', $active->status);
        $this->assertSame('0', (string) $active->revision_number);
        $revision = SupportDocument::where('code', 'WI-PUR-01')->where('id', '!=', $active->id)->sole();
        $this->assertSame('Draft', $revision->status);
        $this->assertNotSame($active->file_path, $revision->file_path);

        // A second edit of the Active document is refused while the revision is in progress.
        $this->actingAs($this->owner)
            ->put(route('document.support_document.update', $active), $this->updatePayload())
            ->assertSessionHasErrors('error');

        $revision->update(['status' => 'For Approval']);
        $this->actingAs($this->approver)
            ->put(route('document.support_document.approveOrNot', $revision), ['decision' => 'pass']);

        $this->assertSame('Superseded', $active->fresh()->status);
        $this->assertSame('1', (string) $revision->fresh()->revision_number);
    }

    public function test_archiving_soft_deletes_only_the_support_document(): void
    {
        $doc = $this->makeDoc();
        // Same id as the support document: the old list modal posted to the System Procedures route.
        $procedure = Document::create([
            'id' => $doc->id,
            'type' => 'System Procedure',
            'title' => 'Supplier Evaluation',
            'code' => 'SP-PUR-01',
            'section_id' => $this->section->id,
            'revision_number' => '0',
            'objective' => 'Objective',
            'scope' => 'Scope',
            'justification' => 'Initial issue',
            'status' => 'Active',
        ]);

        $this->actingAs($this->owner)
            ->delete(route('document.support_document.destroy', $doc), ['delete_justification' => 'short'])
            ->assertSessionHasErrors('delete_justification');
        $this->assertNotSoftDeleted($doc);

        $this->actingAs($this->owner)
            ->from(route('document.support_document.view', $doc))
            ->delete(route('document.support_document.destroy', $doc), ['delete_justification' => 'Merged into WI-PUR-02'])
            ->assertRedirect(route('document.support_document.index'));

        $this->assertSoftDeleted($doc);
        $this->assertSame('Archived', SupportDocument::withTrashed()->find($doc->id)->status);
        $this->assertNotSoftDeleted($procedure);
        $this->assertSame('Active', $procedure->fresh()->status);
    }

    public function test_only_the_owner_or_document_controller_can_archive(): void
    {
        $doc = $this->makeDoc();

        $this->actingAs($this->reviewer)
            ->delete(route('document.support_document.destroy', $doc), ['delete_justification' => 'Merged into WI-PUR-02'])
            ->assertForbidden();
        $this->assertNotSoftDeleted($doc);
    }

    public function test_revision_history_is_limited_to_document_controllers(): void
    {
        $doc = $this->makeDoc();

        $this->actingAs($this->owner)
            ->get(route('document.support_document.rev_history', $doc->code))
            ->assertForbidden();

        $this->actingAs($this->controller)
            ->get(route('document.support_document.rev_history', $doc->code))
            ->assertOk()
            ->assertSee('WI-PUR-01');
    }

    public function test_index_and_edit_pages_render(): void
    {
        $doc = $this->makeDoc();

        $this->actingAs($this->owner)->get(route('document.support_document.index'))
            ->assertOk()
            ->assertSee('assignCodeModal')
            ->assertSee('deleteModal');

        $this->actingAs($this->owner)->get(route('document.support_document.edit', $doc))
            ->assertOk()
            ->assertSee(route('document.support_document.update', $doc), false)
            ->assertSee('<option value="'.$this->section->id.'" selected', false);
    }

    public function test_view_page_shows_each_role_only_its_actions(): void
    {
        $doc = $this->makeDoc(['status' => 'For Review']);
        $decide = route('document.support_document.reviewPassOrFail', $doc);

        $this->actingAs($this->reviewer)->get(route('document.support_document.view', $doc))
            ->assertOk()
            ->assertSee($decide, false)
            ->assertSee('Pass Review')
            ->assertSee('Fail Review');

        $this->actingAs($this->owner)->get(route('document.support_document.view', $doc))
            ->assertOk()
            ->assertDontSee($decide, false)
            ->assertDontSee(route('document.support_document.edit', $doc), false);

        $doc->update(['status' => 'Draft']);

        $this->actingAs($this->owner)->get(route('document.support_document.view', $doc))
            ->assertOk()
            ->assertSee(route('document.support_document.forReview', $doc), false)
            ->assertSee(route('document.support_document.edit', $doc), false)
            ->assertSee(route('document.support_document.destroy', $doc), false);
    }
}
