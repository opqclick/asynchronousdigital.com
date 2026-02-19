<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_role_permissions(): void
    {
        $this->seed();

        $adminRole = Role::where('name', Role::ADMIN)->firstOrFail();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $admin->syncRolesWithRules([$adminRole->id]);

        $teamMemberRole = Role::where('name', Role::TEAM_MEMBER)->firstOrFail();
        $permission = Permission::where('name', 'tasks.manage_own')->firstOrFail();

        $response = $this->actingAs($admin)->put(route('admin.permissions.roles.update', $teamMemberRole), [
            'permission_ids' => [$permission->id],
        ]);

        $response->assertRedirect(route('admin.permissions.roles.index'));
        $this->assertTrue($teamMemberRole->fresh()->permissionItems()->where('permissions.id', $permission->id)->exists());
    }

    public function test_user_deny_override_blocks_role_granted_permission(): void
    {
        $this->seed();

        $adminRole = Role::where('name', Role::ADMIN)->firstOrFail();
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $admin->syncRolesWithRules([$adminRole->id]);

        $projectManagerRole = Role::where('name', Role::PROJECT_MANAGER)->firstOrFail();
        $permission = Permission::where('name', 'tasks.manage')->firstOrFail();

        $projectManagerRole->permissionItems()->syncWithoutDetaching([$permission->id]);

        $pmUser = User::factory()->create(['role_id' => $projectManagerRole->id]);
        $pmUser->syncRolesWithRules([$projectManagerRole->id]);

        $this->assertTrue($pmUser->fresh()->hasPermission('tasks.manage'));

        $response = $this->actingAs($admin)->put(route('admin.permissions.users.update', $pmUser), [
            'deny_permissions' => [$permission->id],
        ]);

        $response->assertRedirect(route('admin.permissions.users.index'));
        $this->assertFalse($pmUser->fresh()->hasPermission('tasks.manage'));
    }
}
