<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('enter-admin', function (User $user) {
            return $user->role === 'Admin' && $user->company_id === 1;
        });

        // Host-company (FCU) staff are eligible to be assigned as consultants.
        // This only gates access to the consultant area itself — actual access to a
        // given client still requires a live ClientUser assignment for that company.
        Gate::define('consultant-access', function (User $user) {
            return $user->company_id === 1;
        });
    }
}
