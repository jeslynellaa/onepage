<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated as BaseRedirectIfAuthenticated;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated extends BaseRedirectIfAuthenticated
{
    /**
     * While the app is down for maintenance, let logged-in users see the public
     * pages instead of bouncing them to the dashboard, which is itself down.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if (app()->isDownForMaintenance()) {
            return $next($request);
        }

        return parent::handle($request, $next, ...$guards);
    }
}
