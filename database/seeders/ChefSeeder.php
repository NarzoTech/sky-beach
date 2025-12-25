<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Website\app\Models\Chef;

class ChefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chefs = [
            [
                'name' => 'John Anderson',
                'designation' => 'Executive Chef',
                'specialization' => 'French Cuisine',
                'bio' => 'With over 20 years of culinary experience, Chef John brings authentic French flavors to every dish. Trained at Le Cordon Bleu Paris, he has worked in Michelin-starred restaurants across Europe.',
                'image' => 'website/images/chef_1.jpg',
                'email' => 'john.anderson@ctake.com',
                'phone' => '+1 234 567 8901',
                'facebook' => 'https://facebook.com/chefjohn',
                'twitter' => 'https://twitter.com/chefjohn',
                'instagram' => 'https://instagram.com/chefjohn',
                'linkedin' => 'https://linkedin.com/in/chefjohn',
                'experience_years' => 20,
                'order' => 1,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Maria Garcia',
                'designation' => 'Head Pastry Chef',
                'specialization' => 'Desserts & Pastries',
                'bio' => 'Chef Maria is a master of sweet creations. Her innovative desserts combine traditional techniques with modern presentation, creating unforgettable dining experiences.',
                'image' => 'website/images/chef_2.jpg',
                'email' => 'maria.garcia@ctake.com',
                'phone' => '+1 234 567 8902',
                'facebook' => 'https://facebook.com/chefmaria',
                'twitter' => 'https://twitter.com/chefmaria',
                'instagram' => 'https://instagram.com/chefmaria',
                'linkedin' => 'https://linkedin.com/in/chefmaria',
                'experience_years' => 15,
                'order' => 2,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Kenji Tanaka',
                'designation' => 'Sushi Master',
                'specialization' => 'Japanese Cuisine',
                'bio' => 'Trained in Tokyo for over 10 years, Chef Kenji brings authentic Japanese culinary artistry to our kitchen. His sushi creations are works of edible art.',
                'image' => 'website/images/chef_3.jpg',
                'email' => 'kenji.tanaka@ctake.com',
                'phone' => '+1 234 567 8903',
                'facebook' => null,
                'twitter' => 'https://twitter.com/chefkenji',
                'instagram' => 'https://instagram.com/chefkenji',
                'linkedin' => null,
                'experience_years' => 18,
                'order' => 3,
                'is_featured' => true,
                'status' => true,
            ],
            [
                'name' => 'Isabella Romano',
                'designation' => 'Sous Chef',
                'specialization' => 'Italian Cuisine',
                'bio' => 'Growing up in Sicily, Chef Isabella learned the secrets of authentic Italian cooking from her grandmother. She specializes in traditional pasta and regional dishes.',
                'image' => 'website/images/chef_4.jpg',
                'email' => 'isabella.romano@ctake.com',
                'phone' => '+1 234 567 8904',
                'facebook' => 'https://facebook.com/chefisabella',
                'twitter' => null,
                'instagram' => 'https://instagram.com/chefisabella',
                'linkedin' => 'https://linkedin.com/in/chefisabella',
                'experience_years' => 12,
                'order' => 4,
                'is_featured' => false,
                'status' => true,
            ],
        ];

        foreach ($chefs as $chef) {
            Chef::create($chef);
        }
    }
}
