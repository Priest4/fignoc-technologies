<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A Work / portfolio case study (brief §7.4–7.5). Distinct from a Product:
 * this is the *build story*. Cross-links to a Product via `product_slug`.
 */
class PortfolioItem extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'status', 'description', 'summary',
        'technologies', 'image_path', 'project_url', 'product_slug', 'detail',
        'is_featured', 'is_coming_soon', 'sort_order',
    ];

    protected $casts = [
        'technologies'   => 'array',
        'detail'         => 'array',
        'is_featured'    => 'boolean',
        'is_coming_soon' => 'boolean',
    ];

    /** Returns all items ordered — the Work index intentionally shows in-dev too. */
    public function scopeActive($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->orderBy('sort_order');
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
