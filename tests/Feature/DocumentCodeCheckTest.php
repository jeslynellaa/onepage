<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Form;
use App\Models\Section;
use App\Models\SupportDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentCodeCheckTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Section $section;

    protected function setUp(): void
    {
        parent::setUp();

        $company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $company->id]);
        $this->section = Section::factory()->create(['company_id' => $company->id]);
    }

    private function makeForm(array $attributes = []): Form
    {
        $this->actingAs($this->user);

        return Form::create(array_merge([
            'title' => 'Risk Register',
            'code' => 'F-BPL-004',
            'section_id' => $this->section->id,
            'pages' => 1,
            'status' => 'Draft',
            'justification' => 'Initial issue',
            'file_path' => 'manuals/original.pdf',
        ], $attributes));
    }

    public function test_an_unused_code_expects_revision_zero(): void
    {
        $this->actingAs($this->user)
            ->getJson(route('document.forms.checkCode', ['code' => 'F-BPL-004']))
            ->assertOk()
            ->assertExactJson([
                'code' => 'F-BPL-004',
                'active' => null,
                'in_progress' => null,
                'expected_revision' => 0,
            ]);
    }

    public function test_an_active_code_is_reported_and_expects_the_next_revision(): void
    {
        $this->makeForm(['status' => 'Superseded', 'revision_number' => '1']);
        $active = $this->makeForm(['status' => 'Active', 'revision_number' => '2']);

        $this->actingAs($this->user)
            ->getJson(route('document.forms.checkCode', ['code' => 'F-BPL-004']))
            ->assertOk()
            ->assertJsonPath('active.id', $active->id)
            ->assertJsonPath('active.revision', '2')
            ->assertJsonPath('active.url', route('document.forms.view', $active->id))
            ->assertJsonPath('in_progress', null)
            ->assertJsonPath('expected_revision', 3);
    }

    public function test_a_draft_is_reported_as_in_progress(): void
    {
        $draft = $this->makeForm(['status' => 'For Review']);

        $this->actingAs($this->user)
            ->getJson(route('document.forms.checkCode', ['code' => 'F-BPL-004']))
            ->assertOk()
            ->assertJsonPath('active', null)
            ->assertJsonPath('in_progress.id', $draft->id)
            ->assertJsonPath('in_progress.status', 'For Review');
    }

    public function test_the_document_being_edited_can_be_excluded(): void
    {
        $this->makeForm(['status' => 'Active', 'revision_number' => '0']);
        $draft = $this->makeForm(['status' => 'Draft']);

        $this->actingAs($this->user)
            ->getJson(route('document.forms.checkCode', ['code' => 'F-BPL-004', 'exclude' => $draft->id]))
            ->assertOk()
            ->assertJsonPath('in_progress', null)
            ->assertJsonPath('expected_revision', 1);
    }

    public function test_archived_documents_are_ignored(): void
    {
        $this->makeForm(['status' => 'Archived'])->delete();

        $this->actingAs($this->user)
            ->getJson(route('document.forms.checkCode', ['code' => 'F-BPL-004']))
            ->assertOk()
            ->assertJsonPath('active', null)
            ->assertJsonPath('in_progress', null);
    }

    public function test_another_companys_documents_are_not_visible(): void
    {
        $this->makeForm(['status' => 'Active', 'revision_number' => '4']);

        $otherCompany = Company::factory()->create();
        $outsider = User::factory()->create(['company_id' => $otherCompany->id]);

        $this->actingAs($outsider)
            ->getJson(route('document.forms.checkCode', ['code' => 'F-BPL-004']))
            ->assertOk()
            ->assertJsonPath('active', null)
            ->assertJsonPath('expected_revision', 0);
    }

    public function test_support_documents_are_checked_against_their_own_table(): void
    {
        $this->makeForm(['code' => 'SD-MPL-01', 'status' => 'Active', 'revision_number' => '5']);

        $this->actingAs($this->user);
        $support = SupportDocument::create([
            'title' => 'Risk Management Guidelines',
            'code' => 'SD-MPL-01',
            'section_id' => $this->section->id,
            'pages' => 6,
            'objective' => 'Objective',
            'scope' => 'Scope',
            'status' => 'Active',
            'revision_number' => '0',
            'justification' => 'Initial issue',
            'file_path' => 'manuals/original.pdf',
        ]);

        $this->getJson(route('document.support_document.checkCode', ['code' => 'SD-MPL-01']))
            ->assertOk()
            ->assertJsonPath('active.id', $support->id)
            ->assertJsonPath('active.url', route('document.support_document.view', $support->id))
            ->assertJsonPath('expected_revision', 1);
    }

    public function test_code_is_required(): void
    {
        $this->actingAs($this->user)
            ->getJson(route('document.forms.checkCode'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('code');
    }

    public function test_upload_pages_expose_section_acronyms_for_autofill(): void
    {
        $this->section->update(['description' => 'BPL']);

        foreach (['document.forms.create', 'document.support_document.create'] as $route) {
            $this->actingAs($this->user)
                ->get(route($route))
                ->assertOk()
                ->assertSee('data-pdf-autofill=', false)
                ->assertSee('data-acronym="BPL"', false);
        }
    }

    public function test_guests_cannot_check_codes(): void
    {
        $this->get(route('document.forms.checkCode', ['code' => 'F-BPL-004']))
            ->assertRedirect();
    }
}
