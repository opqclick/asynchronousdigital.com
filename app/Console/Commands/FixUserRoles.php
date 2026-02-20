<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class FixUserRoles extends Command
{
    protected $signature = 'fix:user-roles';
    protected $description = 'Ensure all users have a valid role_id and active_role_id, and sync user_roles';

    public function handle()
    {
        $defaultRole = Role::where('name', 'admin')->first() ?? Role::first();
        if (!$defaultRole) {
            $this->error('No roles found in the roles table.');
            return 1;
        }

        $users = User::all();
        $fixed = 0;
        foreach ($users as $user) {
            $changed = false;
            if (!$user->role_id) {
                $user->role_id = $defaultRole->id;
                $changed = true;
            }
            if (!$user->active_role_id) {
                $user->active_role_id = $user->role_id;
                $changed = true;
            }
            $user->saveQuietly();
            // Ensure user_roles table is synced
            $user->roles()->syncWithoutDetaching([$user->role_id]);
            if ($changed) {
                $fixed++;
                $this->info("Fixed user: {$user->id} ({$user->name})");
            }
        }
        $this->info("Checked {$users->count()} users. Fixed: {$fixed}");
        return 0;
    }
}
