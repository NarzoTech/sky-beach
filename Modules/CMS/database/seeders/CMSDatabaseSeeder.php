<?php

namespace Modules\CMS\database\seeders;

use Illuminate\Database\Seeder;
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

class CMSDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSiteSettings();
        $this->seedPageSections();
        $this->seedTestimonials();
        $this->seedCounters();
        $this->seedPromotionalBanners();
        $this->seedLegalPages();
        $this->seedGalleryImages();
        $this->seedInfoCards();
        $this->seedEventTypes();
        $this->seedFeatures();
    }

    private function seedSiteSettings(): void
    {
        $settings = [
            // General
            ['key' => 'general.site_name', 'value' => 'CTAKE', 'group' => 'general', 'type' => 'text', 'label' => 'Site Name'],
            ['key' => 'general.tagline', 'value' => 'Delicious Food Restaurant', 'group' => 'general', 'type' => 'text', 'label' => 'Tagline'],

            // Contact
            ['key' => 'contact.address', 'value' => '16/A, Romadan House City Tower New York, United States', 'group' => 'contact', 'type' => 'textarea', 'label' => 'Address'],
            ['key' => 'contact.phone_primary', 'value' => '+990 123 456 789', 'group' => 'contact', 'type' => 'text', 'label' => 'Primary Phone'],
            ['key' => 'contact.phone_secondary', 'value' => '+990 456 123 789', 'group' => 'contact', 'type' => 'text', 'label' => 'Secondary Phone'],
            ['key' => 'contact.email_primary', 'value' => 'info@ctake.com', 'group' => 'contact', 'type' => 'text', 'label' => 'Primary Email'],
            ['key' => 'contact.email_secondary', 'value' => 'support@ctake.com', 'group' => 'contact', 'type' => 'text', 'label' => 'Secondary Email'],
            ['key' => 'contact.google_map_embed', 'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3105.1507877498064!2d-77.03279652422097!3d38.90177634678498!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89b7b7bae8f4e7b9%3A0x6c89e5e8d8b8f4e9!2sMarriott%20Marquis%20Washington%2C%20DC!5e0!3m2!1sen!2sus!4v1234567890', 'group' => 'contact', 'type' => 'textarea', 'label' => 'Google Map Embed URL'],

            // Social
            ['key' => 'social.facebook', 'value' => 'https://facebook.com', 'group' => 'social', 'type' => 'text', 'label' => 'Facebook URL'],
            ['key' => 'social.twitter', 'value' => 'https://twitter.com', 'group' => 'social', 'type' => 'text', 'label' => 'Twitter URL'],
            ['key' => 'social.instagram', 'value' => 'https://instagram.com', 'group' => 'social', 'type' => 'text', 'label' => 'Instagram URL'],
            ['key' => 'social.linkedin', 'value' => 'https://linkedin.com', 'group' => 'social', 'type' => 'text', 'label' => 'LinkedIn URL'],
            ['key' => 'social.youtube', 'value' => 'https://youtube.com', 'group' => 'social', 'type' => 'text', 'label' => 'YouTube URL'],

            // Hours
            ['key' => 'hours.weekdays', 'value' => 'Monday - Sunday: 10:00 AM - 10:00 PM', 'group' => 'hours', 'type' => 'text', 'label' => 'Operating Hours'],
        ];

        foreach ($settings as $index => $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, ['sort_order' => $index])
            );
        }
    }

    private function seedPageSections(): void
    {
        $sections = [
            // Home Page
            [
                'page' => 'home',
                'section_key' => 'hero_banner',
                'title' => 'Special Foods for your Eating',
                'subtitle' => 'Delicious Food',
                'content' => 'Commodo ullamcorper a lacus vestibulum sed arcu non. Non blandit massa enim nec dui nunc mattis enim ut.',
                'image' => 'website/images/banner_img.png',
                'background_image' => 'website/images/banner_bg.jpg',
                'button_text' => 'Order Now',
                'button_link' => '/menu',
            ],
            [
                'page' => 'home',
                'section_key' => 'category_section',
                'title' => 'Our Popular Category',
                'subtitle' => null,
                'content' => null,
            ],
            [
                'page' => 'home',
                'section_key' => 'menu_section',
                'title' => 'Delicious Menu',
                'subtitle' => null,
                'content' => null,
            ],
            [
                'page' => 'home',
                'section_key' => 'app_download',
                'title' => 'Are you Ready to Start your Order?',
                'subtitle' => null,
                'content' => 'Commodo ullamcorper lacus vestibulum sed Non blandit massa enim.',
                'image' => 'website/images/download_img.png',
                'extra_data' => json_encode([
                    'app_store_link' => '#',
                    'play_store_link' => '#',
                ]),
            ],
            [
                'page' => 'home',
                'section_key' => 'blog_section',
                'title' => 'Our Latest News & Article',
                'subtitle' => null,
                'content' => null,
            ],

            // About Page
            [
                'page' => 'about',
                'section_key' => 'story',
                'title' => 'We invite you to visit our restaurant',
                'subtitle' => null,
                'content' => "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt\n\nSed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit",
            ],
            [
                'page' => 'about',
                'section_key' => 'chefs_section',
                'title' => 'Meet Our Special Chefs',
                'subtitle' => null,
                'content' => null,
            ],

            // Contact Page
            [
                'page' => 'contact',
                'section_key' => 'form_section',
                'title' => 'Get In Touch',
                'subtitle' => null,
                'content' => null,
                'image' => 'website/images/contact_img.jpg',
            ],

            // Reservation Page
            [
                'page' => 'reservation',
                'section_key' => 'main',
                'title' => 'Online Reservation',
                'subtitle' => null,
                'content' => null,
                'image' => 'website/images/reservation_img_2.jpg',
            ],

            // Catering Page
            [
                'page' => 'catering',
                'section_key' => 'intro',
                'title' => 'Make Your Event Unforgettable',
                'subtitle' => null,
                'content' => 'From intimate gatherings to grand celebrations, our catering services bring exceptional cuisine and impeccable service to your special occasions.',
                'image' => 'website/images/catering_hero.jpg',
            ],
            [
                'page' => 'catering',
                'section_key' => 'featured_packages',
                'title' => 'Featured Packages',
                'subtitle' => 'Our most popular catering options for your events',
                'content' => null,
            ],
            [
                'page' => 'catering',
                'section_key' => 'all_packages',
                'title' => 'Our Catering Packages',
                'subtitle' => 'Choose a package that fits your event',
                'content' => null,
            ],
            [
                'page' => 'catering',
                'section_key' => 'event_types',
                'title' => 'Events We Cater',
                'subtitle' => null,
                'content' => null,
            ],
        ];

        foreach ($sections as $index => $section) {
            PageSection::updateOrCreate(
                ['page' => $section['page'], 'section_key' => $section['section_key']],
                array_merge($section, ['sort_order' => $index])
            );
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            [
                'name' => 'Indigo Violet',
                'position' => 'Co-Founder',
                'company' => null,
                'content' => 'I love that solvency lets us manage everything in one place. It\'s super helpful to be able to listen to voice samples, upload our own lists, and find quality salespeople that can grow with our team.',
                'image' => 'website/images/client_img_1.png',
                'rating' => 5,
                'is_featured' => true,
            ],
            [
                'name' => 'Jihan Ahmed',
                'position' => 'Co-Founder',
                'company' => null,
                'content' => 'I love that solvency lets us manage everything in one place. It\'s super helpful to be able to listen to voice samples, upload our own lists, and find quality salespeople that can grow with our team.',
                'image' => 'website/images/client_img_2.png',
                'rating' => 5,
                'is_featured' => true,
            ],
        ];

        foreach ($testimonials as $index => $testimonial) {
            Testimonial::updateOrCreate(
                ['name' => $testimonial['name']],
                array_merge($testimonial, ['sort_order' => $index])
            );
        }
    }

    private function seedCounters(): void
    {
        $counters = [
            ['label' => 'Dishes', 'value' => 45, 'icon' => 'fas fa-utensils'],
            ['label' => 'Locations', 'value' => 68, 'icon' => 'fas fa-map-marker-alt'],
            ['label' => 'Chefs', 'value' => 32, 'icon' => 'fas fa-user-tie'],
            ['label' => 'Cities', 'value' => 120, 'icon' => 'fas fa-city'],
        ];

        foreach ($counters as $index => $counter) {
            Counter::updateOrCreate(
                ['label' => $counter['label']],
                array_merge($counter, ['sort_order' => $index])
            );
        }
    }

    private function seedPromotionalBanners(): void
    {
        $banners = [
            [
                'name' => 'Home Large Banner',
                'title' => 'The best Burger place in town',
                'subtitle' => null,
                'description' => null,
                'image' => 'website/images/large_banner_img_1.jpg',
                'position' => 'home_large',
                'button_text' => 'Shop Now',
                'button_link' => '/menu',
            ],
            [
                'name' => 'Home Small Banner',
                'title' => 'Great Value Mixed Drinks',
                'subtitle' => null,
                'description' => null,
                'image' => 'website/images/small_banner_img_1.jpg',
                'position' => 'home_small',
                'button_text' => 'Shop Now',
                'button_link' => '/menu',
            ],
            [
                'name' => 'Home Special Offer',
                'title' => 'Delicious Food with us.',
                'subtitle' => null,
                'description' => null,
                'image' => 'website/images/add_banner_full_img.png',
                'background_image' => 'website/images/add_banner_full_bg.jpg',
                'position' => 'home_full',
                'badge_text' => 'Today special offer',
                'button_text' => 'Order Now',
                'button_link' => '/menu',
            ],
            [
                'name' => 'Sidebar Promo',
                'title' => 'Special Combo Pack',
                'subtitle' => 'Get Up to 50% Off',
                'description' => null,
                'image' => 'website/images/offer_bg.jpg',
                'position' => 'sidebar',
                'button_text' => 'Shop Now',
                'button_link' => '/menu',
            ],
        ];

        foreach ($banners as $index => $banner) {
            PromotionalBanner::updateOrCreate(
                ['name' => $banner['name']],
                array_merge($banner, ['sort_order' => $index])
            );
        }
    }

    private function seedLegalPages(): void
    {
        $pages = [
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'content' => $this->getPrivacyPolicyContent(),
                'meta_title' => 'Privacy Policy - CTAKE',
                'meta_description' => 'Learn about how CTAKE collects, uses, and protects your personal information.',
            ],
            [
                'slug' => 'terms-conditions',
                'title' => 'Terms & Conditions',
                'content' => $this->getTermsConditionsContent(),
                'meta_title' => 'Terms & Conditions - CTAKE',
                'meta_description' => 'Read our terms and conditions for using CTAKE services.',
            ],
        ];

        foreach ($pages as $page) {
            LegalPage::updateOrCreate(
                ['slug' => $page['slug']],
                $page
            );
        }
    }

    private function seedGalleryImages(): void
    {
        $images = [
            // About Story Images
            ['title' => 'Story Image 1', 'image' => 'website/images/about_story_img_1.jpg', 'category' => 'about_story', 'page' => 'about', 'sort_order' => 1],
            ['title' => 'Story Image 2', 'image' => 'website/images/about_story_img_2.jpg', 'category' => 'about_story', 'page' => 'about', 'sort_order' => 2],
            ['title' => 'Story Image 3', 'image' => 'website/images/about_story_img_3.jpg', 'category' => 'about_story', 'page' => 'about', 'sort_order' => 3],
            ['title' => 'Story Image 4', 'image' => 'website/images/about_story_img_4.jpg', 'category' => 'about_story', 'page' => 'about', 'sort_order' => 4],

            // About Showcase Images
            ['title' => 'Showcase Image 1', 'image' => 'website/images/showcase_img_1.jpg', 'category' => 'about_showcase', 'page' => 'about', 'sort_order' => 1],
            ['title' => 'Showcase Image 2', 'image' => 'website/images/showcase_img_2.jpg', 'category' => 'about_showcase', 'page' => 'about', 'sort_order' => 2],
            ['title' => 'Showcase Image 3', 'image' => 'website/images/showcase_img_3.jpg', 'category' => 'about_showcase', 'page' => 'about', 'sort_order' => 3],
            ['title' => 'Showcase Image 4', 'image' => 'website/images/showcase_img_4.jpg', 'category' => 'about_showcase', 'page' => 'about', 'sort_order' => 4],
        ];

        foreach ($images as $image) {
            GalleryImage::updateOrCreate(
                ['image' => $image['image']],
                $image
            );
        }
    }

    private function seedInfoCards(): void
    {
        $cards = [
            // Reservation Page
            [
                'page' => 'reservation',
                'title' => 'Opening Hours',
                'content' => "Monday - Sunday\n10:00 AM - 10:00 PM",
                'icon' => 'fas fa-clock',
                'sort_order' => 1,
            ],
            [
                'page' => 'reservation',
                'title' => 'Call Us',
                'content' => "For immediate assistance\n+1 234 567 8900",
                'icon' => 'fas fa-phone-alt',
                'sort_order' => 2,
            ],
            [
                'page' => 'reservation',
                'title' => 'Cancellation Policy',
                'content' => 'Free cancellation up to 2 hours before your reservation',
                'icon' => 'fas fa-info-circle',
                'sort_order' => 3,
            ],
        ];

        foreach ($cards as $card) {
            InfoCard::updateOrCreate(
                ['page' => $card['page'], 'title' => $card['title']],
                $card
            );
        }
    }

    private function seedEventTypes(): void
    {
        $types = [
            ['name' => 'Wedding', 'icon' => 'fas fa-ring'],
            ['name' => 'Corporate', 'icon' => 'fas fa-briefcase'],
            ['name' => 'Birthday', 'icon' => 'fas fa-birthday-cake'],
            ['name' => 'Anniversary', 'icon' => 'fas fa-heart'],
            ['name' => 'Graduation', 'icon' => 'fas fa-graduation-cap'],
            ['name' => 'Other', 'icon' => 'fas fa-star'],
        ];

        foreach ($types as $index => $type) {
            EventType::updateOrCreate(
                ['name' => $type['name']],
                array_merge($type, ['sort_order' => $index])
            );
        }
    }

    private function seedFeatures(): void
    {
        $features = [
            // Catering Features
            ['page' => 'catering', 'section' => 'intro', 'title' => 'Customizable Menus', 'icon' => 'fas fa-utensils', 'sort_order' => 1],
            ['page' => 'catering', 'section' => 'intro', 'title' => 'Professional Staff', 'icon' => 'fas fa-users', 'sort_order' => 2],
            ['page' => 'catering', 'section' => 'intro', 'title' => 'Fresh, Quality Ingredients', 'icon' => 'fas fa-leaf', 'sort_order' => 3],
            ['page' => 'catering', 'section' => 'intro', 'title' => 'On-Time Delivery', 'icon' => 'fas fa-truck', 'sort_order' => 4],
        ];

        foreach ($features as $feature) {
            Feature::updateOrCreate(
                ['page' => $feature['page'], 'title' => $feature['title']],
                $feature
            );
        }
    }

    private function getPrivacyPolicyContent(): string
    {
        return <<<HTML
<h3>Information We Collect</h3>
<p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support. This may include your name, email address, phone number, delivery address, and payment information.</p>

<h3>How We Use Your Information</h3>
<p>We use the information we collect to process your orders, communicate with you about our services, improve our website and services, and send you promotional materials (with your consent).</p>

<h3>Sharing Your Information</h3>
<p>We do not sell, trade, or otherwise transfer your personal information to third parties without your consent, except as necessary to provide our services (e.g., payment processors, delivery partners).</p>

<h3>Your Choices</h3>
<p>You may opt out of receiving promotional communications from us by following the instructions in those messages. You may also request access to, correction of, or deletion of your personal information by contacting us.</p>

<h3>Security Measures</h3>
<p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction.</p>

<h3>Changes to This Policy</h3>
<p>We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page.</p>

<h3>Contact Us</h3>
<p>If you have any questions about this Privacy Policy, please contact us at:</p>
<p>Email: support@ctake.com<br>Address: 800S, Salt Lake City, USA</p>
HTML;
    }

    private function getTermsConditionsContent(): string
    {
        return <<<HTML
<h3>Acceptance of Terms</h3>
<p>By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement.</p>

<h3>Use of Services</h3>
<p>You agree to use our services only for lawful purposes and in accordance with these Terms. You agree not to use our services in any way that could damage, disable, overburden, or impair our servers or networks.</p>

<h3>Orders and Payments</h3>
<p>All orders are subject to acceptance and availability. Prices are subject to change without notice. Payment must be received in full before orders are processed. We accept various payment methods as indicated on our website.</p>

<h3>Intellectual Property</h3>
<p>All content on this website, including text, graphics, logos, images, and software, is the property of CTAKE and is protected by copyright and other intellectual property laws.</p>

<h3>Limitation of Liability</h3>
<p>CTAKE shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising out of your use of our services.</p>

<h3>Changes to Terms</h3>
<p>We reserve the right to modify these terms at any time. Your continued use of our services after any changes indicates your acceptance of the new terms.</p>

<h3>Contact Us</h3>
<p>If you have any questions about these Terms, please contact us at:</p>
<p>Email: support@ctake.com<br>Address: 800S, Salt Lake City, USA</p>
HTML;
    }
}
