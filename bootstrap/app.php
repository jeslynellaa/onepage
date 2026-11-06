<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // During `php artisan down`, keep the public marketing pages reachable;
        // everything else (login, register, the app itself) returns the 503 page.
        $middleware->preventRequestsDuringMaintenance(except: [
            '/',
            'faqs',
            'pricing',
            'book-demo',
        ]);

        $middleware->alias([
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'nocache' => \App\Http\Middleware\NoCache::class,
            'client-context' => \App\Http\Middleware\ResolveClientContext::class,
            'demo-guard' => \App\Http\Middleware\DemoGuard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
