<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get users in this team
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot('joined_at');
    }

    /**
     * Get projects assigned to this team
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)
            ->withTimestamps()
            ->withPivot('assigned_at');
    }

    /**
     * Get tasks assigned to this team
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class)
            ->withTimestamps()
            ->withPivot('assigned_at');
    }
}
