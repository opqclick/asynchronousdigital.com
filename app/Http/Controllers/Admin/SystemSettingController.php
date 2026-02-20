<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Support\EnvEditor;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    private const SECRET_ENV_KEY_PATTERNS = [
        '/PASSWORD$/',
        '/SECRET$/',
        '/^APP_KEY$/',
        '/TOKEN/',
    ];

    public function edit(): View
    {
        $settings = [
            'notification_in_app_enabled' => SystemSetting::getBool('notification_in_app_enabled', true),
            'notification_email_enabled' => SystemSetting::getBool('notification_email_enabled', true),
            'mail_mailer' => SystemSetting::getValue('mail_mailer', config('mail.default', 'smtp')),
            'mail_host' => SystemSetting::getValue('mail_host', config('mail.mailers.smtp.host')),
            'mail_port' => SystemSetting::getValue('mail_port', (string) config('mail.mailers.smtp.port')),
            'mail_username' => SystemSetting::getValue('mail_username', config('mail.mailers.smtp.username')),
            'mail_password' => SystemSetting::getValue('mail_password', config('mail.mailers.smtp.password')),
            'mail_encryption' => SystemSetting::getValue('mail_encryption', config('mail.mailers.smtp.encryption')),
            'mail_from_address' => SystemSetting::getValue('mail_from_address', config('mail.from.address')),
            'mail_from_name' => SystemSetting::getValue('mail_from_name', config('mail.from.name')),
            'mail_sendmail_path' => SystemSetting::getValue('mail_sendmail_path', config('mail.mailers.sendmail.path')),
        ];

        $envEditor = new EnvEditor((string) config('system_settings.env_path', base_path('.env')));
        $envSettings = $envEditor->all();

        $doSpaces = [
            'do_spaces_key' => $envSettings['DO_SPACES_KEY'] ?? '',
            'do_spaces_secret' => $envSettings['DO_SPACES_SECRET'] ?? '',
            'do_spaces_region' => $envSettings['DO_SPACES_REGION'] ?? '',
            'do_spaces_bucket' => $envSettings['DO_SPACES_BUCKET'] ?? '',
            'do_spaces_endpoint' => $envSettings['DO_SPACES_ENDPOINT'] ?? '',
            'do_spaces_url' => $envSettings['DO_SPACES_URL'] ?? '',
            'do_spaces_root' => $envSettings['DO_SPACES_ROOT'] ?? '',
            'do_spaces_visibility' => $envSettings['DO_SPACES_VISIBILITY'] ?? '',
        ];

        return view('admin.settings.index', [
            'settings' => $settings,
            'envSettings' => $envSettings,
            'doSpaces' => $doSpaces,
            'secretEnvPatterns' => self::SECRET_ENV_KEY_PATTERNS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $section = (string) $request->input('save_section', 'all');
        $allowedSections = ['all', 'notifications', 'smtp', 'do_spaces', 'env'];
        if (!in_array($section, $allowedSections, true)) {
            $section = 'all';
        }

        $smtpRequired = in_array($section, ['all', 'smtp'], true);

        $validated = $request->validate([
            'notification_in_app_enabled' => ['nullable', 'boolean'],
            'notification_email_enabled' => ['nullable', 'boolean'],
            'mail_mailer' => $smtpRequired ? ['required', 'string', 'max:50'] : ['nullable', 'string', 'max:50'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'string', 'max:50'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
            'mail_sendmail_path' => ['nullable', 'string', 'max:255'],
            'do_spaces_key' => ['nullable', 'string', 'max:255'],
            'do_spaces_secret' => ['nullable', 'string', 'max:255'],
            'do_spaces_region' => ['nullable', 'string', 'max:50'],
            'do_spaces_bucket' => ['nullable', 'string', 'max:255'],
            'do_spaces_endpoint' => ['nullable', 'url', 'max:255'],
            'do_spaces_url' => ['nullable', 'url', 'max:255'],
            'do_spaces_root' => ['nullable', 'string', 'max:255'],
            'do_spaces_visibility' => ['nullable', 'string', 'max:50'],
            'env' => ['nullable', 'array'],
            'env.*' => ['nullable', 'string'],
        ]);

        $envEditor = new EnvEditor((string) config('system_settings.env_path', base_path('.env')));
        $currentEnv = $envEditor->all();

        $submittedEnv = [];

        if (in_array($section, ['all', 'env'], true)) {
            $submittedEnv = collect($request->input('env', []))
                ->filter(fn($value, $key) => is_string($key) && preg_match('/^[A-Z0-9_]+$/', $key))
                ->map(function ($value, $key) use ($currentEnv) {
                    $incoming = $value === null ? '' : (string) $value;

                    if ($this->isSecretEnvKey($key) && $incoming === '') {
                        return $currentEnv[$key] ?? '';
                    }

                    return $incoming;
                })
                ->all();
        }

        if (in_array($section, ['all', 'env'], true)) {
            $targetFilesystemDisk = $submittedEnv['FILESYSTEM_DISK']
                ?? ($currentEnv['FILESYSTEM_DISK'] ?? '');

            $filesystemContext = array_merge($currentEnv, $submittedEnv);
            $filesystemContext['DO_SPACES_SECRET'] = $this->resolveSecretValue(
                'DO_SPACES_SECRET',
                $request->input('env.DO_SPACES_SECRET'),
                $currentEnv
            );
            $filesystemContext['AWS_SECRET_ACCESS_KEY'] = $this->resolveSecretValue(
                'AWS_SECRET_ACCESS_KEY',
                $request->input('env.AWS_SECRET_ACCESS_KEY'),
                $currentEnv
            );

            if (
                $targetFilesystemDisk === 'do_spaces' && !$this->isProviderConfigured($filesystemContext, [
                    'DO_SPACES_KEY',
                    'DO_SPACES_SECRET',
                    'DO_SPACES_REGION',
                    'DO_SPACES_BUCKET',
                    'DO_SPACES_ENDPOINT',
                ])
            ) {
                return redirect()
                    ->route('admin.settings.edit')
                    ->withInput()
                    ->withErrors(['env.FILESYSTEM_DISK' => 'DigitalOcean Spaces cannot be selected until all required credentials are filled.'])
                    ->with('active_tab', (string) $request->input('active_tab', 'pane-notifications'));
            }

            if (
                $targetFilesystemDisk === 's3' && !$this->isProviderConfigured($filesystemContext, [
                    'AWS_ACCESS_KEY_ID',
                    'AWS_SECRET_ACCESS_KEY',
                    'AWS_DEFAULT_REGION',
                    'AWS_BUCKET',
                ])
            ) {
                return redirect()
                    ->route('admin.settings.edit')
                    ->withInput()
                    ->withErrors(['env.FILESYSTEM_DISK' => 'AWS S3 cannot be selected until all required credentials are filled.'])
                    ->with('active_tab', (string) $request->input('active_tab', 'pane-notifications'));
            }
        }

        if (in_array($section, ['all', 'notifications'], true)) {
            SystemSetting::setMany([
                'notification_in_app_enabled' => $request->boolean('notification_in_app_enabled') ? '1' : '0',
                'notification_email_enabled' => $request->boolean('notification_email_enabled') ? '1' : '0',
            ]);
        }

        if (in_array($section, ['all', 'smtp'], true)) {
            $targetMailer = (string) ($validated['mail_mailer'] ?? ($currentEnv['MAIL_MAILER'] ?? ''));

            $mailPasswordForValidation = $this->resolveSecretValue(
                'MAIL_PASSWORD',
                $validated['mail_password'] ?? null,
                $currentEnv
            );

            $smtpContext = [
                'MAIL_HOST' => trim((string) ($validated['mail_host'] ?? ($currentEnv['MAIL_HOST'] ?? ''))),
                'MAIL_PORT' => trim((string) (isset($validated['mail_port']) ? (string) $validated['mail_port'] : ($currentEnv['MAIL_PORT'] ?? ''))),
                'MAIL_USERNAME' => trim((string) ($validated['mail_username'] ?? ($currentEnv['MAIL_USERNAME'] ?? ''))),
                'MAIL_PASSWORD' => trim((string) $mailPasswordForValidation),
                'MAIL_FROM_ADDRESS' => trim((string) ($validated['mail_from_address'] ?? ($currentEnv['MAIL_FROM_ADDRESS'] ?? ''))),
            ];

            if (
                $targetMailer === 'smtp' && !$this->isProviderConfigured($smtpContext, [
                    'MAIL_HOST',
                    'MAIL_PORT',
                    'MAIL_USERNAME',
                    'MAIL_PASSWORD',
                    'MAIL_FROM_ADDRESS',
                ])
            ) {
                return redirect()
                    ->route('admin.settings.edit')
                    ->withInput()
                    ->withErrors(['mail_mailer' => 'SMTP cannot be selected until required SMTP credentials are filled.'])
                    ->with('active_tab', (string) $request->input('active_tab', 'pane-smtp'));
            }

            $mailPassword = array_key_exists('mail_password', $validated)
                ? $validated['mail_password']
                : null;

            if ($mailPassword === null || $mailPassword === '') {
                $mailPassword = $currentEnv['MAIL_PASSWORD']
                    ?? SystemSetting::getValue('mail_password', config('mail.mailers.smtp.password'));
            }

            SystemSetting::setMany([
                'mail_mailer' => $validated['mail_mailer'],
                'mail_host' => $validated['mail_host'] ?? null,
                'mail_port' => isset($validated['mail_port']) ? (string) $validated['mail_port'] : null,
                'mail_username' => $validated['mail_username'] ?? null,
                'mail_password' => $mailPassword,
                'mail_encryption' => $validated['mail_encryption'] ?? null,
                'mail_from_address' => $validated['mail_from_address'] ?? null,
                'mail_from_name' => $validated['mail_from_name'] ?? null,
                'mail_sendmail_path' => $validated['mail_sendmail_path'] ?? null,
            ]);

            $submittedEnv = array_merge($submittedEnv, [
                'MAIL_MAILER' => $validated['mail_mailer'],
                'MAIL_HOST' => $validated['mail_host'] ?? '',
                'MAIL_PORT' => isset($validated['mail_port']) ? (string) $validated['mail_port'] : '',
                'MAIL_USERNAME' => $validated['mail_username'] ?? '',
                'MAIL_PASSWORD' => $mailPassword ?? '',
                'MAIL_ENCRYPTION' => $validated['mail_encryption'] ?? '',
                'MAIL_FROM_ADDRESS' => $validated['mail_from_address'] ?? '',
                'MAIL_FROM_NAME' => $validated['mail_from_name'] ?? '',
                'MAIL_SENDMAIL_PATH' => $validated['mail_sendmail_path'] ?? '',
            ]);
        }

        if ($section === 'do_spaces') {
            $doSpacesSecret = $validated['do_spaces_secret'] ?? null;
            if ($doSpacesSecret === null || $doSpacesSecret === '') {
                $doSpacesSecret = $currentEnv['DO_SPACES_SECRET'] ?? '';
            }

            $doSpacesKey = $validated['do_spaces_key'] ?? null;
            if ($doSpacesKey === null || $doSpacesKey === '') {
                $doSpacesKey = $currentEnv['DO_SPACES_KEY'] ?? '';
            }

            $submittedEnv = array_merge($submittedEnv, [
                'DO_SPACES_KEY' => $doSpacesKey,
                'DO_SPACES_SECRET' => $doSpacesSecret,
                'DO_SPACES_REGION' => $validated['do_spaces_region'] ?? '',
                'DO_SPACES_BUCKET' => $validated['do_spaces_bucket'] ?? '',
                'DO_SPACES_ENDPOINT' => $validated['do_spaces_endpoint'] ?? '',
                'DO_SPACES_URL' => $validated['do_spaces_url'] ?? '',
                'DO_SPACES_ROOT' => $validated['do_spaces_root'] ?? '',
                'DO_SPACES_VISIBILITY' => $validated['do_spaces_visibility'] ?? '',
            ]);
        }

        if (!empty($submittedEnv)) {
            $envEditor->setMany($submittedEnv);
        }

        try {
            Artisan::call('optimize:clear');
        } catch (\Throwable $exception) {
        }

        $activeTab = (string) $request->input('active_tab', 'pane-notifications');
        if (!preg_match('/^pane-[a-z0-9\-]+$/', $activeTab)) {
            $activeTab = 'pane-notifications';
        }

        $activeTabLabel = trim((string) $request->input('active_tab_label', ''));
        $activeTabLabel = preg_replace('/[^a-zA-Z0-9\s\/_&\-]/', '', $activeTabLabel) ?? '';

        $successMessage = match ($section) {
            'notifications' => 'Notifications section updated successfully.',
            'smtp' => 'Mailer Configuration section updated successfully.',
            'do_spaces' => 'DigitalOcean Spaces section updated successfully.',
            'env' => ($activeTabLabel !== '' ? $activeTabLabel : 'Environment') . ' section updated successfully.',
            default => 'All configuration updated successfully.',
        };

        return redirect()->route('admin.settings.edit')->with([
            'success' => $successMessage,
            'active_tab' => $activeTab,
        ]);
    }

    public function testEmail(Request $request): JsonResponse
    {
        $request->validate([
            'to' => ['required', 'email', 'max:255'],
        ]);

        $to = (string) $request->input('to');

        // Read current saved SMTP settings
        $host = (string) SystemSetting::getValue('mail_host', config('mail.mailers.smtp.host', ''));
        $port = (int) SystemSetting::getValue('mail_port', (string) config('mail.mailers.smtp.port', 587));
        $username = (string) SystemSetting::getValue('mail_username', config('mail.mailers.smtp.username', ''));
        $password = (string) SystemSetting::getValue('mail_password', config('mail.mailers.smtp.password', ''));
        $encryption = (string) SystemSetting::getValue('mail_encryption', config('mail.mailers.smtp.encryption', 'tls'));
        $fromAddr = (string) SystemSetting::getValue('mail_from_address', config('mail.from.address', ''));
        $fromName = (string) SystemSetting::getValue('mail_from_name', config('mail.from.name', config('app.name')));
        $mailer = (string) SystemSetting::getValue('mail_mailer', config('mail.default', 'smtp'));

        if (empty($host) && $mailer === 'smtp') {
            return response()->json(['success' => false, 'message' => 'SMTP host is not configured. Please save your SMTP settings first.'], 422);
        }

        // Patch the runtime mail config so the mailer uses the saved settings
        config([
            'mail.default' => $mailer,
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => $port,
            'mail.mailers.smtp.username' => $username,
            'mail.mailers.smtp.password' => $password,
            'mail.mailers.smtp.encryption' => $encryption ?: null,
            'mail.from.address' => $fromAddr,
            'mail.from.name' => $fromName,
        ]);

        try {
            Mail::raw(
                "This is a test email from " . config('app.name') . ".\n\n" .
                "If you received this, your mail configuration is working correctly.\n\n" .
                "Sent at: " . now()->format('Y-m-d H:i:s T'),
                function ($message) use ($to, $fromAddr, $fromName) {
                    $message->to($to)
                        ->from($fromAddr, $fromName)
                        ->subject('Test Email — ' . config('app.name'));
                }
            );

            return response()->json([
                'success' => true,
                'message' => "Test email sent successfully to {$to}.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ], 422);
        }
    }

    private function isSecretEnvKey(string $key): bool
    {
        foreach (self::SECRET_ENV_KEY_PATTERNS as $pattern) {
            if (preg_match($pattern, $key)) {
                return true;
            }
        }

        return false;
    }

    private function resolveSecretValue(string $key, mixed $submittedValue, array $currentEnv): string
    {
        if (!is_string($submittedValue)) {
            return (string) ($currentEnv[$key] ?? '');
        }

        $incoming = trim($submittedValue);
        if ($incoming === '') {
            return (string) ($currentEnv[$key] ?? '');
        }

        return $submittedValue;
    }

    private function isProviderConfigured(array $context, array $requiredKeys): bool
    {
        foreach ($requiredKeys as $key) {
            $value = trim((string) ($context[$key] ?? ''));
            if ($value === '') {
                return false;
            }
        }

        return true;
    }
}
