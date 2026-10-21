<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Form;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FormWorkflowTest extends TestCase
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

    private function makeForm(array $attributes = []): Form
    {
        $this->actingAs($this->owner);

        return Form::create(array_merge([
            'title' => 'Purchase Request Form',
            'code' => 'FRM-PUR-01',
            'section_id' => $this->section->id,
            'pages' => 1,
            'status' => 'Draft',
            'justification' => 'Initial issue',
            'file_path' => 'manuals/original.pdf',
        ], $attributes));
    }

    public function test_section_listing_returns_real_workflow_urls(): void
    {
        $form = $this->makeForm();

        $item = $this->actingAs($this->owner)
            ->getJson('/section/form/documents?sectionId='.$this->section->id)
            ->assertOk()
            ->json('items.0');

        $this->assertSame(route('document.forms.forReview', $form->id), $item['sendForReviewUrl']);
        $this->assertTrue($item['can']['send']);
        foreach (['editUrl', 'deleteUrl', 'revHistoryUrl', 'reviewDecisionUrl', 'approveDecisionUrl', 'assignCodeUrl'] as $key) {
            $this->assertNotEmpty($item[$key], "$key missing");
        }
    }

    public function test_process_owner_can_send_a_draft_form_for_review(): void
    {
        $form = $this->makeForm();

        $this->actingAs($this->owner)
            ->put(route('document.forms.forReview', $form))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('For Review', $form->fresh()->status);
        $this->assertTrue(ActivityLog::where('document_type', 'form')
            ->where('document_id', $form->id)
            ->where('action', 'submitted for review')
            ->exists());
    }

    public function test_only_the_process_owner_can_send_for_review(): void
    {
        $form = $this->makeForm();

        $this->actingAs($this->reviewer)
            ->put(route('document.forms.forReview', $form))
            ->assertForbidden();

        $this->assertSame('Draft', $form->fresh()->status);
    }

    public function test_full_workflow_from_draft_to_active(): void
    {
        $form = $this->makeForm(['status' => 'For Review']);

        $this->actingAs($this->owner)
            ->put(route('document.forms.reviewPassOrFail', $form), ['decision' => 'pass'])
            ->assertForbidden();

        $this->actingAs($this->reviewer)
            ->put(route('document.forms.reviewPassOrFail', $form), ['decision' => 'pass'])
            ->assertRedirect();
        $this->assertSame('For Approval', $form->fresh()->status);

        $this->actingAs($this->approver)
            ->put(route('document.forms.approveOrNot', $form), ['decision' => 'pass'])
            ->assertRedirect();
        $form->refresh();
        $this->assertSame('Pending Code', $form->status);
        $this->assertSame('0', (string) $form->revision_number);

        $this->actingAs($this->controller)
            ->put(route('document.forms.assignCode', $form), ['document_code' => 'FRM-PUR-001', 'revision_number' => '0'])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $form->refresh();
        $this->assertSame('Active', $form->status);
        $this->assertSame('FRM-PUR-001', $form->code);
    }

    public function test_failed_review_can_be_edited_and_resent(): void
    {
        $form = $this->makeForm(['status' => 'For Review']);

        $this->actingAs($this->reviewer)
            ->put(route('document.forms.reviewPassOrFail', $form), ['decision' => 'fail']);
        $this->assertSame('Review not Passed', $form->fresh()->status);

        $this->actingAs($this->owner)
            ->put(route('document.forms.update', $form), [
                'title' => 'Purchase Request Form v2',
                'section_id' => $this->section->id,
                'code' => 'FRM-PUR-01',
                'pages' => 2,
                'justification' => 'Addressed reviewer findings',
            ])
            ->assertSessionHasNoErrors();

        $form->refresh();
        $this->assertSame('Draft', $form->status);
        $this->assertSame('Purchase Request Form v2', $form->title);
        $this->assertSame('manuals/original.pdf', $form->file_path);

        $this->actingAs($this->owner)
            ->put(route('document.forms.forReview', $form))
            ->assertSessionHasNoErrors();
        $this->assertSame('For Review', $form->fresh()->status);
    }

    public function test_editing_an_active_form_creates_a_revision_that_supersedes_it_on_approval(): void
    {
        $active = $this->makeForm(['status' => 'Active', 'revision_number' => '0']);

        $this->actingAs($this->owner)
            ->put(route('document.forms.update', $active), [
                'title' => 'Purchase Request Form',
                'section_id' => $this->section->id,
                'pages' => 1,
                'justification' => 'Added budget line',
                'file' => UploadedFile::fake()->create('rev1.pdf', 50, 'application/pdf'),
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Active', $active->fresh()->status);
        $revision = Form::where('code', 'FRM-PUR-01')->where('id', '!=', $active->id)->sole();
        $this->assertSame('Draft', $revision->status);
        $this->assertNotSame($active->file_path, $revision->file_path);

        // A second edit of the Active form is refused while the revision is in progress.
        $this->actingAs($this->owner)
            ->put(route('document.forms.update', $active), [
                'title' => 'x', 'section_id' => $this->section->id, 'pages' => 1, 'justification' => 'dup',
            ])
            ->assertSessionHasErrors('error');

        $revision->update(['status' => 'For Approval']);
        $this->actingAs($this->approver)
            ->put(route('document.forms.approveOrNot', $revision), ['decision' => 'pass']);

        $this->assertSame('Superseded', $active->fresh()->status);
        $this->assertSame('1', (string) $revision->fresh()->revision_number);
    }

    public function test_archiving_requires_justification_and_soft_deletes(): void
    {
        $form = $this->makeForm();

        $this->actingAs($this->owner)
            ->delete(route('document.forms.destroy', $form), [])
            ->assertSessionHasErrors('delete_justification');
        $this->assertNotSoftDeleted($form);

        $this->actingAs($this->owner)
            ->delete(route('document.forms.destroy', $form), ['delete_justification' => 'Merged into FRM-PUR-02'])
            ->assertSessionHasNoErrors();

        $this->assertSoftDeleted($form);
        $this->assertSame('Archived', Form::withTrashed()->find($form->id)->status);
    }

    public function test_index_and_edit_pages_render(): void
    {
        $form = $this->makeForm();

        $this->actingAs($this->owner)->get(route('document.forms.index'))
            ->assertOk()
            ->assertSee('assignCodeModal')
            ->assertSee('deleteModal');

        $this->actingAs($this->owner)->get(route('document.forms.edit', $form))
            ->assertOk()
            ->assertSee(route('document.forms.update', $form), false);
    }

    public function test_revision_history_is_limited_to_document_controllers(): void
    {
        $form = $this->makeForm();

        $this->actingAs($this->owner)
            ->get(route('document.forms.rev_history', $form->code))
            ->assertForbidden();

        $this->actingAs($this->controller)
            ->get(route('document.forms.rev_history', $form->code))
            ->assertOk()
            ->assertSee('FRM-PUR-01');
    }
}
