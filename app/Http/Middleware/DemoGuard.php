<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DemoGuard
{
    /**
     * Routes a demo persona must never reach: the platform admin/consultant
     * areas (several client-admin actions aren't gated by enter-admin), anything
     * that would let a visitor take over a persona account, and Risk Management,
     * which is still under internal review and not shown to prospects yet.
     */
    private const BLOCKED_ROUTES = [
        'admin.*',
        'consultant.*',
        'profile.password.update',
        'risk.*',
        'soar.*',
    ];

    /**
     * Lock expired demo sessions out immediately and fence demo personas off
     * from routes that don't belong in a throwaway sandbox.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $company = $request->user()?->company;

        if (! $company?->is_demo) {
            return $next($request);
        }

        if ($company->demoExpired()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('demo.index')
                ->withErrors(['demo' => 'This demo has expired. Book a session and we\'ll set you up with a fresh one.']);
        }

        if ($request->routeIs(self::BLOCKED_ROUTES) || $this->touchesAnotherUsersProfile($request)) {
            if ($request->expectsJson()) {
                abort(403, 'Not available in the demo.');
            }

            return redirect()->back()->withErrors(['demo' => 'That feature is turned off in the demo.']);
        }

        return $next($request);
    }

    private function touchesAnotherUsersProfile(Request $request): bool
    {
        if (! $request->routeIs('profile.edit', 'profile.update')) {
            return false;
        }

        $user = $request->route('user');
        $userId = is_object($user) ? $user->getKey() : (int) $user;

        return $userId !== $request->user()->id;
    }
}
