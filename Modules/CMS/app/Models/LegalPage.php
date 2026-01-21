<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LegalPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get page by slug
     */
    public static function getBySlug($slug)
    {
        return static::where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Scope: Active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get meta title (fallback to title)
     */
    public function getMetaTitleAttribute($value)
    {
        return $value ?: $this->title;
    }

    /**
     * Clear cache when saved
     */
    protected static function booted()
    {
        static::saved(function ($page) {
            Cache::forget("cms_legal_{$page->slug}");
        });

        static::deleted(function ($page) {
            Cache::forget("cms_legal_{$page->slug}");
        });
    }
}
