<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PromotionalBanner extends Model
{
    protected $fillable = [
        'name',
        'title',
        'subtitle',
        'description',
        'image',
        'background_image',
        'button_text',
        'button_link',
        'position',
        'badge_text',
        'is_active',
        'start_date',
        'end_date',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Scope: Active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope: By position
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get banner by position
     */
    public static function getByPosition($position)
    {
        return static::active()
            ->position($position)
            ->ordered()
            ->first();
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset($this->image) : null;
    }

    /**
     * Get background image URL
     */
    public function getBackgroundImageUrlAttribute()
    {
        return $this->background_image ? asset($this->background_image) : null;
    }

    /**
     * Check if banner is currently active
     */
    public function isCurrentlyActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Clear all banner cache keys
     */
    public static function clearCache($position = null): void
    {
        $positions = $position ? [$position] : ['header', 'sidebar', 'footer', 'popup', 'home', 'menu', 'checkout'];

        foreach ($positions as $pos) {
            Cache::forget("cms_banner_{$pos}");
            Cache::forget("cms_banners_{$pos}");
        }
    }

    /**
     * Clear cache when saved or deleted
     */
    protected static function booted()
    {
        static::saved(function ($banner) {
            self::clearCache($banner->position);
        });

        static::deleted(function ($banner) {
            self::clearCache($banner->position);
        });
    }
}
