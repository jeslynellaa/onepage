<?php

namespace Tests\Feature;

use App\Mail\ConsultantAssignedMail;
use App\Models\ClientUser;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ConsultantAssignmentMailTest extends TestCase
{
    use RefreshDatabase;

    private Company $client;

    private User $admin;

    private User $consultant;

    protected function setUp(): void
    {
        parent::setUp();

        $fcu = Company::factory()->create();
        $this->assertSame(1, $fcu->id);

        $this->client = Company::factory()->create();
        $this->admin = User::factory()->create(['company_id' => $fcu->id, 'role' => 'Admin']);
        $this->consultant = User::factory()->create(['company_id' => $fcu->id]);
    }

    private function assign()
    {
        return $this->actingAs($this->admin)
            ->from(route('dashboard'))
            ->post(route('admin.client.consultants.assign', $this->client), ['user_id' => $this->consultant->id]);
    }

    public function test_new_assignment_emails_the_consultant(): void
    {
        Mail::fake();

        $this->assign()->assertSessionHas('success')->assertSessionHasNoErrors();

        $this->assertDatabaseHas('client_users', [
            'user_id' => $this->consultant->id,
            'company_id' => $this->client->id,
            'status' => 'active',
        ]);
        Mail::assertSent(ConsultantAssignedMail::class, fn ($mail) => $mail->hasTo($this->consultant->email)
            && $mail->client->is($this->client));
    }

    public function test_reassigning_a_revoked_consultant_emails_them_again(): void
    {
        Mail::fake();
        ClientUser::create([
            'user_id' => $this->consultant->id,
            'company_id' => $this->client->id,
            'status' => 'revoked',
            'revoked_at' => now(),
        ]);

        $this->assign()->assertSessionHas('success');

        Mail::assertSent(ConsultantAssignedMail::class, 1);
    }

    public function test_reassigning_an_active_consultant_sends_no_email(): void
    {
        Mail::fake();
        ClientUser::create([
            'user_id' => $this->consultant->id,
            'company_id' => $this->client->id,
            'status' => 'active',
        ]);

        $this->assign()->assertSessionHas('success');

        Mail::assertNothingSent();
    }

    public function test_failed_email_keeps_the_assignment_and_warns_the_admin(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('SMTP down'));

        $this->assign()->assertSessionHas('success')->assertSessionHasErrors('email');

        $this->assertDatabaseHas('client_users', [
            'user_id' => $this->consultant->id,
            'company_id' => $this->client->id,
            'status' => 'active',
        ]);
    }

    public function test_email_tells_the_consultant_how_to_access_the_client(): void
    {
        $html = (new ConsultantAssignedMail($this->consultant, $this->client))->render();

        $this->assertStringContainsString(e($this->client->name), $html);
        $this->assertStringContainsString(route('consultant.clients'), $html);
        $this->assertStringContainsString('Enter', $html);
    }
}
