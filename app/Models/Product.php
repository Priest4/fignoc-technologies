<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'tag', 'status', 'description', 'headline',
        'features', 'who_for', 'work_slug', 'external_url', 'detail',
        'screenshot_path', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features'  => 'array',
        'who_for'   => 'array',
        'detail'    => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
