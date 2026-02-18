<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $teamMemberRole = Role::where('name', 'team_member')->first();
        $clientRole = Role::where('name', 'client')->first();

        // Create Admin User (only if doesn't exist)
        if (!User::where('email', 'admin@asynchronousdigital.com')->exists()) {
            $admin = User::create([
                'role_id' => $adminRole->id,
                'active_role_id' => $adminRole->id,
                'name' => 'Admin User',
                'email' => 'admin@asynchronousdigital.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'is_active' => true,
            ]);

            $admin->syncRolesWithRules([$adminRole->id], $adminRole->id);
        }

        // Create Sample Team Members
        $john = User::create([
            'role_id' => $teamMemberRole->id,
            'active_role_id' => $teamMemberRole->id,
            'name' => 'John Developer',
            'email' => 'john@asynchronousdigital.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567891',
            'payment_model' => 'monthly',
            'monthly_salary' => 5000.00,
            'is_active' => true,
        ]);
        $john->syncRolesWithRules([$teamMemberRole->id], $teamMemberRole->id);

        $sarah = User::create([
            'role_id' => $teamMemberRole->id,
            'active_role_id' => $teamMemberRole->id,
            'name' => 'Sarah Designer',
            'email' => 'sarah@asynchronousdigital.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567892',
            'payment_model' => 'monthly',
            'monthly_salary' => 4500.00,
            'is_active' => true,
        ]);
        $sarah->syncRolesWithRules([$teamMemberRole->id], $teamMemberRole->id);

        // Create Sample Client User
        $clientUser = User::create([
            'role_id' => $clientRole->id,
            'active_role_id' => $clientRole->id,
            'name' => 'Demo Client',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567893',
            'is_active' => true,
        ]);
        $clientUser->syncRolesWithRules([$clientRole->id], $clientRole->id);

        // Create Client Profile
        $clientUser->client()->create([
            'company_name' => 'Example Corp',
            'contact_person' => 'Demo Client',
            'email' => 'client@example.com',
            'phone' => '+1234567893',
            'address' => '123 Business St, City, State 12345',
            'website' => 'https://example.com',
            'is_active' => true,
        ]);
    }
}
