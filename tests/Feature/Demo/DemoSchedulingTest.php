<?php

namespace Tests\Feature\Demo;

use App\Models\Company;
use App\Models\DemoRequest;
use App\Models\User;
use App\Services\DemoProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DemoSchedulingTest extends TestCase
{
    use RefreshDatabase;

    private function hostAdmin(): User
    {
        // The host/platform company must be id 1 (see the enter-admin gate).
        $host = Company::factory()->create();
        $this->assertSame(1, $host->id);

        return User::factory()->create(['company_id' => $host->id, 'role' => 'Admin']);
    }

    private function scheduled(Carbon $startsAt, int $hours = 72): Company
    {
        return (new DemoProvisioner)->provision('Acme (Demo)', $hours, $startsAt);
    }

    /** A date the Book Demo form accepts: 2+ days out, not a Sunday, not a first-week Monday. */
    private function bookableDate(): Carbon
    {
        $date = now()->addDays(3)->startOfDay();
        while ($date->isSunday() || ($date->isMonday() && $date->day <= 7)) {
            $date->addDay();
        }

        return $date;
    }

    public function test_scheduled_demo_opens_at_its_start_and_runs_for_its_duration(): void
    {
        $startsAt = now()->addDays(5)->setTime(9, 0);
        $company = $this->scheduled($startsAt, 48);

        $this->assertTrue($company->demo_starts_at->equalTo($startsAt));
        $this->assertTrue($company->demo_expires_at->equalTo($startsAt->copy()->addHours(48)));
    }

    public function test_link_shows_the_opens_soon_page_before_the_start_time(): void
    {
        $company = $this->scheduled(now()->addDays(2)->setTime(9, 0));

        $this->get(DemoProvisioner::linkFor($company))
            ->assertOk()
            ->assertSee('Your demo opens soon')
            ->assertSee($company->demo_starts_at->format('l, F j, Y'))
            ->assertSee('9:00 AM');

        $this->assertGuest();
    }

    public function test_the_same_link_logs_in_once_the_demo_opens(): void
    {
        $company = $this->scheduled(now()->addDays(2)->setTime(9, 0));
        $link = DemoProvisioner::linkFor($company);

        $this->travelTo($company->demo_starts_at->copy()->addMinute());

        $this->get($link)->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_sample_history_is_dated_back_from_the_opening_time(): void
    {
        $startsAt = now()->addDays(10)->setTime(9, 0);
        $company = $this->scheduled($startsAt);

        $newest = \App\Models\Document::withoutGlobalScopes()->where('company_id', $company->id)->max('created_at');

        $this->assertTrue(Carbon::parse($newest)->lt($startsAt));
        $this->assertTrue(Carbon::parse($newest)->gt(now()), 'history should read as recent when the prospect opens it');
    }

    public function test_book_demo_form_saves_the_request(): void
    {
        Mail::fake();
        $date = $this->bookableDate();

        $this->post(route('demo.store'), [
            'name' => 'Juan dela Cruz',
            'email' => 'juan@acme.test',
            'company' => 'Acme Corp',
            'teamSize' => '1-15',
            'demo_date' => $date->toDateString(),
            'demo_time' => 'afternoon',
        ])->assertSessionHasNoErrors();

        $request = DemoRequest::sole();
        $this->assertSame('Acme Corp', $request->company);
        $this->assertTrue($request->preferredStart()->equalTo($date->copy()->setTime(13, 0)));
    }

    public function test_admin_page_prefills_the_opening_time_from_the_request(): void
    {
        DemoRequest::create([
            'name' => 'Juan', 'email' => 'juan@acme.test', 'company' => 'Acme Corp', 'team_size' => '1-15',
            'demo_date' => '2026-11-03', 'demo_time' => 'morning',
        ]);

        $this->actingAs($this->hostAdmin())
            ->get(route('admin.demos.index'))
            ->assertOk()
            ->assertSee('Acme Corp')
            ->assertSee('value="2026-11-03T09:00"', false);
    }

    public function test_creating_a_demo_from_a_request_schedules_it_and_links_the_request(): void
    {
        $request = DemoRequest::create([
            'name' => 'Juan', 'email' => 'juan@acme.test', 'company' => 'Acme Corp', 'team_size' => '1-15',
            'demo_date' => now()->addDays(4)->toDateString(), 'demo_time' => 'morning',
        ]);
        $startsAt = $request->preferredStart();

        $this->actingAs($this->hostAdmin())
            ->post(route('admin.demos.store'), [
                'company_name' => 'Acme Corp (Demo)',
                'starts_at' => $startsAt->format('Y-m-d\TH:i'),
                'hours' => 72,
                'demo_request_id' => $request->id,
            ])
            ->assertRedirect(route('admin.demos.index'));

        $demo = Company::where('name', 'Acme Corp (Demo)')->sole();
        $this->assertTrue($demo->is_demo);
        $this->assertTrue($demo->demo_starts_at->equalTo($startsAt));
        $this->assertSame($demo->id, $request->fresh()->demo_company_id);
    }

    public function test_extending_keeps_the_same_link_working_past_the_original_close(): void
    {
        $admin = $this->hostAdmin();
        $company = $this->scheduled(now(), 24);
        $link = DemoProvisioner::linkFor($company);

        $this->actingAs($admin)->put(route('admin.demos.extend', $company), ['hours' => 48])->assertRedirect();
        $this->assertTrue($company->fresh()->demo_expires_at->gt(now()->addHours(70)));

        $this->travel(30)->hours();
        auth()->logout();

        $this->get($link)->assertRedirect(route('dashboard'));
    }

    public function test_ending_a_demo_locks_it_immediately(): void
    {
        $admin = $this->hostAdmin();
        $company = $this->scheduled(now());
        $link = DemoProvisioner::linkFor($company);

        $this->actingAs($admin)->put(route('admin.demos.end', $company))->assertRedirect();
        auth()->logout();

        $this->get($link)->assertRedirect(route('demo.index'));
        $this->assertGuest();
    }

    public function test_link_to_a_deleted_demo_redirects_to_book_demo_with_the_expired_message(): void
    {
        $company = $this->scheduled(now(), 1);
        $link = DemoProvisioner::linkFor($company);

        $this->travel(2)->hours();
        $this->artisan('demo:prune')->assertSuccessful();
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);

        $this->get($link)
            ->assertRedirect(route('demo.index'))
            ->assertSessionHasErrors(['demo' => 'This demo has expired. Book a session and we\'ll set you up with a fresh one.']);
        $this->assertGuest();
    }

    public function test_tampered_link_to_a_deleted_demo_is_still_rejected(): void
    {
        $company = $this->scheduled(now(), 1);
        $link = DemoProvisioner::linkFor($company);
        $this->travel(2)->hours();
        $this->artisan('demo:prune');

        $this->get(str_replace("/demo/{$company->id}/", '/demo/999/', $link))->assertForbidden();
    }

    public function test_admin_sends_the_demo_email_to_the_address_they_type(): void
    {
        Mail::fake();
        $admin = $this->hostAdmin();
        $demo = $this->scheduled(now()->addDays(3)->setTime(9, 0));
        DemoRequest::create([
            'name' => 'Juan', 'email' => 'juan@acme.test', 'company' => 'Acme', 'team_size' => '1-15',
            'demo_date' => now()->addDays(3)->toDateString(), 'demo_time' => 'morning', 'demo_company_id' => $demo->id,
        ]);

        // The page suggests the requester's address but doesn't fill it in.
        $this->actingAs($admin)->get(route('admin.demos.index'))
            ->assertSee('placeholder="juan@acme.test"', false)
            ->assertSee('value="Juan"', false);

        $this->actingAs($admin)
            ->post(route('admin.demos.send', $demo), ['email' => 'it-head@acme.test', 'recipient_name' => 'Maria'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        Mail::assertSent(\App\Mail\DemoInvitationMail::class, function ($mail) use ($demo) {
            $html = $mail->render();

            return $mail->hasTo('it-head@acme.test')
                && ! $mail->hasTo('juan@acme.test')
                && $mail->hasReplyTo(\App\Mail\DemoInvitationMail::CONTACT_EMAIL)
                && str_contains($html, 'Hi Maria')
                && str_contains($html, e(DemoProvisioner::linkFor($demo)))
                && str_contains($html, $demo->demo_starts_at->format('l, F j, Y'))
                && str_contains($html, $demo->demo_expires_at->format('l, F j, Y'))
                && str_contains($html, \App\Mail\DemoInvitationMail::CONTACT_EMAIL);
        });
    }

    public function test_sending_requires_a_valid_email_and_an_open_demo(): void
    {
        Mail::fake();
        $admin = $this->hostAdmin();
        $demo = $this->scheduled(now());

        $this->actingAs($admin)->post(route('admin.demos.send', $demo), ['email' => ''])->assertSessionHasErrors('email');
        $this->actingAs($admin)->post(route('admin.demos.send', $demo), ['email' => 'not-an-email'])->assertSessionHasErrors('email');

        $this->actingAs($admin)->put(route('admin.demos.end', $demo));
        $this->actingAs($admin)->post(route('admin.demos.send', $demo), ['email' => 'juan@acme.test'])->assertSessionHasErrors('email');

        Mail::assertNothingSent();
    }

    public function test_only_host_admins_can_send_demo_emails(): void
    {
        Mail::fake();
        $this->hostAdmin();
        $demo = $this->scheduled(now());

        $this->actingAs(User::factory()->create(['role' => 'Admin']))
            ->post(route('admin.demos.send', $demo), ['email' => 'juan@acme.test'])
            ->assertForbidden();

        Mail::assertNothingSent();
    }

    public function test_only_host_admins_can_manage_demos(): void
    {
        $this->hostAdmin();
        $clientAdmin = User::factory()->create(['role' => 'Admin']);
        $demo = $this->scheduled(now());

        $this->actingAs($clientAdmin)->get(route('admin.demos.index'))->assertForbidden();
        $this->actingAs($clientAdmin)->put(route('admin.demos.end', $demo))->assertForbidden();
        $this->assertFalse($demo->fresh()->demoExpired());

        // Demo personas never reach admin routes at all.
        $persona = User::where('company_id', $demo->id)->where('role', 'Admin')->sole();
        $this->actingAs($persona)->get(route('admin.demos.index'))->assertSessionHasErrors('demo');
    }

    public function test_admin_client_list_excludes_demo_companies(): void
    {
        $admin = $this->hostAdmin();
        Company::factory()->create(['name' => 'Real Client Inc']);
        $this->scheduled(now());

        $this->actingAs($admin)->get(route('admin.index'))
            ->assertOk()
            ->assertSee('Real Client Inc')
            ->assertDontSee('Acme (Demo)')
            ->assertSee('Manage demos');
    }

    public function test_create_command_accepts_a_start_time(): void
    {
        $this->artisan('demo:create', ['--company' => 'Globex (Demo)', '--starts' => now()->addDays(3)->format('Y-m-d').' 13:00', '--hours' => 24])
            ->expectsOutputToContain('/demo/')
            ->assertSuccessful();

        $demo = Company::where('name', 'Globex (Demo)')->sole();
        $this->assertSame('13:00', $demo->demo_starts_at->format('H:i'));
        $this->assertTrue($demo->demoNotYetOpen());
    }
}
