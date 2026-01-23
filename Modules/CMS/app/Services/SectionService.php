<?php

namespace Modules\CMS\app\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CMS\app\Models\SiteSection;

class SectionService
{
    /**
     * Section configurations with their fields
     */
    public static array $sectionConfig = [
        'hero_banner' => [
            'label' => 'Hero Banner',
            'fields' => ['title', 'subtitle', 'description', 'image', 'background_image', 'button_text', 'button_link', 'show_search'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'image' => 'nullable|image|max:2048',
                'background_image' => 'nullable|image|max:2048',
                'button_text' => 'nullable|string|max:50',
                'button_link' => 'nullable|string|max:255',
            ],
        ],
        'popular_categories' => [
            'label' => 'Popular Categories',
            'fields' => ['title', 'quantity'],
            'validation' => [
                'title' => 'required|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:12',
            ],
        ],
        'advertisement_large' => [
            'label' => 'Large Advertisement',
            'fields' => ['title', 'image', 'button_text', 'button_link'],
            'validation' => [
                'title' => 'required|string|max:255',
                'image' => 'nullable|image|max:2048',
                'button_text' => 'nullable|string|max:50',
                'button_link' => 'nullable|string|max:255',
            ],
        ],
        'advertisement_small' => [
            'label' => 'Small Advertisement',
            'fields' => ['title', 'image', 'button_text', 'button_link'],
            'validation' => [
                'title' => 'required|string|max:255',
                'image' => 'nullable|image|max:2048',
                'button_text' => 'nullable|string|max:50',
                'button_link' => 'nullable|string|max:255',
            ],
        ],
        'featured_menu' => [
            'label' => 'Featured Menu',
            'fields' => ['title', 'subtitle', 'quantity'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:12',
            ],
        ],
        'special_offer' => [
            'label' => 'Special Offer',
            'fields' => ['title', 'subtitle', 'image', 'background_image', 'button_text', 'button_link'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'image' => 'nullable|image|max:2048',
                'background_image' => 'nullable|image|max:2048',
                'button_text' => 'nullable|string|max:50',
                'button_link' => 'nullable|string|max:255',
            ],
        ],
        'app_download' => [
            'label' => 'App Download',
            'fields' => ['title', 'description', 'image', 'button_link', 'button_link_2'],
            'validation' => [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'image' => 'nullable|image|max:2048',
                'button_link' => 'nullable|string|max:255',   // Apple Store
                'button_link_2' => 'nullable|string|max:255', // Play Store
            ],
        ],
        'our_chefs' => [
            'label' => 'Our Chefs',
            'fields' => ['title', 'subtitle', 'quantity'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:12',
            ],
        ],
        'testimonials' => [
            'label' => 'Testimonials',
            'fields' => ['title', 'background_image', 'video'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'background_image' => 'nullable|image|max:2048',
                'video' => 'nullable|string|max:500',
            ],
        ],
        'counters' => [
            'label' => 'Counters',
            'fields' => ['title'],
            'validation' => [
                'title' => 'nullable|string|max:255',
            ],
        ],
        'latest_blogs' => [
            'label' => 'Latest Blogs',
            'fields' => ['title', 'subtitle', 'quantity'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:12',
            ],
        ],
        // About page sections
        'about_hero' => [
            'label' => 'About Hero',
            'fields' => ['title', 'subtitle', 'description', 'image', 'background_image'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:2000',
                'image' => 'nullable|image|max:2048',
                'background_image' => 'nullable|image|max:2048',
            ],
        ],
        // Contact page sections
        'contact_info' => [
            'label' => 'Contact Information',
            'fields' => ['title', 'subtitle', 'description', 'image'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'image' => 'nullable|image|max:2048',
            ],
        ],
    ];

    /**
     * Get section configuration
     */
    public static function getConfig(string $sectionName): ?array
    {
        return self::$sectionConfig[$sectionName] ?? null;
    }

    /**
     * Get all sections for a page
     */
    public static function getPageSections(string $pageName): array
    {
        $sections = [
            'home' => [
                'hero_banner',
                'popular_categories',
                'advertisement_large',
                'advertisement_small',
                'featured_menu',
                'special_offer',
                'app_download',
                'our_chefs',
                'testimonials',
                'counters',
                'latest_blogs',
            ],
            'about' => ['about_hero'],
            'contact' => ['contact_info'],
        ];

        return $sections[$pageName] ?? [];
    }

    /**
     * Get validation rules for a section
     */
    public function getValidationRules(string $sectionName): array
    {
        $config = self::getConfig($sectionName);
        return $config['validation'] ?? [];
    }

    /**
     * Update or create a section
     */
    public function updateSection(Request $request, string $sectionName, string $pageName = 'home'): array
    {
        DB::beginTransaction();
        try {
            // Find or create the section
            $section = SiteSection::firstOrNew([
                'section_name' => $sectionName,
                'page_name' => $pageName,
            ]);

            // Handle image uploads
            if ($request->hasFile('image')) {
                $section->image = $this->uploadImage($request->file('image'), $section->image);
            }

            if ($request->hasFile('background_image')) {
                $section->background_image = $this->uploadImage($request->file('background_image'), $section->background_image);
            }

            // Update non-translatable fields
            $section->button_text = $request->input('button_text', $section->button_text);
            $section->button_link = $request->input('button_link', $section->button_link);
            $section->button_text_2 = $request->input('button_text_2', $section->button_text_2);
            $section->button_link_2 = $request->input('button_link_2', $section->button_link_2);
            $section->video = $request->input('video', $section->video);
            $section->quantity = $request->input('quantity', $section->quantity);
            $section->section_status = $request->boolean('section_status', true);
            $section->show_search = $request->boolean('show_search', false);
            $section->sort_order = $request->input('sort_order', $section->sort_order ?? 0);

            // Handle extra data
            if ($request->has('extra_data')) {
                $section->extra_data = $request->input('extra_data');
            }

            $section->save();

            // Update translation
            $langCode = $request->input('lang_code', 'en');
            $translation = $section->getOrCreateTranslation($langCode);

            $translation->title = $request->input('title', $translation->title);
            $translation->subtitle = $request->input('subtitle', $translation->subtitle);
            $translation->description = $request->input('description', $translation->description);

            if ($request->has('content')) {
                $translation->content = $request->input('content');
            }

            $translation->save();

            // Clear cache
            $this->clearSectionCache($sectionName, $pageName);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Section updated successfully',
                'section' => $section->fresh(['translation']),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating section {$sectionName}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to update section: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Upload image and delete old one
     */
    protected function uploadImage(UploadedFile $file, ?string $oldPath = null): string
    {
        // Delete old image
        if ($oldPath && Storage::disk('public')->exists(str_replace('storage/', '', $oldPath))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $oldPath));
        }

        // Upload new image
        $path = $file->store('cms/sections', 'public');
        return 'storage/' . $path;
    }

    /**
     * Clear section cache
     */
    protected function clearSectionCache(string $sectionName, string $pageName): void
    {
        Cache::forget("site_section_{$pageName}_{$sectionName}");
        Cache::forget("site_sections_{$pageName}");
    }

    /**
     * Toggle section status
     */
    public function toggleStatus(string $sectionName, string $pageName = 'home'): array
    {
        try {
            $section = SiteSection::where('section_name', $sectionName)
                ->where('page_name', $pageName)
                ->first();

            if (!$section) {
                return ['success' => false, 'message' => 'Section not found'];
            }

            $section->section_status = !$section->section_status;
            $section->save();

            $this->clearSectionCache($sectionName, $pageName);

            return [
                'success' => true,
                'message' => 'Status updated',
                'status' => $section->section_status,
            ];
        } catch (\Exception $e) {
            Log::error("Error toggling section status: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update status'];
        }
    }
}
