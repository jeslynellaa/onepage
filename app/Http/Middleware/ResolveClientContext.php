<?php

namespace App\Http\Middleware;

use App\Support\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveClientContext
{
    /**
     * Drop a stale/revoked active-client session key on every request, so a
     * consultant whose assignment is revoked mid-session loses access immediately
     * rather than only on next login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('active_client_id')) {
            $valid = auth()->check()
                && CompanyContext::hasActiveAssignment(auth()->id(), (int) session('active_client_id'));

            if (! $valid) {
                session()->forget('active_client_id');
            }
        }

        return $next($request);
    }
}
