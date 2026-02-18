<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access with all permissions',
            ],
            [
                'name' => 'project_manager',
                'display_name' => 'Project Manager',
                'description' => 'Manages clients, projects, tasks, teams, and billing operations',
            ],
            [
                'name' => 'team_member',
                'display_name' => 'Team Member',
                'description' => 'Access to assigned tasks and projects',
            ],
            [
                'name' => 'client',
                'display_name' => 'Client',
                'description' => 'Read-only access to own projects and invoices',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
