<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'tech_stack',
        'start_date',
        'end_date',
        'status',
        'billing_model',
        'project_value',
        'attachments',
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'project_value' => 'decimal:2',
        'attachments' => 'array',
    ];

    /**
     * Get the client for this project
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get teams assigned to this project
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withTimestamps()
            ->withPivot('assigned_at');
    }

    /**
     * Get all tasks for this project
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get all invoices for this project
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get all salaries related to this project
     */
    public function salaries(): HasMany
    {
        return $this->hasMany(Salary::class);
    }
}
