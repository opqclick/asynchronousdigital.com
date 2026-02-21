<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortfolioItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'client_name',
        'description',
        'image_url',
        'project_url',
        'tech_tags',
        'display_order',
        'is_published',
    ];

    protected $casts = [
        'tech_tags' => 'array',
        'display_order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('display_order');
    }
}
