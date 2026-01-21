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
        return $this->image ? asset($this->image) : null;
    }

    /**
     * Clear cache when saved
     */
    protected static function booted()
    {
        static::saved(function ($feature) {
            Cache::forget("cms_features_{$feature->page}_{$feature->section}");
            Cache::forget("cms_features_{$feature->page}_");
        });

        static::deleted(function ($feature) {
            Cache::forget("cms_features_{$feature->page}_{$feature->section}");
            Cache::forget("cms_features_{$feature->page}_");
        });
    }
}
