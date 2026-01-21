<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Counter extends Model
{
    protected $fillable = [
        'label',
        'value',
        'icon',
        'suffix',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'integer',
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
     * Get formatted value with suffix
     */
    public function getFormattedValueAttribute()
    {
        return $this->value . ($this->suffix ?? '');
    }

    /**
     * Clear cache when saved
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('cms_counters');
        });

        static::deleted(function () {
            Cache::forget('cms_counters');
        });
    }
}
