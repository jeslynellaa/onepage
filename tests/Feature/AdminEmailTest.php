<?php

namespace Tests\Feature;

use App\Mail\AdminAnnouncementMail;
use App\Models\Company;
use App\Models\User;
use App\Services\DemoProvisioner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminEmailTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Company $client;

    protected function setUp(): void
    {
        parent::setUp();

        // The host/platform company must be id 1 (see the enter-admin gate).
        $host = Company::factory()->create(['name' => 'FCU']);
        $this->assertSame(1, $host->id);
        $this->admin = User::factory()->create(['company_id' => $host->id, 'role' => 'Admin', 'first_name' => 'Ana']);

        $this->client = Company::factory()->create(['name' => 'Acme Corp']);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'subject' => 'Scheduled maintenance',
            'message' => "OnePage will be down on Friday.\n\nThank you for your patience.",
            'button_label' => '',
            'button_url' => '',
            'recipients' => [],
        ], $overrides);
    }

    public function test_page_lists_real_users_grouped_by_company_but_not_demo_personas(): void
    {
        $alice = User::factory()->create(['company_id' => $this->client->id, 'email' => 'alice@acme.test']);
        $demo = (new DemoProvisioner)->provision('Prospect (Demo)', 24, now());
        $persona = User::where('company_id', $demo->id)->first();

        $this->actingAs($this->admin)
            ->get(route('admin.emails.create'))
            ->assertOk()
            ->assertSee('Acme Corp')
            ->assertSee('alice@acme.test')
            ->assertDontSee($persona->email);
    }

    public function test_sends_one_personalised_email_to_each_selected_user_only(): void
    {
        $alice = User::factory()->create(['company_id' => $this->client->id, 'first_name' => 'Alice']);
        $bob = User::factory()->create(['company_id' => $this->client->id, 'first_name' => 'Bob']);
        $carol = User::factory()->create(['company_id' => $this->client->id]);
        Mail::fake();

        $this->actingAs($this->admin)
            ->post(route('admin.emails.send'), $this->payload([
                'recipients' => [$alice->id, $bob->id],
                'button_label' => 'Open OnePage',
                'button_url' => 'https://onepage.test/login',
            ]))
            ->assertRedirect(route('admin.emails.create'))
            ->assertSessionHas('success');

        Mail::assertSent(AdminAnnouncementMail::class, 2);
        Mail::assertSent(AdminAnnouncementMail::class, fn ($mail) => $mail->hasTo($alice->email) && count($mail->to) === 1);
        Mail::assertSent(AdminAnnouncementMail::class, fn ($mail) => $mail->hasTo($bob->email));
        Mail::assertNotSent(AdminAnnouncementMail::class, fn ($mail) => $mail->hasTo($carol->email));

        $mail = new AdminAnnouncementMail($alice, 'Scheduled maintenance', "OnePage will be down on Friday.\n\nThank you <b>all</b>.", 'Open OnePage', 'https://onepage.test/login');
        $mail->assertHasSubject('Scheduled maintenance');
        $mail->assertSeeInHtml('Hi Alice,', false);
        $mail->assertSeeInHtml('OnePage will be down on Friday.');
        $mail->assertSeeInHtml('Thank you &lt;b&gt;all&lt;/b&gt;.', false);
        $mail->assertSeeInHtml('https://onepage.test/login');
    }

    public function test_ignores_posted_ids_of_demo_personas(): void
    {
        $demo = (new DemoProvisioner)->provision('Prospect (Demo)', 24, now());
        $persona = User::where('company_id', $demo->id)->first();
        Mail::fake();

        $this->actingAs($this->admin)
            ->post(route('admin.emails.send'), $this->payload(['recipients' => [$persona->id]]))
            ->assertSessionHasErrors('recipients');

        Mail::assertNothingSent();
    }

    public function test_requires_at_least_one_recipient(): void
    {
        Mail::fake();

        $this->actingAs($this->admin)
            ->post(route('admin.emails.send'), $this->payload())
            ->assertSessionHasErrors('recipients');

        Mail::assertNothingSent();
    }

    public function test_send_test_goes_only_to_the_admin(): void
    {
        $alice = User::factory()->create(['company_id' => $this->client->id]);
        Mail::fake();

        $this->actingAs($this->admin)
            ->post(route('admin.emails.send'), $this->payload(['recipients' => [$alice->id], 'test' => '1']))
            ->assertSessionHas('success');

        Mail::assertSent(AdminAnnouncementMail::class, 1);
        Mail::assertSent(AdminAnnouncementMail::class, fn ($mail) => $mail->hasTo($this->admin->email));
    }

    public function test_non_host_admins_are_forbidden(): void
    {
        $clientAdmin = User::factory()->create(['company_id' => $this->client->id, 'role' => 'Admin']);

        $this->actingAs($clientAdmin)->get(route('admin.emails.create'))->assertForbidden();
        $this->actingAs($clientAdmin)->post(route('admin.emails.send'), $this->payload())->assertForbidden();
    }
}
