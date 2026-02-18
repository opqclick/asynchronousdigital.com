<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiRoleSwitchTest extends TestCase
{
    use RefreshDatabase;

    private function createRole(string $name, string $displayName): Role
    {
        return Role::firstOrCreate(
            ['name' => $name],
            [
                'display_name' => $displayName,
                'description' => $displayName,
            ]
        );
    }

    public function test_multi_role_user_can_switch_active_role_from_dashboard_flow(): void
    {
        $teamMemberRole = $this->createRole(Role::TEAM_MEMBER, 'Team Member');
        $projectManagerRole = $this->createRole(Role::PROJECT_MANAGER, 'Project Manager');

        /** @var User $user */
        $user = User::factory()->create([
            'role_id' => $teamMemberRole->id,
            'active_role_id' => $teamMemberRole->id,
        ]);

        $user->syncRolesWithRules([$teamMemberRole->id, $projectManagerRole->id], $teamMemberRole->id);

        $response = $this->actingAs($user)->post(route('profile.switch-role'), [
            'active_role_id' => $projectManagerRole->id,
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'active_role_id' => $projectManagerRole->id,
            'role_id' => $projectManagerRole->id,
        ]);
    }
}
