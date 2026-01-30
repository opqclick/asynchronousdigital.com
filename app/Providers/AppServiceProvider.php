<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // Define Gates for role-based menu access
        Gate::define('admin', function ($user) {
            return $user->role->name === 'admin';
        });

        Gate::define('team_member', function ($user) {
            return $user->role->name === 'team_member';
        });

        Gate::define('client', function ($user) {
            return $user->role->name === 'client';
        });
    }
}
