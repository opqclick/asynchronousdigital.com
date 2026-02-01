<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->command->error('Admin role not found. Please run RoleSeeder first.');
            return;
        }

        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@asynchronousdigital.com')->first();

        if ($existingAdmin) {
            $this->command->info('Admin user already exists.');
            return;
        }

        // Create admin user
        User::create([
            'role_id' => $adminRole->id,
            'name' => 'Admin',
            'email' => 'admin@asynchronousdigital.com',
            'password' => Hash::make('password'), // Change this in production!
            'phone' => '+1234567890',
            'address' => 'Remote / Cloud-based',
            'date_of_birth' => null,
            'joining_date' => now(),
            'profile_picture' => null,
            'documents' => null,
            'bank_name' => null,
            'bank_account_number' => null,
            'bank_account_holder' => null,
            'bank_routing_number' => null,
            'bank_swift_code' => null,
            'payment_model' => 'monthly',
            'monthly_salary' => 0,
            'is_active' => true,
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@asynchronousdigital.com');
        $this->command->warn('Password: password (Please change this immediately!)');
    }
}
