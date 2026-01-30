<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Development Team
        $devTeam = Team::create([
            'name' => 'Development Team',
            'description' => 'Handles all software development tasks',
            'is_active' => true,
        ]);

        // Create Design Team
        $designTeam = Team::create([
            'name' => 'Design Team',
            'description' => 'Handles UI/UX design and branding',
            'is_active' => true,
        ]);

        // Assign team members to teams
        $john = User::where('email', 'john@asynchronousdigital.com')->first();
        $sarah = User::where('email', 'sarah@asynchronousdigital.com')->first();

        if ($john) {
            $devTeam->users()->attach($john->id, ['joined_at' => now()]);
        }

        if ($sarah) {
            $designTeam->users()->attach($sarah->id, ['joined_at' => now()]);
        }
    }
}
