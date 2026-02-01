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
     * Clear all legal page cache keys
     */
    public static function clearCache($slug = null): void
    {
        if ($slug) {
            Cache::forget("cms_legal_{$slug}");
        } else {
            // Clear common legal page slugs
            $slugs = ['terms-and-conditions', 'privacy-policy', 'refund-policy', 'about-us', 'faq'];
            foreach ($slugs as $s) {
                Cache::forget("cms_legal_{$s}");
            }
        }
    }

    /**
     * Clear cache when saved or deleted
     */
    protected static function booted()
    {
        static::saved(function ($page) {
            self::clearCache($page->slug);
        });

        static::deleted(function ($page) {
            self::clearCache($page->slug);
        });
    }
}
