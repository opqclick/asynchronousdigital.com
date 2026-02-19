<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamContent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'role_title',
        'bio',
        'image_url',
        'display_order',
        'is_published',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('display_order');
    }
}
