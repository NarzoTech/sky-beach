<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageSection extends Model
{
    protected $fillable = [
        'page',
        'section_key',
        'title',
        'subtitle',
        'content',
        'image',
        'background_image',
        'button_text',
        'button_link',
        'extra_data',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'extra_data' => 'json',
        'is_active' => 'boolean',
    ];

    /**
     * Get section by page and key
     */
    public static function getSection($page, $sectionKey)
    {
        return static::where('page', $page)
            ->where('section_key', $sectionKey)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all sections for a page
     */
    public static function getPageSections($page)
    {
        return static::where('page', $page)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

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
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return image_url($this->image);
    }

    /**
     * Get background image URL
     */
    public function getBackgroundImageUrlAttribute()
    {
        return image_url($this->background_image);
    }

    /**
     * Clear cache when saved
     */
    protected static function booted()
    {
        static::saved(function ($section) {
            Cache::forget("cms_section_{$section->page}_{$section->section_key}");
        });

        static::deleted(function ($section) {
            Cache::forget("cms_section_{$section->page}_{$section->section_key}");
        });
    }
}
