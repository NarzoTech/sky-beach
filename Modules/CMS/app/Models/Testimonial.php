<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'position',
        'company',
        'content',
        'image',
        'rating',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Scope: Active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Featured
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset($this->image) : asset('website/images/default_avatar.png');
    }

    /**
     * Clear all testimonial cache keys
     */
    public static function clearCache(): void
    {
        // Clear all possible testimonial cache combinations
        $limits = [null, 3, 4, 5, 6, 8, 10, 12];
        $featured = [true, false, '', '1', '0'];

        foreach ($limits as $limit) {
            foreach ($featured as $feat) {
                Cache::forget("cms_testimonials_{$limit}_{$feat}");
            }
        }

        // Also clear the base key
        Cache::forget('cms_testimonials');
    }

    /**
     * Clear cache when saved or deleted
     */
    protected static function booted()
    {
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}
