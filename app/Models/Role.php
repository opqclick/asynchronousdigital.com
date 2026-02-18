<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const ADMIN = 'admin';
    public const PROJECT_MANAGER = 'project_manager';
    public const TEAM_MEMBER = 'team_member';
    public const CLIENT = 'client';

    public const PERMISSIONS = [
        self::ADMIN => ['*'],
        self::PROJECT_MANAGER => [
            'dashboard.view',
            'projects.manage',
            'tasks.manage',
        ],
        self::TEAM_MEMBER => [
            'dashboard.view',
            'tasks.manage_own',
            'salaries.view_own',
        ],
        self::CLIENT => [
            'dashboard.view_own',
            'projects.view_own',
            'invoices.view_own',
        ],
    ];

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Get users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Return all distinct permissions in the system.
     */
    public static function allPermissions(): array
    {
        $permissions = [];

        foreach (self::PERMISSIONS as $rolePermissions) {
            foreach ($rolePermissions as $permission) {
                if ($permission === '*') {
                    continue;
                }

                $permissions[$permission] = true;
            }
        }

        return array_keys($permissions);
    }

    /**
     * Get permissions for this role.
     */
    public function permissions(): array
    {
        return self::PERMISSIONS[$this->name] ?? [];
    }

    /**
     * Check if this role has a permission.
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions();

        if (in_array('*', $permissions, true)) {
            return true;
        }

        return in_array($permission, $permissions, true);
    }
}
