<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Document;
use App\Models\Form;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardActionRequestsTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $reviewer;

    private User $approver;

    private Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $company = Company::factory()->create();
        $this->owner = User::factory()->create(['company_id' => $company->id]);
        $this->reviewer = User::factory()->create(['company_id' => $company->id]);
        $this->approver = User::factory()->create(['company_id' => $company->id]);

        $this->section = Section::factory()->create([
            'company_id' => $company->id,
            'process_owner_id' => $this->owner->id,
            'reviewer_id' => $this->reviewer->id,
            'approver_id' => $this->approver->id,
        ]);
    }

    public function test_form_awaiting_approval_appears_in_the_approvers_action_requests(): void
    {
        $this->actingAs($this->owner);
        $form = Form::create([
            'title' => 'Purchase Request Form',
            'code' => 'F-PUR-05',
            'section_id' => $this->section->id,
            'pages' => 1,
            'status' => 'For Approval',
            'justification' => 'Initial issue',
            'file_path' => 'manuals/original.pdf',
        ]);

        $this->actingAs($this->approver)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Needs Your Approval')
            ->assertSee('Purchase Request Form')
            ->assertSee(route('document.forms.view', $form), false);

        $this->actingAs($this->reviewer)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Needs Your Approval')
            ->assertSee('All caught up!');
    }

    public function test_system_procedure_awaiting_review_still_appears_for_its_reviewer(): void
    {
        $this->actingAs($this->owner);
        $doc = Document::create([
            'type' => 'System Procedure',
            'title' => 'Supplier Evaluation',
            'code' => 'SP-PUR-01',
            'section_id' => $this->section->id,
            'revision_number' => '0',
            'objective' => 'Objective',
            'scope' => 'Scope',
            'status' => 'For Review',
        ]);

        $this->actingAs($this->reviewer)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Needs Your Review')
            ->assertSee('Supplier Evaluation')
            ->assertSee(route('document.system_procedures.view_pdf', $doc), false);
    }
}
