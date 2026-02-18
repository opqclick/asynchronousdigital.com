<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagerPermissionsTest extends TestCase
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

    private function createProjectForManager(User $projectManager, Client $client, string $name = 'Managed Project'): Project
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

    public function test_project_manager_redirects_to_admin_dashboard_from_dashboard_route(): void
    {
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');

        $response = $this
            ->actingAs($projectManager)
            ->get('/dashboard');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_project_manager_can_access_allowed_admin_modules(): void
    {
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');

        $this->actingAs($projectManager)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($projectManager)->get(route('admin.projects.index'))->assertOk();
        $this->actingAs($projectManager)->get(route('admin.tasks.index'))->assertOk();
    }

    public function test_project_manager_cannot_access_admin_only_modules(): void
    {
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');

        $this->actingAs($projectManager)->get(route('admin.clients.index'))->assertForbidden();
        $this->actingAs($projectManager)->get(route('admin.teams.index'))->assertForbidden();
        $this->actingAs($projectManager)->get(route('admin.invoices.index'))->assertForbidden();
        $this->actingAs($projectManager)->get(route('admin.payments.index'))->assertForbidden();
        $this->actingAs($projectManager)->get(route('admin.contact-messages.index'))->assertForbidden();
        $this->actingAs($projectManager)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($projectManager)->get(route('admin.salaries.index'))->assertForbidden();
        $this->actingAs($projectManager)->get(route('admin.user-activities.index'))->assertForbidden();
        $this->actingAs($projectManager)->get(route('admin.services.index'))->assertForbidden();
        $this->actingAs($projectManager)->get(route('admin.testimonials.index'))->assertForbidden();
    }

    public function test_project_manager_only_sees_assigned_projects_in_list(): void
    {
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $otherManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $client = $this->createClientRecord();

        $myProject = $this->createProjectForManager($projectManager, $client, 'PM Owned Project');
        $otherProject = $this->createProjectForManager($otherManager, $client, 'Other PM Project');

        $response = $this->actingAs($projectManager)->get(route('admin.projects.index'));

        $response->assertOk();
        $response->assertSee($myProject->name);
        $response->assertDontSee($otherProject->name);
    }

    public function test_project_manager_can_only_create_tasks_in_assigned_projects(): void
    {
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $otherManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $client = $this->createClientRecord();

        $myProject = $this->createProjectForManager($projectManager, $client, 'PM Owned Project');
        $otherProject = $this->createProjectForManager($otherManager, $client, 'Other PM Project');

        $allowedResponse = $this->actingAs($projectManager)->post(route('admin.tasks.store'), [
            'title' => 'Allowed Task',
            'project_id' => $myProject->id,
            'status' => 'to_do',
            'priority' => 'medium',
        ]);

        $allowedResponse->assertRedirect(route('admin.tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Allowed Task',
            'project_id' => $myProject->id,
        ]);

        $forbiddenResponse = $this->actingAs($projectManager)->post(route('admin.tasks.store'), [
            'title' => 'Forbidden Task',
            'project_id' => $otherProject->id,
            'status' => 'to_do',
            'priority' => 'medium',
        ]);

        $forbiddenResponse->assertForbidden();
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Forbidden Task',
            'project_id' => $otherProject->id,
        ]);
    }

    public function test_project_manager_cannot_create_or_edit_or_delete_projects(): void
    {
        $projectManager = $this->createUserWithRole(Role::PROJECT_MANAGER, 'Project Manager');
        $client = $this->createClientRecord();
        $project = $this->createProjectForManager($projectManager, $client);

        $this->actingAs($projectManager)->get(route('admin.projects.create'))->assertForbidden();

        $storeResponse = $this->actingAs($projectManager)->post(route('admin.projects.store'), [
            'name' => 'Unauthorized Project',
            'client_id' => $client->id,
            'project_manager_id' => $projectManager->id,
            'status' => 'active',
            'billing_model' => 'fixed_price',
            'start_date' => now()->toDateString(),
        ]);

        $storeResponse->assertForbidden();

        $this->actingAs($projectManager)->get(route('admin.projects.edit', $project))->assertForbidden();
        $this->actingAs($projectManager)->put(route('admin.projects.update', $project), [
            'name' => 'Unauthorized Update',
            'client_id' => $client->id,
            'project_manager_id' => $projectManager->id,
            'status' => 'active',
            'billing_model' => 'fixed_price',
            'start_date' => now()->toDateString(),
        ])->assertForbidden();
        $this->actingAs($projectManager)->delete(route('admin.projects.destroy', $project))->assertForbidden();
    }
}
