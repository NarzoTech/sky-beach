<?php

use Modules\CMS\app\Models\SiteSetting;
use Modules\CMS\app\Models\PageSection;
use Modules\CMS\app\Models\Testimonial;
use Modules\CMS\app\Models\Counter;
use Modules\CMS\app\Models\PromotionalBanner;
use Modules\CMS\app\Models\LegalPage;
use Modules\CMS\app\Models\GalleryImage;
use Modules\CMS\app\Models\InfoCard;
use Modules\CMS\app\Models\EventType;
use Modules\CMS\app\Models\Feature;
use Illuminate\Support\Facades\Cache;

/**
 * Get a site setting value
 */
if (!function_exists('cms_setting')) {
    function cms_setting($key, $default = null)
    {
        return Cache::remember("cms_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = SiteSetting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }
}

/**
 * Get multiple site settings by group
 */
if (!function_exists('cms_settings')) {
    function cms_settings($group = null)
    {
        $cacheKey = "cms_settings_group_{$group}";
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            $query = SiteSetting::query();
            if ($group) {
                $query->where('group', $group);
            }
            return $query->orderBy('sort_order')->pluck('value', 'key')->toArray();
        });
    }
}

/**
 * Get a page section
 */
if (!function_exists('cms_section')) {
    function cms_section($page, $sectionKey)
    {
        return Cache::remember("cms_section_{$page}_{$sectionKey}", 3600, function () use ($page, $sectionKey) {
            return PageSection::where('page', $page)
                ->where('section_key', $sectionKey)
                ->where('is_active', true)
                ->first();
        });
    }
}

/**
 * Get all sections for a page
 */
if (!function_exists('cms_page_sections')) {
    function cms_page_sections($page)
    {
        return Cache::remember("cms_page_sections_{$page}", 3600, function () use ($page) {
            return PageSection::where('page', $page)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->keyBy('section_key');
        });
    }
}

/**
 * Get all active testimonials
 */
if (!function_exists('cms_testimonials')) {
    function cms_testimonials($limit = null, $featured = false)
    {
        $cacheKey = "cms_testimonials_{$limit}_{$featured}";
        return Cache::remember($cacheKey, 3600, function () use ($limit, $featured) {
            $query = Testimonial::where('is_active', true)
                ->orderBy('sort_order');

            if ($featured) {
                $query->where('is_featured', true);
            }

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }
}

/**
 * Get all active counters
 */
if (!function_exists('cms_counters')) {
    function cms_counters()
    {
        return Cache::remember('cms_counters', 3600, function () {
            return Counter::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }
}

/**
 * Get promotional banner by position
 */
if (!function_exists('cms_banner')) {
    function cms_banner($position)
    {
        return Cache::remember("cms_banner_{$position}", 3600, function () use ($position) {
            return PromotionalBanner::where('position', $position)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->orderBy('sort_order')
                ->first();
        });
    }
}

/**
 * Get all banners for a position
 */
if (!function_exists('cms_banners')) {
    function cms_banners($position)
    {
        return Cache::remember("cms_banners_{$position}", 3600, function () use ($position) {
            return PromotionalBanner::where('position', $position)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->orderBy('sort_order')
                ->get();
        });
    }
}

/**
 * Get legal page by slug
 */
if (!function_exists('cms_legal_page')) {
    function cms_legal_page($slug)
    {
        return Cache::remember("cms_legal_{$slug}", 3600, function () use ($slug) {
            return LegalPage::where('slug', $slug)
                ->where('is_active', true)
                ->first();
        });
    }
}

/**
 * Get gallery images
 */
if (!function_exists('cms_gallery')) {
    function cms_gallery($category, $limit = null)
    {
        $cacheKey = "cms_gallery_{$category}_{$limit}";
        return Cache::remember($cacheKey, 3600, function () use ($category, $limit) {
            $query = GalleryImage::where('category', $category)
                ->where('is_active', true)
                ->orderBy('sort_order');

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }
}

/**
 * Get gallery images by page
 */
if (!function_exists('cms_gallery_by_page')) {
    function cms_gallery_by_page($page, $limit = null)
    {
        $cacheKey = "cms_gallery_page_{$page}_{$limit}";
        return Cache::remember($cacheKey, 3600, function () use ($page, $limit) {
            $query = GalleryImage::where('page', $page)
                ->where('is_active', true)
                ->orderBy('sort_order');

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }
}

/**
 * Get info cards for a page
 */
if (!function_exists('cms_info_cards')) {
    function cms_info_cards($page)
    {
        return Cache::remember("cms_info_cards_{$page}", 3600, function () use ($page) {
            return InfoCard::where('page', $page)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }
}

/**
 * Get event types
 */
if (!function_exists('cms_event_types')) {
    function cms_event_types()
    {
        return Cache::remember('cms_event_types', 3600, function () {
            return EventType::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }
}

/**
 * Get features for a page/section
 */
if (!function_exists('cms_features')) {
    function cms_features($page, $section = null)
    {
        $cacheKey = "cms_features_{$page}_{$section}";
        return Cache::remember($cacheKey, 3600, function () use ($page, $section) {
            $query = Feature::where('page', $page)
                ->where('is_active', true)
                ->orderBy('sort_order');

            if ($section) {
                $query->where('section', $section);
            }

            return $query->get();
        });
    }
}

/**
 * Clear all CMS cache
 */
if (!function_exists('cms_clear_cache')) {
    function cms_clear_cache()
    {
        $patterns = [
            'cms_setting_*',
            'cms_settings_*',
            'cms_section_*',
            'cms_page_sections_*',
            'cms_testimonials_*',
            'cms_counters',
            'cms_banner_*',
            'cms_banners_*',
            'cms_legal_*',
            'cms_gallery_*',
            'cms_info_cards_*',
            'cms_event_types',
            'cms_features_*',
        ];

        // Clear cache using tags if available, otherwise clear all
        try {
            Cache::flush();
        } catch (\Exception $e) {
            // If flush fails, return false
            return false;
        }

        return true;
    }
}

/**
 * Get contact information
 */
if (!function_exists('cms_contact')) {
    function cms_contact($key = null)
    {
        $contacts = cms_settings('contact');

        if ($key) {
            return $contacts["contact.{$key}"] ?? $contacts[$key] ?? null;
        }

        return $contacts;
    }
}

/**
 * Get social media links
 */
if (!function_exists('cms_social')) {
    function cms_social($platform = null)
    {
        $social = cms_settings('social');

        if ($platform) {
            return $social["social.{$platform}"] ?? $social[$platform] ?? null;
        }

        return $social;
    }
}

/**
 * Get business hours
 */
if (!function_exists('cms_hours')) {
    function cms_hours()
    {
        return cms_settings('hours');
    }
}
