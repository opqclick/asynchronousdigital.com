<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'month',
        'base_amount',
        'bonus',
        'deduction',
        'total_amount',
        'status',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the user (team member) for this salary
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project related to this salary (if project-based)
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
