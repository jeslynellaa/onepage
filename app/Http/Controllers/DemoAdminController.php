<?php

namespace App\Http\Controllers;

use App\Mail\DemoInvitationMail;
use App\Models\Company;
use App\Models\DemoRequest;
use App\Services\DemoProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

/**
 * Host-admin screen for scheduling throwaway demo workspaces, usually from a
 * Book Demo request, and managing the ones already handed out.
 */
class DemoAdminController extends Controller
{
    public function index()
    {
        Gate::authorize('enter-admin');

        $demos = Company::where('is_demo', true)
            ->orderByDesc('demo_starts_at')
            ->get();

        $requests = DemoRequest::with('demoCompany')
            ->latest()
            ->limit(50)
            ->get();

        // The Book Demo request each demo was created from, to suggest a name/email when sending.
        $requestsByDemo = DemoRequest::whereIn('demo_company_id', $demos->modelKeys())
            ->get()
            ->keyBy('demo_company_id');

        return view('admin.demos.index', compact('demos', 'requests', 'requestsByDemo'));
    }

    public function store(Request $request, DemoProvisioner $provisioner)
    {
        Gate::authorize('enter-admin');

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'hours' => 'required|integer|min:1|max:720',
            'demo_request_id' => 'nullable|exists:demo_requests,id',
        ]);

        $company = $provisioner->provision(
            $validated['company_name'],
            (int) $validated['hours'],
            Carbon::parse($validated['starts_at'])
        );

        if ($validated['demo_request_id'] ?? null) {
            DemoRequest::whereKey($validated['demo_request_id'])->update(['demo_company_id' => $company->id]);
        }

        return redirect()->route('admin.demos.index')
            ->with('success', "Demo for {$company->name} is scheduled. Copy its link below.")
            ->with('new_demo_id', $company->id);
    }

    public function extend(Request $request, Company $demo)
    {
        Gate::authorize('enter-admin');
        abort_unless($demo->is_demo, 404);

        $validated = $request->validate(['hours' => 'required|integer|min:1|max:720']);

        // Extend from whichever is later, so reviving an expired demo gives it the full time.
        $from = $demo->demo_expires_at && $demo->demo_expires_at->isFuture() ? $demo->demo_expires_at : now();
        $expiresAt = $from->copy()->addHours((int) $validated['hours']);

        $demo->update(['demo_expires_at' => $expiresAt, 'subscription_ends_at' => $expiresAt]);

        return back()->with('success', "{$demo->name} now closes {$expiresAt->format('M j, g:i A')}.");
    }

    /**
     * Email the prospect their demo's schedule and link. The recipient is always
     * typed in by the admin, never taken automatically from the Book Demo request.
     */
    public function send(Request $request, Company $demo)
    {
        Gate::authorize('enter-admin');
        abort_unless($demo->is_demo, 404);

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'recipient_name' => 'nullable|string|max:255',
        ]);

        if ($demo->demoExpired()) {
            return back()->withErrors(['email' => "{$demo->name} has already closed. Extend it before sending the link."]);
        }

        try {
            Mail::to($validated['email'])->send(new DemoInvitationMail($demo, $validated['recipient_name'] ?? null));
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['email' => 'The email could not be sent. Check the mail settings and try again.']);
        }

        return back()->with('success', "Demo link for {$demo->name} sent to {$validated['email']}.");
    }

    public function end(Company $demo)
    {
        Gate::authorize('enter-admin');
        abort_unless($demo->is_demo, 404);

        // Locks the link and any open session immediately; demo:prune deletes the data.
        $demo->update(['demo_expires_at' => now(), 'subscription_ends_at' => now()]);

        return back()->with('success', "{$demo->name} has been closed and will be deleted within the hour.");
    }
}
