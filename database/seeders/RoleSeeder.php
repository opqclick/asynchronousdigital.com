<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            Role::create($role);
        }
    }
}
