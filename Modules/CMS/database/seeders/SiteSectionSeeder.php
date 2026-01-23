<?php

namespace Modules\CMS\database\seeders;

use Illuminate\Database\Seeder;
use Modules\CMS\app\Models\SiteSection;

class SiteSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding Site Sections...');

        $sections = [
            // Homepage Sections
            [
                'section_name' => 'hero_banner',
                'page_name' => 'home',
                'button_text' => 'Order Now',
                'button_link' => '/menu',
                'section_status' => true,
                'show_search' => false,
                'sort_order' => 1,
                'translation' => [
                    'title' => 'Special Foods for your Eating',
                    'subtitle' => 'Delicious Food',
                    'description' => 'Experience the finest culinary delights crafted with passion and served with love. Our menu features a perfect blend of traditional recipes and modern cuisine.',
                ],
            ],
            [
                'section_name' => 'popular_categories',
                'page_name' => 'home',
                'quantity' => 6,
                'section_status' => true,
                'sort_order' => 2,
                'translation' => [
                    'title' => 'Our Popular Category',
                ],
            ],
            [
                'section_name' => 'advertisement_large',
                'page_name' => 'home',
                'button_text' => 'Order Now',
                'button_link' => '/menu',
                'section_status' => true,
                'sort_order' => 3,
                'translation' => [
                    'title' => 'The Best Burger Place in Town',
                ],
            ],
            [
                'section_name' => 'advertisement_small',
                'page_name' => 'home',
                'button_text' => 'Order Now',
                'button_link' => '/menu',
                'section_status' => true,
                'sort_order' => 4,
                'translation' => [
                    'title' => 'Great Value Mixed Drinks',
                ],
            ],
            [
                'section_name' => 'featured_menu',
                'page_name' => 'home',
                'quantity' => 8,
                'section_status' => true,
                'sort_order' => 5,
                'translation' => [
                    'title' => 'Delicious Menu',
                    'subtitle' => 'Explore our carefully crafted dishes',
                ],
            ],
            [
                'section_name' => 'special_offer',
                'page_name' => 'home',
                'button_text' => 'Order Now',
                'button_link' => '/menu',
                'section_status' => true,
                'sort_order' => 6,
                'translation' => [
                    'title' => 'Delicious Food with us',
                    'subtitle' => 'Today Special Offer',
                ],
            ],
            [
                'section_name' => 'app_download',
                'page_name' => 'home',
                'button_link' => '#', // Apple Store
                'button_link_2' => '#', // Play Store
                'section_status' => true,
                'sort_order' => 7,
                'translation' => [
                    'title' => 'Are you Ready to Start your Order?',
                    'description' => 'Download our app and enjoy exclusive offers, easy ordering, and fast delivery right to your doorstep.',
                ],
            ],
            [
                'section_name' => 'our_chefs',
                'page_name' => 'home',
                'quantity' => 4,
                'section_status' => true,
                'sort_order' => 8,
                'translation' => [
                    'title' => 'Meet Our Special Chefs',
                    'subtitle' => 'The culinary experts behind our delicious dishes',
                ],
            ],
            [
                'section_name' => 'testimonials',
                'page_name' => 'home',
                'video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'section_status' => true,
                'sort_order' => 9,
                'translation' => [
                    'title' => 'What Our Customers Say',
                ],
            ],
            [
                'section_name' => 'counters',
                'page_name' => 'home',
                'section_status' => true,
                'sort_order' => 10,
                'translation' => [
                    'title' => 'Our Achievements',
                ],
            ],
            [
                'section_name' => 'latest_blogs',
                'page_name' => 'home',
                'quantity' => 3,
                'section_status' => true,
                'sort_order' => 11,
                'translation' => [
                    'title' => 'Our Latest News & Article',
                    'subtitle' => 'Stay updated with our latest stories',
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $translation = $sectionData['translation'] ?? [];
            unset($sectionData['translation']);

            // Check if section already exists
            $section = SiteSection::where('section_name', $sectionData['section_name'])
                ->where('page_name', $sectionData['page_name'])
                ->first();

            if (!$section) {
                $section = SiteSection::create($sectionData);

                // Create English translation
                if (!empty($translation)) {
                    $section->translations()->create([
                        'lang_code' => 'en',
                        'title' => $translation['title'] ?? '',
                        'subtitle' => $translation['subtitle'] ?? '',
                        'description' => $translation['description'] ?? '',
                    ]);
                }

                $this->command->info("Created section: {$sectionData['section_name']}");
            } else {
                $this->command->info("Section already exists: {$sectionData['section_name']}");
            }
        }

        $this->command->info('Site Sections seeding completed!');
    }
}
