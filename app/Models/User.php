<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected static function booted(): void
    {
        static::created(function (User $user) {
            if ($user->role_id) {
                $user->roles()->syncWithoutDetaching([$user->role_id]);

                if (!$user->active_role_id) {
                    $user->forceFill([
                        'active_role_id' => $user->role_id,
                    ])->saveQuietly();
                }
            }
        });

        static::updated(function (User $user) {
            if ($user->role_id) {
                $user->roles()->syncWithoutDetaching([$user->role_id]);

                if (!$user->active_role_id) {
                    $user->forceFill([
                        'active_role_id' => $user->role_id,
                    ])->saveQuietly();
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'active_role_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'date_of_birth',
        'joining_date',
        'profile_picture',
        'documents',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'bank_routing_number',
        'bank_swift_code',
        'payment_model',
        'monthly_salary',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'monthly_salary' => 'decimal:2',
            'date_of_birth' => 'date',
            'joining_date' => 'date',
            'documents' => 'array',
        ];
    }

    /**
     * Get the role of the user
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'active_role_id');
    }

    /**
     * Get all roles assigned to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    /**
     * Get teams the user belongs to
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withTimestamps()
            ->withPivot('joined_at');
    }

    /**
     * Get tasks assigned to the user
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class)
            ->withTimestamps()
            ->withPivot('assigned_at');
    }

    /**
     * Get the client profile if user is a client
     */
    public function client(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Get salaries for the user
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }

    /**
     * Get projects managed by this user
     */
    public function managedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'project_manager_id');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->hasAssignedRole(Role::ADMIN);
    }

    /**
     * Check if user is project manager
     */
    public function isProjectManager(): bool
    {
        return $this->hasAssignedRole(Role::PROJECT_MANAGER);
    }

    /**
     * Check if user is team member
     */
    public function isTeamMember(): bool
    {
        return $this->hasAssignedRole(Role::TEAM_MEMBER);
    }

    /**
     * Check if user is client
     */
    public function isClient(): bool
    {
        return $this->hasAssignedRole(Role::CLIENT);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->hasAssignedRole($roleName);
    }

    /**
     * Check if user has any of the provided roles.
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->hasAnyAssignedRole($roleNames);
    }

    /**
     * Check if user has a specific role assigned (ignores active context).
     */
    public function hasAssignedRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Check if user has any assigned role (ignores active context).
     */
    public function hasAnyAssignedRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->roles()
            ->get()
            ->contains(fn (Role $role) => $role->hasPermission($permission));
    }

    /**
     * Return all role names assigned to the user.
     */
    public function roleNames(): array
    {
        return $this->roles()->pluck('name')->all();
    }

    /**
     * Sync roles while enforcing business constraints.
     */
    public function syncRolesWithRules(array $roleIds, ?int $activeRoleId = null): void
    {
        $roleIds = array_values(array_unique(array_map('intval', $roleIds)));

        if (count($roleIds) < 1) {
            throw new \InvalidArgumentException('At least one role is required.');
        }

        $assignedRoles = Role::query()->whereIn('id', $roleIds)->get(['id', 'name']);

        if ($assignedRoles->count() !== count($roleIds)) {
            throw new \InvalidArgumentException('One or more selected roles are invalid.');
        }

        $hasClient = $assignedRoles->contains(fn (Role $role) => $role->name === Role::CLIENT);
        if ($hasClient && count($roleIds) > 1) {
            throw new \InvalidArgumentException('Client role must remain exclusive.');
        }

        $activeRoleId = $activeRoleId ?? $this->active_role_id ?? $this->role_id ?? $roleIds[0];
        if (!in_array($activeRoleId, $roleIds, true)) {
            $activeRoleId = $roleIds[0];
        }

        $this->roles()->sync($roleIds);
        $this->forceFill([
            'role_id' => $activeRoleId,
            'active_role_id' => $activeRoleId,
        ])->save();
    }

    /**
     * Switch currently active role context.
     */
    public function switchActiveRole(int $roleId): void
    {
        if (!$this->roles()->where('roles.id', $roleId)->exists()) {
            throw new \InvalidArgumentException('Selected role is not assigned to this user.');
        }

        $this->forceFill([
            'role_id' => $roleId,
            'active_role_id' => $roleId,
        ])->save();
    }

    /**
     * Ensure active role is valid and set.
     */
    public function ensureActiveRoleContext(): void
    {
        $assignedIds = $this->roles()->pluck('roles.id')->all();
        if (empty($assignedIds)) {
            return;
        }

        $activeRoleId = $this->active_role_id ?? $this->role_id;
        if (!$activeRoleId || !in_array($activeRoleId, $assignedIds, true)) {
            $fallbackRoleId = $assignedIds[0];
            $this->forceFill([
                'role_id' => $fallbackRoleId,
                'active_role_id' => $fallbackRoleId,
            ])->save();
        }
    }

    /**
     * Get the profile URL for AdminLTE
     */
    public function adminlte_profile_url(): string
    {
        return route('profile.edit');
    }
}
