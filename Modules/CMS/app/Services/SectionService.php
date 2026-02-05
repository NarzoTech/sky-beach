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

        // ============ ABOUT PAGE SECTIONS ============
        'about_breadcrumb' => [
            'label' => 'About Breadcrumb',
            'fields' => ['title', 'background_image'],
            'validation' => [
                'title' => 'required|string|max:255',
                'background_image' => 'nullable|image|max:2048',
            ],
        ],
        'about_story' => [
            'label' => 'About Story',
            'fields' => ['title', 'description', 'button_text', 'button_link', 'gallery_images'],
            'gallery_count' => 4,
            'gallery_labels' => ['Large Image (Left)', 'Small Image (Top Right)', 'Small Image (Bottom Left)', 'Large Image (Right)'],
            'validation' => [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:3000',
                'button_text' => 'nullable|string|max:50',
                'button_link' => 'nullable|string|max:255',
                'gallery_images.*' => 'nullable|image|max:2048',
            ],
        ],
        'about_gallery' => [
            'label' => 'About Gallery',
            'fields' => ['title', 'quantity'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:12',
            ],
        ],
        'about_showcase' => [
            'label' => 'About Showcase',
            'fields' => ['title', 'gallery_images'],
            'gallery_count' => 4,
            'gallery_labels' => ['Large Image (Left)', 'Small Image (Top)', 'Small Image (Bottom)', 'Large Image (Right)'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'gallery_images.*' => 'nullable|image|max:2048',
            ],
        ],
        'about_reservation' => [
            'label' => 'About Reservation',
            'fields' => ['title', 'subtitle', 'image'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'image' => 'nullable|image|max:2048',
            ],
        ],
        'about_testimonials' => [
            'label' => 'About Testimonials',
            'fields' => ['title', 'background_image', 'video'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'background_image' => 'nullable|image|max:2048',
                'video' => 'nullable|string|max:500',
            ],
        ],
        'about_counters' => [
            'label' => 'About Counters',
            'fields' => ['title'],
            'validation' => [
                'title' => 'nullable|string|max:255',
            ],
        ],
        'about_chefs' => [
            'label' => 'About Chefs',
            'fields' => ['title', 'subtitle', 'quantity', 'button_text', 'button_link'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:12',
                'button_text' => 'nullable|string|max:50',
                'button_link' => 'nullable|string|max:255',
            ],
        ],
        'about_blogs' => [
            'label' => 'About Blogs',
            'fields' => ['title', 'subtitle', 'quantity'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:6',
            ],
        ],

        // ============ CONTACT PAGE SECTIONS ============
        'contact_breadcrumb' => [
            'label' => 'Contact Breadcrumb',
            'fields' => ['title', 'background_image'],
            'validation' => [
                'title' => 'required|string|max:255',
                'background_image' => 'nullable|image|max:2048',
            ],
        ],
        'contact_form' => [
            'label' => 'Contact Form',
            'fields' => ['title', 'subtitle', 'image'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'image' => 'nullable|image|max:2048',
            ],
        ],
        'contact_info' => [
            'label' => 'Contact Information',
            'fields' => ['title', 'description'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
            ],
        ],
        'contact_map' => [
            'label' => 'Contact Map',
            'fields' => ['title', 'video'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'video' => 'nullable|string|max:1000',
            ],
        ],

        // ============ MENU PAGE SECTIONS ============
        'menu_breadcrumb' => [
            'label' => 'Menu Breadcrumb',
            'fields' => ['title', 'background_image'],
            'validation' => [
                'title' => 'required|string|max:255',
                'background_image' => 'nullable|image|max:2048',
            ],
        ],
        'menu_filters' => [
            'label' => 'Menu Filters',
            'fields' => ['title', 'subtitle'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
            ],
        ],

        // ============ MENU DETAIL PAGE SECTIONS ============
        'menu_detail_featured_offer' => [
            'label' => 'Featured Offer',
            'fields' => ['title', 'description', 'button_text'],
            'validation' => [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'button_text' => 'nullable|string|max:50',
            ],
        ],
        'menu_detail_sidebar_banner' => [
            'label' => 'Sidebar Banner',
            'fields' => ['title', 'subtitle', 'image', 'button_text', 'button_link'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'image' => 'nullable|image|max:2048',
                'button_text' => 'nullable|string|max:50',
                'button_link' => 'nullable|string|max:255',
            ],
        ],

        // ============ RESERVATION PAGE SECTIONS ============
        'reservation_breadcrumb' => [
            'label' => 'Reservation Breadcrumb',
            'fields' => ['title', 'background_image'],
            'validation' => [
                'title' => 'required|string|max:255',
                'background_image' => 'nullable|image|max:2048',
            ],
        ],
        'reservation_form' => [
            'label' => 'Reservation Form',
            'fields' => ['title', 'subtitle', 'image', 'description'],
            'validation' => [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'image' => 'nullable|image|max:2048',
                'description' => 'nullable|string|max:1000',
            ],
        ],
        'reservation_info' => [
            'label' => 'Reservation Info Cards',
            'fields' => ['title', 'subtitle', 'description'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
            ],
        ],
        'reservation_gallery' => [
            'label' => 'Gallery Section',
            'fields' => ['title', 'subtitle', 'quantity'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:12',
            ],
        ],
        'reservation_blogs' => [
            'label' => 'Latest Blogs Section',
            'fields' => ['title', 'subtitle', 'quantity'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:6',
            ],
        ],

        // ============ SERVICE PAGE SECTIONS ============
        'service_breadcrumb' => [
            'label' => 'Service Breadcrumb',
            'fields' => ['title', 'background_image'],
            'validation' => [
                'title' => 'required|string|max:255',
                'background_image' => 'nullable|image|max:2048',
            ],
        ],
        'service_list' => [
            'label' => 'Service List',
            'fields' => ['title', 'subtitle', 'quantity'],
            'validation' => [
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:20',
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
            'about' => [
                'about_breadcrumb',
                'about_story',
                'about_gallery',
                'about_showcase',
                'about_reservation',
                'about_testimonials',
                'about_counters',
                'about_chefs',
                'about_blogs',
            ],
            'contact' => [
                'contact_breadcrumb',
                'contact_form',
                'contact_info',
                'contact_map',
            ],
            'menu' => [
                'menu_breadcrumb',
                'menu_filters',
            ],
            'menu_detail' => [
                'menu_detail_featured_offer',
                'menu_detail_sidebar_banner',
            ],
            'reservation' => [
                'reservation_breadcrumb',
                'reservation_form',
                'reservation_gallery',
                'reservation_blogs',
            ],
            'service' => [
                'service_breadcrumb',
                'service_list',
            ],
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

            // Handle gallery images (multiple images stored as array)
            if ($request->hasFile('gallery_images')) {
                $existingImages = $section->images ?? [];
                $galleryImages = $request->file('gallery_images');

                foreach ($galleryImages as $index => $file) {
                    if ($file instanceof UploadedFile) {
                        $oldPath = $existingImages[$index] ?? null;
                        $existingImages[$index] = $this->uploadImage($file, $oldPath);
                    }
                }

                $section->images = $existingImages;
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
