<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'service_interest',
        'budget_range',
        'status',
        'assigned_to',
        'internal_notes',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new')->orderBy('created_at', 'desc');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at')->orderBy('created_at', 'desc');
    }

    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update([
                'read_at' => now(),
                'status' => 'read',
            ]);
        }
    }
}
