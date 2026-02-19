<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    private string $envPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->envPath = storage_path('framework/testing/.env.settings.test');

        if (!is_dir(dirname($this->envPath))) {
            mkdir(dirname($this->envPath), 0777, true);
        }

        file_put_contents($this->envPath, implode(PHP_EOL, [
            'APP_NAME=AsynchronousDigital',
            'MAIL_MAILER=smtp',
            'MAIL_HOST=mailhog',
            'MAIL_PASSWORD=existing-secret',
            'DO_SPACES_KEY=existing-key',
            'DO_SPACES_SECRET=existing-do-secret',
            '',
        ]));

        config(['system_settings.env_path' => $this->envPath]);
    }

    protected function tearDown(): void
    {
        if (is_file($this->envPath)) {
            unlink($this->envPath);
        }

        parent::tearDown();
    }

    private function createAdmin(): User
    {
        $role = Role::firstOrCreate(
            ['name' => Role::ADMIN],
            ['display_name' => 'Admin', 'description' => 'Admin']
        );

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_admin_can_update_system_settings(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'notification_in_app_enabled' => '1',
            'notification_email_enabled' => '0',
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.example.com',
            'mail_port' => 2525,
            'mail_username' => 'mailer-user',
            'mail_password' => 'secret-pass',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'no-reply@example.com',
            'mail_from_name' => 'Asynchronous Digital',
            'env' => [
                'DO_SPACES_KEY' => 'do-key-123',
                'DO_SPACES_SECRET' => 'do-secret-xyz',
                'DO_SPACES_REGION' => 'sgp1',
                'DO_SPACES_BUCKET' => 'bucket-one',
                'DO_SPACES_ENDPOINT' => 'https://sgp1.digitaloceanspaces.com',
                'DO_SPACES_URL' => 'https://bucket-one.sgp1.digitaloceanspaces.com',
                'DO_SPACES_ROOT' => 'AsynchronousDigitalCRM',
                'DO_SPACES_VISIBILITY' => 'public',
            ],
        ]);

        $response->assertRedirect(route('admin.settings.edit'));

        $this->assertSame('0', SystemSetting::getValue('notification_email_enabled'));
        $this->assertSame('smtp.example.com', SystemSetting::getValue('mail_host'));
        $this->assertSame('2525', SystemSetting::getValue('mail_port'));
        $this->assertSame('no-reply@example.com', SystemSetting::getValue('mail_from_address'));

        $envContents = file_get_contents($this->envPath);
        $this->assertStringContainsString('MAIL_HOST=smtp.example.com', $envContents);
        $this->assertStringContainsString('MAIL_PORT=2525', $envContents);
        $this->assertStringContainsString('DO_SPACES_KEY=do-key-123', $envContents);
        $this->assertStringContainsString('DO_SPACES_SECRET=do-secret-xyz', $envContents);
    }

    public function test_admin_can_update_general_env_values_from_dashboard(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'notification_in_app_enabled' => '1',
            'notification_email_enabled' => '1',
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.example.com',
            'mail_port' => 1025,
            'mail_username' => 'mailer-user',
            'mail_password' => '',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'admin@example.com',
            'mail_from_name' => 'Admin',
            'env' => [
                'APP_NAME' => 'Async Digital Platform',
                'APP_ENV' => 'production',
                'DO_SPACES_KEY' => 'existing-key',
                'DO_SPACES_SECRET' => '',
                'DO_SPACES_REGION' => 'nyc3',
                'DO_SPACES_BUCKET' => 'bucket-two',
                'DO_SPACES_ENDPOINT' => 'https://nyc3.digitaloceanspaces.com',
                'DO_SPACES_URL' => 'https://bucket-two.nyc3.digitaloceanspaces.com',
                'DO_SPACES_ROOT' => 'RootTwo',
                'DO_SPACES_VISIBILITY' => 'private',
            ],
        ]);

        $response->assertRedirect(route('admin.settings.edit'));

        $envContents = file_get_contents($this->envPath);
        $this->assertStringContainsString('APP_NAME="Async Digital Platform"', $envContents);
        $this->assertStringContainsString('APP_ENV=production', $envContents);
        $this->assertStringContainsString('MAIL_PASSWORD=existing-secret', $envContents);
        $this->assertStringContainsString('DO_SPACES_KEY=existing-key', $envContents);
        $this->assertStringContainsString('DO_SPACES_SECRET=existing-do-secret', $envContents);
        $this->assertStringContainsString('DO_SPACES_REGION=nyc3', $envContents);
        $this->assertStringContainsString('DO_SPACES_VISIBILITY=private', $envContents);
    }
}
