<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'slug', 'title', 'excerpt', 'cover_path', 'body', 'author',
        'read_minutes', 'published_at', 'is_published',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at');
    }

    /** Markdown body rendered to HTML at display time. */
    public function getBodyHtmlAttribute(): string
    {
        return Str::markdown($this->body ?? '');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Editorial topic label for Insights UI (derived — no CMS field yet). */
    public function getTopicAttribute(): string
    {
        $hay = strtolower(($this->slug ?? '') . ' ' . ($this->title ?? ''));

        return match (true) {
            str_contains($hay, 'ecommerce') || str_contains($hay, 'paynow') || str_contains($hay, 'ecocash') => 'Ecommerce',
            str_contains($hay, 'ngo') => 'NGO',
            str_contains($hay, 'aeo') && str_contains($hay, 'seo') => 'AEO · SEO · GEO',
            str_contains($hay, 'geo') => 'GEO',
            str_contains($hay, 'aeo') || str_contains($hay, 'answer-engine') || str_contains($hay, 'ai-answer') || str_contains($hay, 'ai-overview') => 'AEO',
            str_contains($hay, 'seo') => 'SEO',
            default => 'Insight',
        };
    }

    /** Public URL for the cover image, or null when unset. */
    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_path) {
            return null;
        }

        return asset(ltrim($this->cover_path, '/'));
    }
}
