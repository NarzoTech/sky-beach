<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class InfoCard extends Model
{
    protected $fillable = [
        'page',
        'title',
        'content',
        'icon',
        'icon_image',
        'link',
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
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get cards by page
     */
    public static function getByPage($page)
    {
        return static::active()
            ->forPage($page)
            ->ordered()
            ->get();
    }

    /**
     * Get icon image URL
     */
    public function getIconImageUrlAttribute()
    {
        return $this->icon_image ? asset($this->icon_image) : null;
    }

    /**
     * Clear cache when saved
     */
    protected static function booted()
    {
        static::saved(function ($card) {
            Cache::forget("cms_info_cards_{$card->page}");
        });

        static::deleted(function ($card) {
            Cache::forget("cms_info_cards_{$card->page}");
        });
    }
}
