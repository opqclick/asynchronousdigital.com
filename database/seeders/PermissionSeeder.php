<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionNames = Role::allPermissions();

        foreach ($permissionNames as $permissionName) {
            Permission::updateOrCreate(
                ['name' => $permissionName],
                [
                    'display_name' => ucwords(str_replace(['.', '_'], ' ', $permissionName)),
                    'description' => 'Permission for ' . str_replace('.', ' ', $permissionName),
                ]
            );
        }

        $permissionsByName = Permission::query()->pluck('id', 'name');

        foreach (Role::all() as $role) {
            $defaultPermissions = Role::PERMISSIONS[$role->name] ?? [];

            if (in_array('*', $defaultPermissions, true)) {
                $role->permissionItems()->sync(Permission::query()->pluck('id')->all());
                continue;
            }

            $permissionIds = collect($defaultPermissions)
                ->map(fn (string $permissionName) => $permissionsByName[$permissionName] ?? null)
                ->filter()
                ->values()
                ->all();

            $role->permissionItems()->sync($permissionIds);
        }
    }
}
