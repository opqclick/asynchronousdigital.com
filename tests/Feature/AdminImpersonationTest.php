<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminImpersonationTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $roleName, string $displayName): User
    {
        $role = Role::firstOrCreate(
            ['name' => $roleName],
            [
                'display_name' => $displayName,
                'description' => $displayName,
            ]
        );

        return User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

    public function test_admin_can_impersonate_and_return_to_admin_account(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN, 'Administrator');
        $teamMember = $this->createUserWithRole(Role::TEAM_MEMBER, 'Team Member');

        $startResponse = $this->actingAs($admin)->post(route('admin.users.impersonate', $teamMember));

        $startResponse->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($teamMember);
        $this->assertSame($admin->id, session('impersonator_id'));

        $leaveResponse = $this->actingAs($teamMember)->get(route('admin.impersonation.leave'));

        $leaveResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session('impersonator_id'));
    }

    public function test_non_admin_cannot_impersonate_user(): void
    {
        $teamMember = $this->createUserWithRole(Role::TEAM_MEMBER, 'Team Member');
        $client = $this->createUserWithRole(Role::CLIENT, 'Client');

        $response = $this->actingAs($teamMember)->post(route('admin.users.impersonate', $client));

        $response->assertForbidden();
        $this->assertAuthenticatedAs($teamMember);
        $this->assertNull(session('impersonator_id'));
    }

    public function test_admin_cannot_impersonate_another_admin(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN, 'Administrator');
        $otherAdmin = $this->createUserWithRole(Role::ADMIN, 'Administrator');

        $response = $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->post(route('admin.users.impersonate', $otherAdmin));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertAuthenticatedAs($admin);
        $this->assertNull(session('impersonator_id'));
    }
}
