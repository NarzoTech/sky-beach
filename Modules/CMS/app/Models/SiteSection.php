<?php

namespace Modules\CMS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;

class SiteSection extends Model
{
    protected $fillable = [
        'section_name',
        'page_name',
        'image',
        'images',
        'background_image',
        'video',
        'quantity',
        'button_text',
        'button_link',
        'button_text_2',
        'button_link_2',
        'extra_data',
        'section_status',
        'show_search',
        'sort_order',
    ];

    protected $casts = [
        'images' => 'array',
        'extra_data' => 'array',
        'section_status' => 'boolean',
        'show_search' => 'boolean',
    ];

    /**
     * Get the translation for current language
     */
    public function translation(): HasOne
    {
        $langCode = session('locale', config('app.locale', 'en'));

        return $this->hasOne(SiteSectionTranslation::class)
            ->where('lang_code', $langCode)
            ->withDefault([
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'content' => null,
            ]);
    }

    /**
     * Get all translations
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SiteSectionTranslation::class);
    }

    /**
     * Get translation for specific language
     */
    public function getTranslation(string $langCode = 'en'): ?SiteSectionTranslation
    {
        return $this->translations()->where('lang_code', $langCode)->first();
    }

    /**
     * Get or create translation for a language
     */
    public function getOrCreateTranslation(string $langCode = 'en'): SiteSectionTranslation
    {
        return $this->translations()->firstOrCreate(
            ['lang_code' => $langCode],
            ['title' => '', 'subtitle' => '', 'description' => '']
        );
    }

    /**
     * Scope: Active sections
     */
    public function scopeActive($query)
    {
        return $query->where('section_status', true);
    }

    /**
     * Scope: By page
     */
    public function scopeForPage($query, string $page)
    {
        return $query->where('page_name', $page);
    }

    /**
     * Scope: By section name
     */
    public function scopeSection($query, string $section)
    {
        return $query->where('section_name', $section);
    }

    /**
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get section by name and page (with caching)
     */
    public static function getSection(string $sectionName, string $pageName = 'home'): ?self
    {
        $cacheKey = "site_section_{$pageName}_{$sectionName}";

        return Cache::remember($cacheKey, 3600, function () use ($sectionName, $pageName) {
            return static::with('translation')
                ->where('section_name', $sectionName)
                ->where('page_name', $pageName)
                ->first();
        });
    }

    /**
     * Get all sections for a page (with caching)
     */
    public static function getPageSections(string $pageName = 'home'): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = "site_sections_{$pageName}";

        return Cache::remember($cacheKey, 3600, function () use ($pageName) {
            return static::with('translation')
                ->where('page_name', $pageName)
                ->where('section_status', true)
                ->orderBy('sort_order')
                ->get()
                ->keyBy('section_name');
        });
    }

    /**
     * Clear cache when saved
     */
    protected static function booted()
    {
        static::saved(function ($section) {
            Cache::forget("site_section_{$section->page_name}_{$section->section_name}");
            Cache::forget("site_sections_{$section->page_name}");
        });

        static::deleted(function ($section) {
            Cache::forget("site_section_{$section->page_name}_{$section->section_name}");
            Cache::forget("site_sections_{$section->page_name}");
        });
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset($this->image) : null;
    }

    /**
     * Get background image URL
     */
    public function getBackgroundImageUrlAttribute(): ?string
    {
        return $this->background_image ? asset($this->background_image) : null;
    }

    /**
     * Get title from translation
     */
    public function getTitleAttribute(): string
    {
        return $this->translation->title ?? '';
    }

    /**
     * Get subtitle from translation
     */
    public function getSubtitleAttribute(): string
    {
        return $this->translation->subtitle ?? '';
    }

    /**
     * Get description from translation
     */
    public function getDescriptionAttribute(): string
    {
        return $this->translation->description ?? '';
    }
}
