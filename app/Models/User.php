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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
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
        return $this->belongsTo(Role::class);
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
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role->name === 'admin';
    }

    /**
     * Check if user is team member
     */
    public function isTeamMember(): bool
    {
        return $this->role->name === 'team_member';
    }

    /**
     * Check if user is client
     */
    public function isClient(): bool
    {
        return $this->role->name === 'client';
    }

    /**
     * Get the profile URL for AdminLTE
     */
    public function adminlte_profile_url(): string
    {
        return route('profile.edit');
    }
}
