<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EventType extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description',
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
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get all active event types
     */
    public static function getActive()
    {
        return static::active()->ordered()->get();
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
        static::saved(function () {
            Cache::forget('cms_event_types');
        });

        static::deleted(function () {
            Cache::forget('cms_event_types');
        });
    }
}
