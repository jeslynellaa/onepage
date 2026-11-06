<?php

namespace App\Http\Controllers;

use App\Mail\AdminAnnouncementMail;
use App\Models\User;
use App\Services\DemoProvisioner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

/**
 * Host-admin screen for writing a one-off email and sending it to selected
 * OnePage users (Admin → Email Users).
 */
class AdminEmailController extends Controller
{
    public function create()
    {
        Gate::authorize('enter-admin');

        $recipients = $this->eligibleUsers()
            ->with('company:id,name')
            ->get()
            ->sortBy([
                fn (User $a, User $b) => strcasecmp($a->company?->name ?? '', $b->company?->name ?? ''),
                fn (User $a, User $b) => strcasecmp($a->last_name.$a->first_name, $b->last_name.$b->first_name),
            ])
            ->groupBy(fn (User $user) => $user->company?->name ?? 'No company');

        return view('admin.emails.create', compact('recipients'));
    }

    public function send(Request $request)
    {
        Gate::authorize('enter-admin');

        $isTest = $request->boolean('test');

        $validated = $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:10000',
            'button_label' => 'nullable|required_with:button_url|string|max:60',
            'button_url' => 'nullable|required_with:button_label|url:http,https|max:2048',
            'recipients' => $isTest ? 'nullable|array' : 'required|array|min:1',
            'recipients.*' => 'integer',
        ], [
            'recipients.required' => 'Select at least one recipient.',
            'recipients.min' => 'Select at least one recipient.',
        ]);

        $makeMail = fn (User $user) => new AdminAnnouncementMail(
            $user,
            $validated['subject'],
            $validated['message'],
            $validated['button_label'] ?? null,
            $validated['button_url'] ?? null,
        );

        if ($isTest) {
            $me = $request->user();

            try {
                Mail::to($me->email)->send($makeMail($me));
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->withErrors(['subject' => 'The test email could not be sent. Check the mail settings and try again.']);
            }

            return back()->withInput()->with('success', "Test email sent to {$me->email}.");
        }

        // Only ever send to users this page offers, whatever ids were posted.
        $users = $this->eligibleUsers()->whereKey($validated['recipients'])->get();

        if ($users->isEmpty()) {
            return back()->withInput()->withErrors(['recipients' => 'Select at least one recipient.']);
        }

        // One email per person, so recipients don't see each other's addresses.
        $failed = [];
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send($makeMail($user));
            } catch (\Throwable $e) {
                report($e);
                $failed[] = $user->email;
            }
        }

        $sent = $users->count() - count($failed);

        if ($failed) {
            return back()->withInput()->withErrors([
                'recipients' => "Sent to {$sent} of {$users->count()}. Could not send to: ".implode(', ', $failed).'.',
            ]);
        }

        return redirect()->route('admin.emails.create')
            ->with('success', "Email \"{$validated['subject']}\" sent to {$sent} ".str('recipient')->plural($sent).'.');
    }

    /** Real users with an email address: demo workspace personas are left out. */
    private function eligibleUsers(): Builder
    {
        return User::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where('email', 'not like', '%@'.DemoProvisioner::EMAIL_DOMAIN)
            ->where(fn (Builder $q) => $q
                ->whereNull('company_id')
                ->orWhereHas('company', fn (Builder $c) => $c->where('is_demo', false)));
    }
}
