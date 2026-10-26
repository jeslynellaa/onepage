<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Document;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemProcedurePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_preview_renders_when_section_signatories_are_not_set(): void
    {
        $company = Company::factory()->create();
        $owner = User::factory()->create(['company_id' => $company->id]);
        $section = Section::factory()->create([
            'company_id' => $company->id,
            'process_owner_id' => $owner->id,
            'reviewer_id' => null,
            'approver_id' => null,
        ]);

        $this->actingAs($owner);
        $doc = Document::create([
            'type' => 'System Procedure',
            'title' => 'Objectives, Targets, and Programs Setting',
            'code' => 'SP-OBJ-01',
            'section_id' => $section->id,
            'revision_number' => '0',
            'objective' => 'Objective',
            'scope' => 'Scope',
            'status' => 'Draft',
        ]);

        $this->get(route('document.system_procedures.sp_preview', $doc))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }
}
