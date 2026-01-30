<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title',
        'icon',
        'short_description',
        'full_description',
        'pricing_model',
        'base_price',
        'price_display',
        'features',
        'order',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
