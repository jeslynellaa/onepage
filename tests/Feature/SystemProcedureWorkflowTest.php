<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Document;
use App\Models\ProcedureComments;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemProcedureWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $reviewer;

    private User $approver;

    private User $controller;

    private Section $section;

    private Section $otherSection;

    protected function setUp(): void
    {
        parent::setUp();

        $company = Company::factory()->create();
        $this->owner = User::factory()->create(['company_id' => $company->id]);
        $this->reviewer = User::factory()->create(['company_id' => $company->id]);
        $this->approver = User::factory()->create(['company_id' => $company->id]);
        $this->controller = User::factory()->role('Document Controller')->create(['company_id' => $company->id]);
        $otherOwner = User::factory()->create(['company_id' => $company->id]);

        $this->section = Section::factory()->create([
            'company_id' => $company->id,
            'title' => 'Purchasing Process',
            'process_owner_id' => $this->owner->id,
            'reviewer_id' => $this->reviewer->id,
            'approver_id' => $this->approver->id,
        ]);
        $this->otherSection = Section::factory()->create([
            'company_id' => $company->id,
            'title' => 'Warehousing Process',
            'process_owner_id' => $otherOwner->id,
            'reviewer_id' => $this->reviewer->id,
            'approver_id' => $this->approver->id,
        ]);
    }

    private function makeDoc(string $status): Document
    {
        $this->actingAs($this->owner);

        return Document::create([
            'type' => 'System Procedure',
            'title' => 'Supplier Evaluation',
            'code' => 'SP-PUR-01',
            'section_id' => $this->section->id,
            'revision_number' => '0',
            'objective' => 'Objective',
            'scope' => 'Scope',
            'justification' => 'Initial issue',
            'status' => $status,
        ]);
    }

    private function storePayload(Section $section): array
    {
        return [
            'title' => 'Receiving',
            'code' => 'SP-NEW-01',
            'section_id' => $section->id,
            'revision_number' => '0',
            'objective' => 'Objective',
            'justification' => 'Initial issue',
            'scope' => 'Scope',
            'type' => 'System Procedure',
            'procedure_steps_json' => json_encode([['responsibility' => 'Clerk', 'activities' => 'Receive goods']]),
        ];
    }

    public function test_process_owner_can_only_create_procedures_for_their_own_sections(): void
    {
        $this->actingAs($this->owner)->get(route('document.system_procedures.create'))
            ->assertOk()
            ->assertSee('Purchasing Process')
            ->assertDontSee('Warehousing Process');

        $this->post(route('document.system_procedures.store'), $this->storePayload($this->otherSection))
            ->assertSessionHasErrors('section_id');
        $this->assertDatabaseMissing('documents', ['code' => 'SP-NEW-01']);

        $this->post(route('document.system_procedures.store'), $this->storePayload($this->section))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('documents', ['code' => 'SP-NEW-01', 'section_id' => $this->section->id]);
    }

    public function test_document_controller_can_create_for_any_section(): void
    {
        // Posts rather than rendering the create view: it declares a global selected() function
        // inline, so it can only be rendered once per PHP process.
        $this->actingAs($this->controller)
            ->post(route('document.system_procedures.store'), $this->storePayload($this->otherSection))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('documents', ['code' => 'SP-NEW-01', 'section_id' => $this->otherSection->id]);
    }

    public function test_user_who_owns_no_section_cannot_create(): void
    {
        $this->actingAs($this->reviewer)->get(route('document.system_procedures'))
            ->assertOk()
            ->assertViewHas('canCreate', false);

        $this->get(route('document.system_procedures.create'))
            ->assertRedirect(route('document.system_procedures'))
            ->assertSessionHasErrors('error');
    }

    public function test_only_the_process_owner_can_send_a_draft_for_review(): void
    {
        $doc = $this->makeDoc('Draft');

        foreach ([$this->reviewer, $this->approver] as $user) {
            $item = $this->actingAs($user)
                ->getJson('/section/documents?sectionId='.$this->section->id)
                ->json('items.0');
            $this->assertFalse($item['can']['send']);
            $this->assertFalse($item['can']['edit']);

            $this->put(route('document.system_procedures.forReview', $doc))->assertForbidden();
        }
        $this->assertSame('Draft', $doc->fresh()->status);

        $this->actingAs($this->owner)->put(route('document.system_procedures.forReview', $doc));
        $this->assertSame('For Review', $doc->fresh()->status);
    }

    public function test_reviewer_send_back_comments_reach_the_process_owner(): void
    {
        $doc = $this->makeDoc('For Review');

        // Submitted from the comment form, which closes once the document is sent back.
        $this->actingAs($this->reviewer)
            ->from(route('document.system_procedures.showComment', $doc))
            ->followingRedirects()
            ->post(route('document.system_procedures.storeComment', $doc), [
                'scope_comment' => 'Clarify which suppliers are covered.',
                'action' => 'send_back',
            ])
            ->assertOk()
            ->assertSee('Comments saved and document sent back.');

        $this->assertSame('For Revision', $doc->fresh()->status);
        $this->assertSame('review', ProcedureComments::where('document_id', $doc->id)->value('stage'));
        $this->assertStringContainsString('sent back by Reviewer', ActivityLog::where('document_id', $doc->id)->latest('id')->value('description'));

        $this->actingAs($this->owner)->get(route('document.system_procedures.view_pdf', $doc))
            ->assertOk()
            ->assertSee('Clarify which suppliers are covered.');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('draftCount', 1)
            ->assertSee('Returned for Revision')
            ->assertSee(route('document.system_procedures.view_pdf', $doc), false);

        $this->put(route('document.system_procedures.forReview', $doc));
        $this->assertSame('For Review', $doc->fresh()->status);
    }

    public function test_approver_send_back_comments_reach_the_process_owner(): void
    {
        $doc = $this->makeDoc('For Approval');

        $this->actingAs($this->approver)->post(route('document.system_procedures.storeComment', $doc), [
            'notes_comment' => 'Add the approval limit to step 3.',
            'action' => 'send_back',
        ])->assertSessionHasNoErrors();

        $this->assertSame('For Revision', $doc->fresh()->status);
        $this->assertSame('approval', ProcedureComments::where('document_id', $doc->id)->value('stage'));
        $this->assertStringContainsString('sent back by Approver', ActivityLog::where('document_id', $doc->id)->latest('id')->value('description'));

        $this->actingAs($this->owner)->get(route('document.system_procedures.view_pdf', $doc))
            ->assertOk()
            ->assertSee('Add the approval limit to step 3.');

        $this->get(route('dashboard'))->assertViewHas('draftCount', 1);
    }

    public function test_only_the_assigned_reviewer_can_comment(): void
    {
        $doc = $this->makeDoc('For Review');

        $this->actingAs($this->approver)->post(route('document.system_procedures.storeComment', $doc), [
            'scope_comment' => 'Not my stage.',
            'action' => 'send_back',
        ])->assertForbidden();

        $this->assertSame('For Review', $doc->fresh()->status);
    }

    public function test_failing_review_requires_a_reason_and_is_final(): void
    {
        $doc = $this->makeDoc('For Review');

        $this->actingAs($this->reviewer)
            ->put(route('document.system_procedures.reviewPassOrFail', $doc), ['decision' => 'fail'])
            ->assertSessionHasErrors('reason');
        $this->assertSame('For Review', $doc->fresh()->status);

        $this->put(route('document.system_procedures.reviewPassOrFail', $doc), [
            'decision' => 'fail',
            'reason' => 'Duplicates SP-PUR-02.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Review not Passed', $doc->fresh()->status);
        $this->assertDatabaseHas('procedure_comments', [
            'document_id' => $doc->id,
            'section' => 'rejection',
            'stage' => 'review',
            'comment' => 'Duplicates SP-PUR-02.',
        ]);

        $this->actingAs($this->owner)->get(route('document.system_procedures.view_pdf', $doc))
            ->assertSee('Reason for Rejection')
            ->assertSee('Duplicates SP-PUR-02.');

        $this->put(route('document.system_procedures.forReview', $doc))->assertSessionHasErrors('error');
        $this->get(route('document.system_procedures.edit', $doc))->assertForbidden();
        $this->get(route('dashboard'))->assertViewHas('draftCount', 0);
    }

    public function test_not_approving_requires_a_reason_and_is_final(): void
    {
        $doc = $this->makeDoc('For Approval');

        $this->actingAs($this->approver)
            ->put(route('document.system_procedures.approveOrNot', $doc), ['decision' => 'fail'])
            ->assertSessionHasErrors('reason');

        $this->put(route('document.system_procedures.approveOrNot', $doc), [
            'decision' => 'fail',
            'reason' => 'Out of scope for this year.',
        ])->assertSessionHasNoErrors();

        $this->assertSame('Not Approved', $doc->fresh()->status);
        $this->assertDatabaseHas('procedure_comments', [
            'document_id' => $doc->id,
            'section' => 'rejection',
            'stage' => 'approval',
        ]);

        $this->actingAs($this->owner)->get(route('document.system_procedures.edit', $doc))->assertForbidden();
    }

    public function test_backfill_assigns_a_stage_to_existing_comments(): void
    {
        $doc = $this->makeDoc('For Revision');
        foreach ([$this->reviewer, $this->approver] as $user) {
            \DB::table('procedure_comments')->insert([
                'document_id' => $doc->id,
                'user_id' => $user->id,
                'company_id' => $doc->company_id,
                'section' => 'scope',
                'comment' => 'Old comment',
                'stage' => null,
            ]);
        }

        (require database_path('migrations/2026_10_30_090000_backfill_stage_on_procedure_comments.php'))->up();

        $this->assertSame('review', \DB::table('procedure_comments')->where('user_id', $this->reviewer->id)->value('stage'));
        $this->assertSame('approval', \DB::table('procedure_comments')->where('user_id', $this->approver->id)->value('stage'));
    }

    public function test_reviewer_cannot_approve_and_approver_cannot_review(): void
    {
        $doc = $this->makeDoc('For Review');
        $this->actingAs($this->approver)
            ->put(route('document.system_procedures.reviewPassOrFail', $doc), ['decision' => 'pass'])
            ->assertForbidden();

        $doc->update(['status' => 'For Approval']);
        $this->actingAs($this->reviewer)
            ->put(route('document.system_procedures.approveOrNot', $doc), ['decision' => 'pass'])
            ->assertForbidden();
    }
}
