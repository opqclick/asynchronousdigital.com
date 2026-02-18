<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogUserActivity;
use Illuminate\Support\Facades\Event;
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
        // Register event listeners
        Event::listen(Login::class, LogUserActivity::class);
        Event::listen(Login::class, function (Login $event) {
            $user = User::find($event->user->getAuthIdentifier());
            if ($user) {
                $user->ensureActiveRoleContext();
            }
        });

        Gate::before(function ($user, string $ability) {
            if ($ability === 'impersonating') {
                return null;
            }

            if ($user->isAdmin()) {
                return true;
            }

            return null;
        });

        // Define Gates for role-based menu access
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('project_manager', function ($user) {
            return $user->isProjectManager();
        });

        Gate::define('admin_or_project_manager', function ($user) {
            return $user->isAdmin() || $user->isProjectManager();
        });

        Gate::define('team_member', function ($user) {
            return $user->isTeamMember();
        });

        Gate::define('client', function ($user) {
            return $user->isClient();
        });

        Gate::define('impersonating', function ($user) {
            return session()->has('impersonator_id');
        });

        foreach (Role::allPermissions() as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}
