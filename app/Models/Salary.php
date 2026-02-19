<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'month',
        'base_amount',
        'bonus',
        'deduction',
        'total_amount',
        'status',
        'is_received',
        'received_at',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'month' => 'date',
        'base_amount' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'payment_date' => 'date',
        'is_received' => 'boolean',
        'received_at' => 'datetime',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($salary) {
            // Auto-calculate total_amount if not provided
            if (!isset($salary->total_amount) || $salary->total_amount === null) {
                $salary->total_amount = $salary->base_amount + ($salary->bonus ?? 0) - ($salary->deduction ?? 0);
            }
        });
    }

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
