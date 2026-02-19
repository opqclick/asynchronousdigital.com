<?php

namespace Tests\Feature;

use App\Mail\TaskAssigned;
use App\Models\Client;
use App\Models\Project;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TaskAssignmentEmailNotificationTest extends TestCase
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

    private function createProject(): array
    {
        $admin = $this->createUserWithRole(Role::ADMIN, 'Admin');
        $clientUser = $this->createUserWithRole(Role::CLIENT, 'Client');

        $client = Client::create([
            'user_id' => $clientUser->id,
            'company_name' => 'Acme Corp',
            'contact_person' => 'John Client',
            'email' => $clientUser->email,
        ]);

        $project = Project::create([
            'client_id' => $client->id,
            'name' => 'Website Revamp',
            'status' => 'active',
            'billing_model' => 'fixed_price',
            'start_date' => now()->toDateString(),
        ]);

        return [$admin, $project];
    }

    public function test_assignee_gets_email_on_task_create(): void
    {
        Mail::fake();

        [$admin, $project] = $this->createProject();
        $assignee = $this->createUserWithRole(Role::TEAM_MEMBER, 'Team Member');

        $response = $this->actingAs($admin)->post(route('admin.tasks.store'), [
            'title' => 'Implement Email Notification',
            'project_id' => $project->id,
            'status' => 'to_do',
            'priority' => 'medium',
            'users' => [$assignee->id],
        ]);

        $response->assertRedirect(route('admin.tasks.index'));

        Mail::assertQueued(TaskAssigned::class, function (TaskAssigned $mail) use ($assignee) {
            return $mail->hasTo($assignee->email)
                && $mail->task->title === 'Implement Email Notification';
        });
        Mail::assertQueued(TaskAssigned::class, 1);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $assignee->id,
            'notifiable_type' => User::class,
            'type' => TaskAssignedNotification::class,
        ]);
    }

    public function test_only_new_assignees_get_email_on_task_reassignment(): void
    {
        Mail::fake();

        [$admin, $project] = $this->createProject();
        $existingAssignee = $this->createUserWithRole(Role::TEAM_MEMBER, 'Team Member');
        $newAssignee = $this->createUserWithRole(Role::TEAM_MEMBER, 'Team Member');

        $task = Task::create([
            'title' => 'Prepare Release Notes',
            'project_id' => $project->id,
            'created_by' => $admin->id,
            'status' => 'to_do',
            'priority' => 'high',
        ]);
        $task->users()->attach($existingAssignee->id);

        $response = $this->actingAs($admin)->put(route('admin.tasks.update', $task), [
            'title' => 'Prepare Release Notes',
            'project_id' => $project->id,
            'status' => 'to_do',
            'priority' => 'high',
            'users' => [$existingAssignee->id, $newAssignee->id],
        ]);

        $response->assertRedirect(route('admin.tasks.index'));

        Mail::assertQueued(TaskAssigned::class, function (TaskAssigned $mail) use ($newAssignee) {
            return $mail->hasTo($newAssignee->email);
        });

        Mail::assertNotQueued(TaskAssigned::class, function (TaskAssigned $mail) use ($existingAssignee) {
            return $mail->hasTo($existingAssignee->email);
        });

        Mail::assertQueued(TaskAssigned::class, 1);

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $newAssignee->id,
            'notifiable_type' => User::class,
            'type' => TaskAssignedNotification::class,
        ]);
    }

    public function test_email_notification_can_be_disabled_from_system_settings(): void
    {
        Mail::fake();

        [$admin, $project] = $this->createProject();
        $assignee = $this->createUserWithRole(Role::TEAM_MEMBER, 'Team Member');

        SystemSetting::setMany([
            'notification_in_app_enabled' => '1',
            'notification_email_enabled' => '0',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.tasks.store'), [
            'title' => 'In App Only Task',
            'project_id' => $project->id,
            'status' => 'to_do',
            'priority' => 'medium',
            'users' => [$assignee->id],
        ]);

        $response->assertRedirect(route('admin.tasks.index'));

        Mail::assertNothingQueued();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $assignee->id,
            'notifiable_type' => User::class,
            'type' => TaskAssignedNotification::class,
        ]);
    }
}
