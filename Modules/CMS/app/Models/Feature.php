<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Feature extends Model
{
    protected $fillable = [
        'page',
        'section',
        'title',
        'description',
        'icon',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: Active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By page
     */
    public function scopeForPage($query, $page)
    {
        return $query->where('page', $page);
    }

    /**
     * Scope: By section
     */
    public function scopeForSection($query, $section)
    {
        return $query->where('section', $section);
    }

    /**
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get features by page and optional section
     */
    public static function getByPage($page, $section = null)
    {
        $query = static::active()
            ->forPage($page)
            ->ordered();

        if ($section) {
            $query->forSection($section);
        }

        return $query->get();
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return image_url($this->image);
    }

    /**
     * Clear all feature cache keys
     */
    public static function clearCache($page = null, $section = null): void
    {
        $pages = $page ? [$page] : ['home', 'about', 'menu', 'contact', 'reservation', 'services'];
        $sections = $section ? [$section] : [null, 'hero', 'features', 'services', 'benefits'];

        foreach ($pages as $p) {
            foreach ($sections as $s) {
                Cache::forget("cms_features_{$p}_{$s}");
            }
        }
    }

    /**
     * Clear cache when saved or deleted
     */
    protected static function booted()
    {
        static::saved(function ($feature) {
            self::clearCache($feature->page, $feature->section);
        });

        static::deleted(function ($feature) {
            self::clearCache($feature->page, $feature->section);
        });
    }
}
