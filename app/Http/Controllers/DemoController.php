<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Services\DemoProvisioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    /**
     * Entry point for a signed demo link (see demo:create). Logs the visitor in as
     * the demo company's landing persona; reopening the link returns them to the
     * same company with their changes intact.
     */
    public function enter(Request $request, Company $company)
    {
        abort_unless($company->is_demo, 404);

        if ($company->demoExpired()) {
            return redirect()->route('demo.index')
                ->withErrors(['demo' => 'This demo has expired. Book a session and we\'ll set you up with a fresh one.']);
        }

        $persona = User::where('company_id', $company->id)
            ->where('role', DemoProvisioner::LANDING_ROLE)
            ->firstOrFail();

        // Drop whoever was logged in on this browser (possibly a real account)
        // before handing it a demo session.
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
        }

        Auth::login($persona);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    /**
     * Swap the logged-in user for another persona in the same demo company.
     */
    public function switchPersona(Request $request)
    {
        $validated = $request->validate(['user_id' => 'required|integer']);

        $current = $request->user();
        abort_unless($current->company?->is_demo, 403);

        $target = User::where('company_id', $current->company_id)->findOrFail($validated['user_id']);

        Auth::login($target);
        $request->session()->regenerate();

        return redirect()->back()->with('success', "You're now viewing as {$target->first_name} ({$target->position}).");
    }
}
