<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Website\app\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'What are your opening hours?',
                'answer' => 'We are open Monday to Thursday from 11:00 AM to 10:00 PM, Friday and Saturday from 11:00 AM to 11:00 PM, and Sunday from 11:00 AM to 9:00 PM.',
                'category' => 'General',
                'order' => 1,
                'status' => true,
            ],
            [
                'question' => 'Do you accept reservations?',
                'answer' => 'Yes, we highly recommend making a reservation, especially for dinner service and weekends. You can book online through our website or call us directly.',
                'category' => 'Reservations',
                'order' => 2,
                'status' => true,
            ],
            [
                'question' => 'Do you offer vegetarian and vegan options?',
                'answer' => 'Absolutely! We have a variety of vegetarian and vegan dishes on our menu. All items are clearly marked, and our staff can help you find the perfect meal for your dietary preferences.',
                'category' => 'Menu',
                'order' => 3,
                'status' => true,
            ],
            [
                'question' => 'Can you accommodate food allergies?',
                'answer' => 'Yes, we take food allergies very seriously. Please inform your server about any allergies or dietary restrictions, and our kitchen staff will prepare your meal accordingly. Common allergens are listed on our menu.',
                'category' => 'Menu',
                'order' => 4,
                'status' => true,
            ],
            [
                'question' => 'Do you offer delivery or takeout?',
                'answer' => 'Yes! We offer both delivery and takeout services. You can order through our website, by phone, or through popular delivery apps. Minimum order requirements may apply for delivery.',
                'category' => 'Delivery',
                'order' => 5,
                'status' => true,
            ],
            [
                'question' => 'Do you have parking available?',
                'answer' => 'Yes, we have a dedicated parking lot with ample space for our guests. Additionally, there is street parking available nearby.',
                'category' => 'General',
                'order' => 6,
                'status' => true,
            ],
            [
                'question' => 'Can I host a private event at your restaurant?',
                'answer' => 'Yes! We have private dining rooms available for events such as birthdays, anniversaries, corporate gatherings, and more. Please contact us to discuss your event requirements and menu options.',
                'category' => 'Events',
                'order' => 7,
                'status' => true,
            ],
            [
                'question' => 'Do you offer gift cards?',
                'answer' => 'Yes, gift cards are available for purchase in any denomination. They make perfect gifts for food lovers and can be purchased online or at our restaurant.',
                'category' => 'General',
                'order' => 8,
                'status' => true,
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept cash, all major credit cards (Visa, MasterCard, American Express, Discover), and mobile payment methods such as Apple Pay and Google Pay.',
                'category' => 'Payment',
                'order' => 9,
                'status' => true,
            ],
            [
                'question' => 'Is there a dress code?',
                'answer' => 'We have a smart casual dress code. While we want you to be comfortable, we ask that guests refrain from wearing athletic wear, flip-flops, or overly casual attire.',
                'category' => 'General',
                'order' => 10,
                'status' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
