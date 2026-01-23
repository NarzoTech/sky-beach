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
            // ============ HOMEPAGE SECTIONS ============
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
                'button_link' => '#',
                'button_link_2' => '#',
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

            // ============ ABOUT PAGE SECTIONS ============
            [
                'section_name' => 'about_breadcrumb',
                'page_name' => 'about',
                'section_status' => true,
                'sort_order' => 1,
                'translation' => [
                    'title' => 'About Us',
                ],
            ],
            [
                'section_name' => 'about_story',
                'page_name' => 'about',
                'button_text' => 'View All Menu',
                'button_link' => '/menu',
                'section_status' => true,
                'sort_order' => 2,
                'translation' => [
                    'title' => 'We invite you to visit our restaurant',
                    'description' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.',
                ],
            ],
            [
                'section_name' => 'about_gallery',
                'page_name' => 'about',
                'quantity' => 4,
                'section_status' => true,
                'sort_order' => 3,
                'translation' => [
                    'title' => 'Our Gallery',
                ],
            ],
            [
                'section_name' => 'about_showcase',
                'page_name' => 'about',
                'quantity' => 4,
                'section_status' => true,
                'sort_order' => 4,
                'translation' => [
                    'title' => 'Showcase',
                ],
            ],
            [
                'section_name' => 'about_reservation',
                'page_name' => 'about',
                'section_status' => true,
                'sort_order' => 5,
                'translation' => [
                    'title' => 'ONLINE RESERVATION',
                    'subtitle' => 'Book your table now',
                ],
            ],
            [
                'section_name' => 'about_testimonials',
                'page_name' => 'about',
                'video' => 'https://youtu.be/nqye02H_H6I?si=ougeOsfL0tat6YbT',
                'section_status' => true,
                'sort_order' => 6,
                'translation' => [
                    'title' => 'What Our Customers Say',
                ],
            ],
            [
                'section_name' => 'about_counters',
                'page_name' => 'about',
                'section_status' => true,
                'sort_order' => 7,
                'translation' => [
                    'title' => 'Our Achievements',
                ],
            ],
            [
                'section_name' => 'about_chefs',
                'page_name' => 'about',
                'quantity' => 4,
                'button_text' => 'View All Chefs',
                'button_link' => '/chefs',
                'section_status' => true,
                'sort_order' => 8,
                'translation' => [
                    'title' => 'Meet Our Special Chefs',
                    'subtitle' => 'The culinary experts behind our delicious dishes',
                ],
            ],
            [
                'section_name' => 'about_blogs',
                'page_name' => 'about',
                'quantity' => 3,
                'section_status' => true,
                'sort_order' => 9,
                'translation' => [
                    'title' => 'Our Latest News & Article',
                    'subtitle' => 'Stay updated with our latest stories',
                ],
            ],

            // ============ CONTACT PAGE SECTIONS ============
            [
                'section_name' => 'contact_breadcrumb',
                'page_name' => 'contact',
                'section_status' => true,
                'sort_order' => 1,
                'translation' => [
                    'title' => 'Contact Us',
                ],
            ],
            [
                'section_name' => 'contact_form',
                'page_name' => 'contact',
                'section_status' => true,
                'sort_order' => 2,
                'translation' => [
                    'title' => 'Get In Touch',
                    'subtitle' => 'We would love to hear from you',
                ],
            ],
            [
                'section_name' => 'contact_info',
                'page_name' => 'contact',
                'section_status' => true,
                'sort_order' => 3,
                'translation' => [
                    'title' => 'Contact Information',
                    'description' => 'Feel free to reach out to us through any of the following channels.',
                ],
            ],
            [
                'section_name' => 'contact_map',
                'page_name' => 'contact',
                'video' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3104.8776746534986!2d-77.027541687759!3d38.903912546200644!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89b7b7931d95b707%3A0x16e85cf5a8a5fdce!2sMarriott%20Marquis%20Washington%2C%20DC!5e0!3m2!1sen!2sbd!4v1700767199965!5m2!1sen!2sbd',
                'section_status' => true,
                'sort_order' => 4,
                'translation' => [
                    'title' => 'Find Us On Map',
                ],
            ],

            // ============ MENU PAGE SECTIONS ============
            [
                'section_name' => 'menu_breadcrumb',
                'page_name' => 'menu',
                'section_status' => true,
                'sort_order' => 1,
                'translation' => [
                    'title' => 'Our Menu',
                ],
            ],
            [
                'section_name' => 'menu_filters',
                'page_name' => 'menu',
                'section_status' => true,
                'sort_order' => 2,
                'translation' => [
                    'title' => 'Filter Menu',
                    'subtitle' => 'Find your favorite dishes',
                ],
            ],

            // ============ RESERVATION PAGE SECTIONS ============
            [
                'section_name' => 'reservation_breadcrumb',
                'page_name' => 'reservation',
                'section_status' => true,
                'sort_order' => 1,
                'translation' => [
                    'title' => 'Reservations',
                ],
            ],
            [
                'section_name' => 'reservation_form',
                'page_name' => 'reservation',
                'section_status' => true,
                'sort_order' => 2,
                'translation' => [
                    'title' => 'ONLINE RESERVATION',
                    'subtitle' => 'Book your table',
                    'description' => 'Reserve your table and enjoy a memorable dining experience with us.',
                ],
            ],
            [
                'section_name' => 'reservation_info',
                'page_name' => 'reservation',
                'section_status' => true,
                'sort_order' => 3,
                'translation' => [
                    'title' => 'Reservation Information',
                    'subtitle' => 'Important details about your booking',
                ],
            ],

            // ============ SERVICE PAGE SECTIONS ============
            [
                'section_name' => 'service_breadcrumb',
                'page_name' => 'service',
                'section_status' => true,
                'sort_order' => 1,
                'translation' => [
                    'title' => 'Our Services',
                ],
            ],
            [
                'section_name' => 'service_list',
                'page_name' => 'service',
                'quantity' => 9,
                'section_status' => true,
                'sort_order' => 2,
                'translation' => [
                    'title' => 'What We Offer',
                    'subtitle' => 'Explore our wide range of services',
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
