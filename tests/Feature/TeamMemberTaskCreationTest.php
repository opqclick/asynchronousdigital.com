<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamMemberTaskCreationTest extends TestCase
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

    private function createClientRecord(): Client
    {
        $clientUser = $this->createUserWithRole(Role::CLIENT, 'Client');

        return Client::create([
            'user_id' => $clientUser->id,
            'company_name' => 'Acme Corp',
            'contact_person' => 'John Client',
            'email' => $clientUser->email,
        ]);
    }

    private function createProject(Client $client, User $projectManager, string $name): Project
    {
        return Project::create([
            'client_id' => $client->id,
            'project_manager_id' => $projectManager->id,
            'name' => $name,
            'status' => 'active',
            'billing_model' => 'fixed_price',
            'start_date' => now()->toDateString(),
        ]);
    }

    public function test_team_member_can_create_task_in_assigned_project_only(): void
    {
        $teamMember = $this->createUserWithRole(Role::TEAM_MEMBER, 'Team Member');
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $client = $this->createClientRecord();

        $assignedProject = $this->createProject($client, $projectManager, 'Assigned Project');
        $unassignedProject = $this->createProject($client, $projectManager, 'Unassigned Project');

        $team = Team::create(['name' => 'Alpha Team']);
        $team->users()->attach($teamMember->id, ['joined_at' => now()]);
        $team->projects()->attach($assignedProject->id, ['assigned_at' => now()]);

        $allowedResponse = $this->actingAs($teamMember)->post(route('team-member.tasks.store'), [
            'title' => 'Team Member Allowed Task',
            'project_id' => $assignedProject->id,
            'priority' => 'medium',
        ]);

        $allowedResponse->assertRedirect(route('team-member.dashboard'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Team Member Allowed Task',
            'project_id' => $assignedProject->id,
            'status' => 'to_do',
            'created_by' => $teamMember->id,
        ]);

        $taskId = Task::where('title', 'Team Member Allowed Task')->value('id');
        $this->assertDatabaseHas('task_user', [
            'task_id' => $taskId,
            'user_id' => $teamMember->id,
        ]);

        $forbiddenResponse = $this->actingAs($teamMember)->post(route('team-member.tasks.store'), [
            'title' => 'Team Member Forbidden Task',
            'project_id' => $unassignedProject->id,
            'priority' => 'high',
        ]);

        $forbiddenResponse->assertForbidden();
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Team Member Forbidden Task',
            'project_id' => $unassignedProject->id,
        ]);
    }

    public function test_team_member_create_page_shows_only_assigned_projects(): void
    {
        $teamMember = $this->createUserWithRole(Role::TEAM_MEMBER, 'Team Member');
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $client = $this->createClientRecord();

        $assignedProject = $this->createProject($client, $projectManager, 'Assigned Project');
        $unassignedProject = $this->createProject($client, $projectManager, 'Unassigned Project');

        $team = Team::create(['name' => 'Alpha Team']);
        $team->users()->attach($teamMember->id, ['joined_at' => now()]);
        $team->projects()->attach($assignedProject->id, ['assigned_at' => now()]);

        $response = $this->actingAs($teamMember)->get(route('team-member.tasks.create'));

        $response->assertOk();
        $response->assertSee($assignedProject->name);
        $response->assertDontSee($unassignedProject->name);
    }
}
