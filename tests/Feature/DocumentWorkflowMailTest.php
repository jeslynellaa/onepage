<?php

namespace Tests\Feature;

use App\Mail\DocumentWorkflowMail;
use App\Models\Company;
use App\Models\Document;
use App\Models\Form;
use App\Models\Section;
use App\Models\SupportDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DocumentWorkflowMailTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    private User $owner;

    private User $reviewer;

    private User $approver;

    private User $controller;

    private Section $section;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->company = Company::factory()->create();
        $this->owner = User::factory()->create(['company_id' => $this->company->id]);
        $this->reviewer = User::factory()->create(['company_id' => $this->company->id]);
        $this->approver = User::factory()->create(['company_id' => $this->company->id]);
        $this->controller = User::factory()->role('Document Controller')->create(['company_id' => $this->company->id]);

        $this->section = Section::factory()->create([
            'company_id' => $this->company->id,
            'process_owner_id' => $this->owner->id,
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

    private function assertEmailed(User $user, string $event): void
    {
        Mail::assertSent(DocumentWorkflowMail::class, fn (DocumentWorkflowMail $mail) => $mail->hasTo($user->email) && $mail->event === $event);
    }

    public function test_sending_for_review_emails_the_reviewer(): void
    {
        $doc = $this->makeDoc('Draft');

        $this->actingAs($this->owner)
            ->put(route('document.system_procedures.forReview', $doc))
            ->assertSessionHasNoErrors();

        $this->assertEmailed($this->reviewer, DocumentWorkflowMail::REVIEW_REQUESTED);
        Mail::assertSentCount(1);
    }

    public function test_passing_review_emails_the_approver_and_the_process_owner(): void
    {
        $doc = $this->makeDoc('For Review');

        $this->actingAs($this->reviewer)
            ->put(route('document.system_procedures.reviewPassOrFail', $doc), ['decision' => 'pass'])
            ->assertSessionHasNoErrors();

        $this->assertEmailed($this->approver, DocumentWorkflowMail::APPROVAL_REQUESTED);
        $this->assertEmailed($this->owner, DocumentWorkflowMail::REVIEW_PASSED);
        Mail::assertSentCount(2);
    }

    public function test_failing_review_emails_the_process_owner_with_the_reason(): void
    {
        $doc = $this->makeDoc('For Review');

        $this->actingAs($this->reviewer)
            ->put(route('document.system_procedures.reviewPassOrFail', $doc), ['decision' => 'fail', 'reason' => 'Scope is incomplete'])
            ->assertSessionHasNoErrors();

        Mail::assertSent(DocumentWorkflowMail::class, fn (DocumentWorkflowMail $mail) => $mail->hasTo($this->owner->email)
            && $mail->event === DocumentWorkflowMail::REVIEW_FAILED
            && $mail->reason === 'Scope is incomplete');
        Mail::assertSentCount(1);
    }

    public function test_approval_emails_the_companys_document_controllers_and_the_process_owner(): void
    {
        $doc = $this->makeDoc('For Approval');
        $otherCompanyController = User::factory()->role('Document Controller')->create([
            'company_id' => Company::factory()->create()->id,
        ]);

        $this->actingAs($this->approver)
            ->put(route('document.system_procedures.approveOrNot', $doc), ['decision' => 'pass'])
            ->assertSessionHasNoErrors();

        $this->assertEmailed($this->controller, DocumentWorkflowMail::CODE_REQUESTED);
        $this->assertEmailed($this->owner, DocumentWorkflowMail::APPROVED);
        Mail::assertNotSent(DocumentWorkflowMail::class, fn (DocumentWorkflowMail $mail) => $mail->hasTo($otherCompanyController->email));
        Mail::assertSentCount(2);
    }

    public function test_non_approval_emails_the_process_owner_with_the_reason(): void
    {
        $doc = $this->makeDoc('For Approval');

        $this->actingAs($this->approver)
            ->put(route('document.system_procedures.approveOrNot', $doc), ['decision' => 'fail', 'reason' => 'Wrong owner'])
            ->assertSessionHasNoErrors();

        Mail::assertSent(DocumentWorkflowMail::class, fn (DocumentWorkflowMail $mail) => $mail->hasTo($this->owner->email)
            && $mail->event === DocumentWorkflowMail::NOT_APPROVED
            && $mail->reason === 'Wrong owner');
        Mail::assertSentCount(1);
    }

    public function test_sending_back_emails_the_process_owner_naming_who_sent_it_back(): void
    {
        $reviewing = $this->makeDoc('For Review');
        $this->actingAs($this->reviewer)
            ->post(route('document.system_procedures.storeComment', $reviewing), ['scope_comment' => 'Too broad', 'action' => 'send_back'])
            ->assertSessionHasNoErrors();
        $this->assertEmailed($this->owner, DocumentWorkflowMail::SENT_BACK_BY_REVIEWER);

        $approving = $this->makeDoc('For Approval');
        $this->actingAs($this->approver)
            ->post(route('document.system_procedures.storeComment', $approving), ['scope_comment' => 'Too narrow', 'action' => 'send_back'])
            ->assertSessionHasNoErrors();
        $this->assertEmailed($this->owner, DocumentWorkflowMail::SENT_BACK_BY_APPROVER);
    }

    public function test_saving_comments_without_sending_back_emails_nobody(): void
    {
        $doc = $this->makeDoc('For Review');

        $this->actingAs($this->reviewer)
            ->post(route('document.system_procedures.storeComment', $doc), ['scope_comment' => 'Draft note', 'action' => 'save'])
            ->assertSessionHasNoErrors();

        Mail::assertNothingSent();
    }

    public function test_support_documents_and_forms_email_the_same_people(): void
    {
        $this->actingAs($this->owner);
        $support = SupportDocument::create([
            'title' => 'Accreditation Guide', 'code' => 'WI-PUR-01', 'section_id' => $this->section->id, 'pages' => 1,
            'objective' => '<p>Objective</p>', 'scope' => '<p>Scope</p>', 'status' => 'Draft', 'justification' => 'Initial issue',
            'file_path' => 'manuals/original.pdf',
        ]);
        $form = Form::create([
            'title' => 'Purchase Request', 'code' => 'FRM-PUR-01', 'section_id' => $this->section->id, 'pages' => 1,
            'status' => 'Draft', 'justification' => 'Initial issue', 'file_path' => 'manuals/original.pdf',
        ]);

        foreach ([[$support, 'document.support_document'], [$form, 'document.forms']] as [$doc, $prefix]) {
            Mail::fake();

            $this->actingAs($this->owner)->put(route("$prefix.forReview", $doc))->assertSessionHasNoErrors();
            $this->assertEmailed($this->reviewer, DocumentWorkflowMail::REVIEW_REQUESTED);

            $this->actingAs($this->reviewer)->put(route("$prefix.reviewPassOrFail", $doc), ['decision' => 'pass'])->assertSessionHasNoErrors();
            $this->assertEmailed($this->approver, DocumentWorkflowMail::APPROVAL_REQUESTED);
            $this->assertEmailed($this->owner, DocumentWorkflowMail::REVIEW_PASSED);

            $this->actingAs($this->approver)->put(route("$prefix.approveOrNot", $doc), ['decision' => 'pass'])->assertSessionHasNoErrors();
            $this->assertEmailed($this->controller, DocumentWorkflowMail::CODE_REQUESTED);
            $this->assertEmailed($this->owner, DocumentWorkflowMail::APPROVED);

            Mail::assertSentCount(5);
        }
    }

    public function test_someone_holding_two_roles_gets_one_email_asking_them_to_act(): void
    {
        $this->section->update(['approver_id' => $this->owner->id]);
        $doc = $this->makeDoc('For Review');

        $this->actingAs($this->reviewer)
            ->put(route('document.system_procedures.reviewPassOrFail', $doc), ['decision' => 'pass'])
            ->assertSessionHasNoErrors();

        $this->assertEmailed($this->owner, DocumentWorkflowMail::APPROVAL_REQUESTED);
        Mail::assertSentCount(1);
    }

    public function test_nobody_is_emailed_about_their_own_action(): void
    {
        $this->section->update(['approver_id' => $this->reviewer->id]);
        $doc = $this->makeDoc('For Review');

        $this->actingAs($this->reviewer)
            ->put(route('document.system_procedures.reviewPassOrFail', $doc), ['decision' => 'pass'])
            ->assertSessionHasNoErrors();

        $this->assertEmailed($this->owner, DocumentWorkflowMail::REVIEW_PASSED);
        Mail::assertSentCount(1);
    }

    public function test_a_failed_email_keeps_the_transition_and_warns_the_user(): void
    {
        $doc = $this->makeDoc('Draft');
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $this->actingAs($this->owner)
            ->from(route('document.system_procedures'))
            ->put(route('document.system_procedures.forReview', $doc))
            ->assertSessionHas('success')
            ->assertSessionHasErrors('email');

        $this->assertSame('For Review', $doc->fresh()->status);
    }

    public function test_the_email_describes_the_document_and_links_to_it(): void
    {
        $doc = $this->makeDoc('For Review');

        $html = (new DocumentWorkflowMail($this->owner, $doc, DocumentWorkflowMail::REVIEW_FAILED, 'Scope <b>incomplete</b>'))->render();

        $this->assertStringContainsString('Supplier Evaluation (SP-PUR-01)', $html);
        $this->assertStringContainsString('did not pass review', $html);
        $this->assertStringContainsString('Scope &lt;b&gt;incomplete&lt;/b&gt;', $html);
        $this->assertStringContainsString(route('document.system_procedures.view_pdf', $doc), $html);
    }
}
