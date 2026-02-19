<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberTaskActivityNotificationHierarchyTest extends TestCase
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

    private function createProject(User $projectManager): Project
    {
        $clientUser = $this->createUserWithRole(Role::CLIENT, 'Client');

        $client = Client::create([
            'user_id' => $clientUser->id,
            'company_name' => 'Acme Corp',
            'contact_person' => 'John Client',
            'email' => $clientUser->email,
        ]);

        return Project::create([
            'client_id' => $client->id,
            'project_manager_id' => $projectManager->id,
            'name' => 'Notification Scope Project',
            'status' => 'active',
            'billing_model' => 'fixed_price',
            'start_date' => now()->toDateString(),
        ]);
    }

    public function test_team_member_task_status_change_notifies_admin_and_project_manager_only(): void
    {
        SystemSetting::setValue('notification_in_app_enabled', '1');

        $admin = $this->createUserWithRole(Role::ADMIN, 'Admin');
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $otherProjectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $teamMember = $this->createUserWithRole(Role::TEAM_MEMBER, 'Team Member');

        $project = $this->createProject($projectManager);

        $task = Task::create([
            'title' => 'Hierarchy Notification Task',
            'project_id' => $project->id,
            'created_by' => $teamMember->id,
            'status' => 'to_do',
            'priority' => 'medium',
        ]);

        $task->users()->attach($teamMember->id);

        $response = $this->actingAs($teamMember)->post(route('team-member.tasks.update-status', $task), [
            'status' => 'in_progress',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $admin->id,
            'notifiable_type' => User::class,
            'type' => TaskActivityNotification::class,
        ]);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $projectManager->id,
            'notifiable_type' => User::class,
            'type' => TaskActivityNotification::class,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $otherProjectManager->id,
            'notifiable_type' => User::class,
            'type' => TaskActivityNotification::class,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'notifiable_id' => $teamMember->id,
            'notifiable_type' => User::class,
            'type' => TaskActivityNotification::class,
        ]);
    }
}
