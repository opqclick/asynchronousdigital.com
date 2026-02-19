<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Role;
use App\Models\SystemSetting;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogUserActivity;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
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
        $this->configureSecureUrlGeneration();
        $this->applySystemMailSettings();

        // Register event listeners
        Event::listen(Login::class, LogUserActivity::class);
        Event::listen(Login::class, function (Login $event) {
            $user = User::find($event->user->getAuthIdentifier());
            if ($user) {
                $user->ensureActiveRoleContext();
            }
        });

        // Define Gates for role-based menu access
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('project_manager', function ($user) {
            return $user->isProjectManager();
        });

        Gate::define('project_manager_only', function ($user) {
            return !$user->isAdmin() && $user->isProjectManager();
        });

        Gate::define('admin_or_project_manager', function ($user) {
            return $user->isAdmin() || $user->isProjectManager();
        });

        Gate::define('team_member', function ($user) {
            return $user->isTeamMember();
        });

        Gate::define('team_member_only', function ($user) {
            return !$user->isAdmin() && $user->isTeamMember();
        });

        Gate::define('client', function ($user) {
            return $user->isClient();
        });

        Gate::define('client_only', function ($user) {
            return !$user->isAdmin() && $user->isClient();
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

    private function configureSecureUrlGeneration(): void
    {
        if (app()->runningInConsole()) {
            return;
        }

        $forwardedProto = (string) request()->header('X-Forwarded-Proto', '');
        if (strtolower($forwardedProto) === 'https') {
            URL::forceScheme('https');
        }
    }

    private function applySystemMailSettings(): void
    {
        try {
            if (!Schema::hasTable('system_settings')) {
                return;
            }
        } catch (\Throwable $exception) {
            return;
        }

        $mailer = SystemSetting::getValue('mail_mailer', config('mail.default', 'smtp'));

        config([
            'mail.default' => $mailer,
            'mail.mailers.smtp.host' => SystemSetting::getValue('mail_host', config('mail.mailers.smtp.host')),
            'mail.mailers.smtp.port' => (int) SystemSetting::getValue('mail_port', (string) config('mail.mailers.smtp.port')),
            'mail.mailers.smtp.username' => SystemSetting::getValue('mail_username', config('mail.mailers.smtp.username')),
            'mail.mailers.smtp.password' => SystemSetting::getValue('mail_password', config('mail.mailers.smtp.password')),
            'mail.mailers.smtp.encryption' => SystemSetting::getValue('mail_encryption', config('mail.mailers.smtp.encryption')),
            'mail.mailers.sendmail.path' => SystemSetting::getValue('mail_sendmail_path', config('mail.mailers.sendmail.path')),
            'mail.from.address' => SystemSetting::getValue('mail_from_address', config('mail.from.address')),
            'mail.from.name' => SystemSetting::getValue('mail_from_name', config('mail.from.name')),
        ]);
    }
}
