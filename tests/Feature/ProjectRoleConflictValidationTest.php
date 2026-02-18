<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRoleConflictValidationTest extends TestCase
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

    public function test_admin_cannot_assign_same_user_as_pm_and_team_member_on_project_create(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN, 'Administrator');
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');

        $team = Team::create(['name' => 'Alpha Team']);
        $team->users()->attach($projectManager->id, ['joined_at' => now()]);

        $client = $this->createClientRecord();

        $response = $this->actingAs($admin)
            ->from(route('admin.projects.create'))
            ->post(route('admin.projects.store'), [
                'name' => 'Conflict Project',
                'client_id' => $client->id,
                'project_manager_id' => $projectManager->id,
                'status' => 'active',
                'billing_model' => 'fixed_price',
                'start_date' => now()->toDateString(),
                'teams' => [$team->id],
            ]);

        $response->assertRedirect(route('admin.projects.create'));
        $response->assertSessionHasErrors(['project_manager_id', 'teams']);

        $this->assertDatabaseMissing('projects', [
            'name' => 'Conflict Project',
        ]);
    }

    public function test_admin_cannot_add_project_manager_as_team_member_when_team_has_that_project(): void
    {
        $admin = $this->createUserWithRole(Role::ADMIN, 'Administrator');
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $client = $this->createClientRecord();

        $team = Team::create(['name' => 'Alpha Team']);

        $project = Project::create([
            'client_id' => $client->id,
            'project_manager_id' => $projectManager->id,
            'name' => 'Managed Project',
            'status' => 'active',
            'billing_model' => 'fixed_price',
            'start_date' => now()->toDateString(),
        ]);

        $project->teams()->attach($team->id, ['assigned_at' => now()]);

        $response = $this->actingAs($admin)
            ->from(route('admin.teams.edit', $team))
            ->put(route('admin.teams.update', $team), [
                'name' => $team->name,
                'members' => [$projectManager->id],
            ]);

        $response->assertRedirect(route('admin.teams.edit', $team));
        $response->assertSessionHasErrors(['members']);

        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $projectManager->id,
        ]);
    }
}
