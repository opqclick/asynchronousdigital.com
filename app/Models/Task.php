<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'order',
        'attachments',
    ];

    protected $casts = [
        'due_date' => 'date',
        'attachments' => 'array',
    ];

    /**
     * Get the project for this task
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get users assigned to this task
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot('assigned_at');
    }

    /**
     * Get comments for this task
     */
    public function comments()
    {
        return $this->hasMany(TaskComment::class)->whereNull('parent_id')->with('user', 'replies')->latest();
    }

    /**
     * Get teams assigned to this task
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withTimestamps()
            ->withPivot('assigned_at');
    }
}
