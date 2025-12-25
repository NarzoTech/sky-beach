<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Website\app\Models\WebsiteService;

class WebsiteServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'Private Chef Experience',
                'slug' => 'private-chef-experience',
                'short_description' => 'Enjoy a personalized dining experience in the comfort of your home',
                'description' => '<p>Our private chef service brings restaurant-quality cuisine directly to your home. Whether it\'s an intimate dinner for two or a celebration with friends and family, our experienced chefs will create a customized menu tailored to your preferences.</p><p>Each meal is prepared fresh using the finest ingredients, with meticulous attention to presentation and flavor. Let us take care of everything from menu planning to cleanup, so you can focus on enjoying your special occasion.</p>',
                'icon' => 'fa-utensils',
                'image' => 'website/images/service_1.jpg',
                'price' => 299.99,
                'duration' => 180,
                'order' => 1,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'title' => 'Catering Services',
                'slug' => 'catering-services',
                'short_description' => 'Professional catering for events of all sizes',
                'description' => '<p>Make your event unforgettable with our comprehensive catering services. We specialize in corporate events, weddings, birthday parties, and more. Our team will work with you to design a menu that fits your event theme and budget.</p><p>From elegant plated dinners to casual buffets, we handle everything including setup, service, and cleanup. Our experienced staff ensures your guests enjoy exceptional food and seamless service.</p>',
                'icon' => 'fa-concierge-bell',
                'image' => 'website/images/service_2.jpg',
                'price' => 25.00,
                'duration' => null,
                'order' => 2,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'title' => 'Cooking Classes',
                'slug' => 'cooking-classes',
                'short_description' => 'Learn culinary skills from professional chefs',
                'description' => '<p>Join our hands-on cooking classes and learn to create restaurant-quality dishes at home. Our expert chefs will guide you through techniques, recipes, and culinary secrets in a fun and interactive environment.</p><p>Classes are available for all skill levels, from beginners to advanced home cooks. Perfect for date nights, team building events, or anyone passionate about food!</p>',
                'icon' => 'fa-graduation-cap',
                'image' => 'website/images/service_3.jpg',
                'price' => 89.99,
                'duration' => 120,
                'order' => 3,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'title' => 'Meal Prep Service',
                'slug' => 'meal-prep-service',
                'short_description' => 'Healthy, ready-to-eat meals delivered to your door',
                'description' => '<p>Save time and eat healthy with our meal prep service. We prepare nutritious, delicious meals and deliver them fresh to your doorstep. Choose from our rotating menu or request custom meals based on your dietary preferences.</p><p>All meals are chef-prepared using fresh, high-quality ingredients and are ready to heat and eat. Perfect for busy professionals and health-conscious individuals.</p>',
                'icon' => 'fa-box',
                'image' => 'website/images/service_4.jpg',
                'price' => 12.99,
                'duration' => null,
                'order' => 4,
                'is_featured' => false,
                'status' => true,
            ],
        ];

        foreach ($services as $service) {
            WebsiteService::create($service);
        }
    }
}
