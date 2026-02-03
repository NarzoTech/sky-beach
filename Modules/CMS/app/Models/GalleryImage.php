<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class GalleryImage extends Model
{
    protected $fillable = [
        'title',
        'image',
        'category',
        'page',
        'alt_text',
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
     * Scope: By category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
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
     * Get images by category
     */
    public static function getByCategory($category, $limit = null)
    {
        $query = static::active()
            ->category($category)
            ->ordered();

        if ($limit) {
            $query->limit($limit);
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
     * Get alt text (fallback to title)
     */
    public function getAltAttribute()
    {
        return $this->alt_text ?: $this->title ?: 'Gallery Image';
    }

    /**
     * Clear all gallery cache keys
     */
    public static function clearCache($category = null, $page = null): void
    {
        $limits = [null, 3, 4, 5, 6, 8, 10, 12, 20];
        $categories = $category ? [$category] : ['food', 'restaurant', 'events', 'about', 'general'];
        $pages = $page ? [$page] : ['home', 'about', 'menu', 'contact', 'reservation'];

        // Clear category-based cache
        foreach ($categories as $cat) {
            foreach ($limits as $limit) {
                Cache::forget("cms_gallery_{$cat}_{$limit}");
            }
        }

        // Clear page-based cache
        foreach ($pages as $p) {
            foreach ($limits as $limit) {
                Cache::forget("cms_gallery_page_{$p}_{$limit}");
            }
        }
    }

    /**
     * Clear cache when saved or deleted
     */
    protected static function booted()
    {
        static::saved(function ($image) {
            self::clearCache($image->category, $image->page);
        });

        static::deleted(function ($image) {
            self::clearCache($image->category, $image->page);
        });
    }
}
