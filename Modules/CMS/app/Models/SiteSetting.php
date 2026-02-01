<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'sort_order',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function set($key, $value, $group = 'general', $type = 'text', $label = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'label' => $label ?? ucwords(str_replace(['_', '.'], ' ', $key)),
            ]
        );

        Cache::forget("cms_setting_{$key}");

        return $setting;
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)
            ->orderBy('sort_order')
            ->get()
            ->pluck('value', 'key');
    }

    /**
     * Scope: By group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Clear all settings cache keys
     */
    public static function clearCache($key = null, $group = null): void
    {
        if ($key) {
            Cache::forget("cms_setting_{$key}");
        }

        if ($group) {
            Cache::forget("cms_settings_group_{$group}");
        }

        // Clear common setting groups
        $groups = ['general', 'contact', 'social', 'seo', 'hours', 'payment', 'email'];
        foreach ($groups as $g) {
            Cache::forget("cms_settings_group_{$g}");
        }
    }

    /**
     * Clear cache when saved or deleted
     */
    protected static function booted()
    {
        static::saved(function ($setting) {
            self::clearCache($setting->key, $setting->group);
        });

        static::deleted(function ($setting) {
            self::clearCache($setting->key, $setting->group);
        });
    }
}
