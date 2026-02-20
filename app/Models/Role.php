<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
            'projects.view_own',
        ],
        self::CLIENT => [
            'dashboard.view_own',
            'projects.view_own',
            'invoices.view_own',
        ],
    ];

    public const EXTRA_PERMISSIONS = [
        'clients.manage',
        'teams.manage',
        'invoices.manage',
        'payments.manage',
        'salaries.manage',
        'users.manage',
        'settings.manage',
        'permissions.manage',
        'user-activities.view',
        'user-activities.edit',
        'user-activities.delete',
        'user-activities.restore',
        'services.manage',
        'team-content.manage',
        'testimonials.manage',
        'contact-messages.manage',
        'recycle-bin.view',
        'recycle-bin.restore',
    ];

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Get users with this role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')->withTimestamps();
    }

    /**
     * Get permissions assigned to this role.
     */
    public function permissionItems(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
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

        foreach (self::EXTRA_PERMISSIONS as $permission) {
            $permissions[$permission] = true;
        }

        return array_keys($permissions);
    }

    /**
     * Get permissions for this role.
     */
    public function permissions(): array
    {
        $dbPermissions = $this->permissionItems()->pluck('permissions.name')->all();

        if (!empty($dbPermissions)) {
            return $dbPermissions;
        }

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
