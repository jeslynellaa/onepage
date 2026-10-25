<?php

namespace Tests\Feature\Demo;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\Document;
use App\Models\Section;
use App\Models\User;
use App\Services\DemoProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DemoWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function demo(int $hours = 72): Company
    {
        return (new DemoProvisioner)->provision('Acme (Demo)', $hours);
    }

    private function enterLink(Company $company): string
    {
        return URL::temporarySignedRoute('demo.enter', $company->demo_expires_at, ['company' => $company]);
    }

    private function persona(Company $company, string $role): User
    {
        return User::where('company_id', $company->id)->where('role', $role)->firstOrFail();
    }

    public function test_provisioning_builds_an_isolated_company_with_every_persona_and_workflow_status(): void
    {
        $other = Company::factory()->create();
        $company = $this->demo();

        $this->assertTrue($company->is_demo);
        $this->assertNotSame(1, $company->id);

        $this->assertEqualsCanonicalizing(
            array_keys(DemoProvisioner::PERSONAS),
            User::where('company_id', $company->id)->pluck('role')->all()
        );

        $statuses = Document::withoutGlobalScopes()->where('company_id', $company->id)->pluck('status')->unique()->all();
        $this->assertEqualsCanonicalizing(
            ['Draft', 'For Review', 'For Revision', 'For Approval', 'Pending Code', 'Active', 'Superseded'],
            $statuses
        );

        $owner = $this->persona($company, 'User');
        $this->assertSame(
            Section::where('company_id', $company->id)->count(),
            Section::where('company_id', $company->id)->where('process_owner_id', $owner->id)->count()
        );

        // Nothing leaked into another tenant, and every log row is stamped correctly.
        $this->assertSame(0, Document::withoutGlobalScopes()->where('company_id', $other->id)->count());
        $this->assertSame(0, ActivityLog::withoutGlobalScopes()->whereNull('company_id')->count());
    }

    public function test_provisioning_inside_a_logged_in_request_still_stamps_the_demo_company(): void
    {
        $real = Company::factory()->create();
        $this->actingAs(User::factory()->create(['company_id' => $real->id]));

        $company = $this->demo();

        $this->assertSame(0, Document::withoutGlobalScopes()->where('company_id', $real->id)->count());
        $this->assertGreaterThan(0, Document::withoutGlobalScopes()->where('company_id', $company->id)->count());
    }

    public function test_signed_link_logs_in_as_the_document_controller(): void
    {
        $company = $this->demo();

        $this->get($this->enterLink($company))->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($this->persona($company, 'Document Controller'));
    }

    public function test_unsigned_or_tampered_link_is_rejected(): void
    {
        $company = $this->demo();

        $this->get(route('demo.enter', $company))->assertForbidden();
        $this->get($this->enterLink($company).'x')->assertForbidden();
        $this->assertGuest();
    }

    public function test_link_never_opens_a_real_company(): void
    {
        $real = Company::factory()->create(['demo_expires_at' => now()->addDay()]);

        $this->get($this->enterLink($real))->assertNotFound();
        $this->assertGuest();
    }

    public function test_link_replaces_an_existing_real_session(): void
    {
        $real = User::factory()->create();
        $company = $this->demo();

        $this->actingAs($real)->get($this->enterLink($company));

        $this->assertAuthenticatedAs($this->persona($company, 'Document Controller'));
    }

    public function test_expired_demo_is_locked_out_mid_session(): void
    {
        $company = $this->demo();
        $this->actingAs($this->persona($company, 'Reviewer'));

        $this->travel(73)->hours();

        $this->get(route('dashboard'))->assertRedirect(route('demo.index'));
        $this->assertGuest();
    }

    public function test_switching_persona_within_the_demo(): void
    {
        $company = $this->demo();
        $reviewer = $this->persona($company, 'Reviewer');

        $this->actingAs($this->persona($company, 'Document Controller'))
            ->post(route('demo.switch'), ['user_id' => $reviewer->id])
            ->assertRedirect();

        $this->assertAuthenticatedAs($reviewer);
    }

    public function test_cannot_switch_into_another_company(): void
    {
        $company = $this->demo();
        $outsider = User::factory()->create();
        $controller = $this->persona($company, 'Document Controller');

        $this->actingAs($controller)
            ->post(route('demo.switch'), ['user_id' => $outsider->id])
            ->assertNotFound();

        $this->assertAuthenticatedAs($controller);
    }

    public function test_real_users_cannot_use_the_switcher(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $colleague = User::factory()->create(['company_id' => $company->id]);

        $this->actingAs($user)
            ->post(route('demo.switch'), ['user_id' => $colleague->id])
            ->assertForbidden();

        $this->assertAuthenticatedAs($user);
    }

    public function test_demo_personas_are_fenced_off_from_admin_and_account_takeover_routes(): void
    {
        $company = $this->demo();
        $admin = $this->persona($company, 'Admin');
        $someoneElse = User::factory()->create();

        $this->actingAs($admin);

        $this->get(route('admin.client.create'))->assertRedirect();
        $this->post(route('admin.client.store'), ['name' => 'Hijack', 'subscription_plan' => 'x', 'subscription_status' => 'active'])
            ->assertRedirect()->assertSessionHasErrors('demo');
        $this->get(route('profile.edit', $someoneElse))->assertSessionHasErrors('demo');
        $this->put(route('profile.password.update', $admin), [])->assertSessionHasErrors('demo');

        $this->assertDatabaseMissing('companies', ['name' => 'Hijack']);
        $this->get(route('profile.edit', $admin))->assertOk();
    }

    public function test_user_search_only_returns_the_acting_company(): void
    {
        $company = $this->demo();
        User::factory()->create(['first_name' => 'Rinaldo']);

        $this->actingAs($this->persona($company, 'User'))
            ->getJson('/users/search?query=Rin')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.first_name', 'Rina');
    }

    public function test_mail_to_demo_personas_is_dropped_but_real_recipients_still_receive_it(): void
    {
        $company = $this->demo();
        $persona = $this->persona($company, 'User');
        $this->assertNull(Mail::raw('Action assigned', fn ($m) => $m->to($persona->email)));

        $sent = Mail::raw('Action assigned', fn ($m) => $m->to([$persona->email, 'real@example.com']));
        $this->assertNotNull($sent);
        $this->assertSame(['real@example.com'], array_map(fn ($a) => $a->getAddress(), $sent->getOriginalMessage()->getTo()));
    }

    public function test_prune_deletes_only_expired_demos_and_all_their_rows(): void
    {
        $expired = $this->demo(1);
        $live = $this->demo(72);
        $real = Company::factory()->create();

        $this->travel(2)->hours();
        $this->artisan('demo:prune')->assertSuccessful();

        $this->assertDatabaseMissing('companies', ['id' => $expired->id]);
        foreach (['users', 'sections', 'documents', 'procedure_steps', 'step_documents', 'activity_logs', 'procedure_comments'] as $table) {
            $this->assertDatabaseMissing($table, ['company_id' => $expired->id]);
        }

        $this->assertDatabaseHas('companies', ['id' => $live->id]);
        $this->assertDatabaseHas('documents', ['company_id' => $live->id]);
        $this->assertDatabaseHas('companies', ['id' => $real->id]);
    }

    public function test_create_command_prints_a_working_link(): void
    {
        $this->artisan('demo:create', ['--company' => 'Globex (Demo)', '--hours' => 48])
            ->expectsOutputToContain('/demo/')
            ->assertSuccessful();

        $company = Company::where('name', 'Globex (Demo)')->firstOrFail();
        $this->assertTrue($company->is_demo);
        $this->assertEqualsWithDelta(48 * 3600, now()->diffInSeconds($company->demo_expires_at), 5);
    }

    public function test_each_waiting_document_can_be_moved_on_by_its_persona(): void
    {
        $company = $this->demo();
        $doc = fn (string $status) => Document::withoutGlobalScopes()
            ->where('company_id', $company->id)->where('status', $status)->firstOrFail();

        $forReview = $doc('For Review');
        $this->actingAs($this->persona($company, 'Reviewer'))
            ->put(route('document.system_procedures.reviewPassOrFail', $forReview), ['decision' => 'pass']);
        $this->assertSame('For Approval', $forReview->fresh()->status);

        $forApproval = $doc('For Approval');
        $this->actingAs($this->persona($company, 'Approver'))
            ->put(route('document.system_procedures.approveOrNot', $forApproval), ['decision' => 'pass']);
        $this->assertSame('Pending Code', $forApproval->fresh()->status);

        $pendingCode = $forApproval->fresh();
        $this->actingAs($this->persona($company, 'Document Controller'))
            ->put(route('document.system_procedures.assignCode', $pendingCode), ['document_code' => $pendingCode->code, 'revision_number' => '0']);
        $this->assertSame('Active', $pendingCode->fresh()->status);
    }

    public function test_key_pages_render_for_each_persona(): void
    {
        $company = $this->demo();

        foreach (User::where('company_id', $company->id)->get() as $persona) {
            $this->actingAs($persona);

            $this->get(route('dashboard'))->assertOk()->assertSee('Demo workspace');
            $this->get(route('document.system_procedures'))->assertOk();
            $this->get(route('document.ms_manual.index'))->assertOk();
        }
    }
}
