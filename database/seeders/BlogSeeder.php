<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Website\app\Models\Blog;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogs = [
            [
                'title' => 'The Wonders of Thai Cuisine: Sweet, Salty & Sour',
                'slug' => 'wonders-of-thai-cuisine',
                'short_description' => 'Discover the perfect balance of flavors in authentic Thai dishes that have captivated food lovers worldwide.',
                'description' => '<p>Thai cuisine is renowned for its harmonious blend of sweet, salty, sour, and spicy flavors. Each dish is carefully crafted to achieve the perfect balance that tantalizes your taste buds.</p><p>From the iconic Pad Thai to the aromatic Tom Yum soup, Thai food represents centuries of culinary tradition passed down through generations. The use of fresh herbs like lemongrass, galangal, and Thai basil creates unforgettable aromatic experiences.</p><p>Join us on a culinary journey as we explore the secrets behind these amazing dishes and learn what makes Thai cuisine one of the most beloved in the world.</p>',
                'image' => 'website/images/footer_post_img_1.jpg',
                'author' => 'Chef Maria Santos',
                'tags' => 'Thai Food, Asian Cuisine, Food Culture',
                'views' => 1250,
                'is_featured' => true,
                'status' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Pairing Wine with Indian Food: Tips from a Sommelier',
                'slug' => 'pairing-wine-with-indian-food',
                'short_description' => 'Expert sommelier advice on selecting the perfect wines to complement rich and spicy Indian cuisine.',
                'description' => '<p>Many people believe that wine and Indian food don\'t pair well, but that couldn\'t be further from the truth! The key is understanding the complex flavor profiles of Indian dishes and matching them with the right wines.</p><p>For rich, creamy curries like butter chicken, try a full-bodied Chardonnay. The wine\'s buttery notes complement the dish beautifully. For spicier dishes like vindaloo, opt for off-dry Riesling or Gewürztraminer - their slight sweetness helps tame the heat.</p><p>Discover more pairing secrets and elevate your Indian dining experience to new heights!</p>',
                'image' => 'website/images/footer_post_img_2.jpg',
                'author' => 'Michael Chen',
                'tags' => 'Wine Pairing, Indian Food, Sommelier Tips',
                'views' => 890,
                'is_featured' => true,
                'status' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => '10 Essential Spices Every Home Cook Should Have',
                'slug' => '10-essential-spices-home-cook',
                'short_description' => 'Build your spice collection with these must-have ingredients that will transform your cooking.',
                'description' => '<p>A well-stocked spice cabinet is the foundation of great cooking. Whether you\'re a beginner or an experienced cook, these 10 essential spices will help you create delicious meals from various cuisines.</p><p>From the warmth of cinnamon to the earthiness of cumin, each spice brings unique flavors and aromas to your dishes. Learn how to use them properly, how to store them for maximum freshness, and which dishes they work best in.</p><p>Transform your everyday meals into restaurant-quality dishes with these essential spices!</p>',
                'image' => null,
                'author' => 'Chef Maria Santos',
                'tags' => 'Cooking Tips, Spices, Home Cooking',
                'views' => 2100,
                'is_featured' => false,
                'status' => true,
                'published_at' => now()->subDays(10),
            ],
            [
                'title' => 'The Art of Making Perfect Pizza Dough',
                'slug' => 'art-of-perfect-pizza-dough',
                'short_description' => 'Master the techniques for creating authentic Italian pizza dough at home.',
                'description' => '<p>Making pizza dough from scratch is easier than you think! With just a few simple ingredients and some patience, you can create restaurant-quality pizza at home.</p><p>The secret lies in understanding the fermentation process and giving your dough enough time to develop flavor. We\'ll walk you through everything from selecting the right flour to achieving that perfect crispy-yet-chewy texture.</p><p>Get ready to impress your family and friends with homemade pizzas that rival your favorite pizzeria!</p>',
                'image' => null,
                'author' => 'Giovanni Russo',
                'tags' => 'Pizza, Baking, Italian Cuisine',
                'views' => 1580,
                'is_featured' => false,
                'status' => true,
                'published_at' => now()->subDays(15),
            ],
            [
                'title' => 'Healthy Eating: Mediterranean Diet Benefits',
                'slug' => 'mediterranean-diet-benefits',
                'short_description' => 'Explore the health benefits of the Mediterranean diet and how to incorporate it into your lifestyle.',
                'description' => '<p>The Mediterranean diet has been recognized as one of the healthiest eating patterns in the world. Rich in vegetables, fruits, whole grains, and healthy fats, this diet offers numerous health benefits.</p><p>Studies have shown that following a Mediterranean diet can reduce the risk of heart disease, improve brain health, and help maintain a healthy weight. The emphasis on fresh, seasonal ingredients and minimal processed foods makes it sustainable and delicious.</p><p>Learn how to adopt Mediterranean eating habits and enjoy the flavors of the Mediterranean region!</p>',
                'image' => null,
                'author' => 'Dr. Sarah Williams',
                'tags' => 'Healthy Eating, Mediterranean, Nutrition',
                'views' => 945,
                'is_featured' => false,
                'status' => true,
                'published_at' => now()->subDays(20),
            ],
        ];

        foreach ($blogs as $blog) {
            Blog::create($blog);
        }
    }
}
