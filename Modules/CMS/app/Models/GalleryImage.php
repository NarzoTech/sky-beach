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
        return $this->image ? asset($this->image) : null;
    }

    /**
     * Get alt text (fallback to title)
     */
    public function getAltAttribute()
    {
        return $this->alt_text ?: $this->title ?: 'Gallery Image';
    }

    /**
     * Clear cache when saved
     */
    protected static function booted()
    {
        static::saved(function ($image) {
            Cache::forget("cms_gallery_{$image->category}");
        });

        static::deleted(function ($image) {
            Cache::forget("cms_gallery_{$image->category}");
        });
    }
}
