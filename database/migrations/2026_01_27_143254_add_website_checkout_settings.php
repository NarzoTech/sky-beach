<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default website checkout settings
        $settings = [
            // Tax Settings
            ['key' => 'website_tax_enabled', 'value' => '1'],
            ['key' => 'website_tax_rate', 'value' => '15'],

            // Delivery Fee Settings
            ['key' => 'website_delivery_fee_enabled', 'value' => '1'],
            ['key' => 'website_delivery_fee', 'value' => '50'],
            ['key' => 'website_free_delivery_threshold', 'value' => '0'],

            // Loyalty Points Settings
            ['key' => 'website_loyalty_enabled', 'value' => '1'],
        ];

        foreach ($settings as $setting) {
            // Only insert if key doesn't exist
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();
            if (!$exists) {
                DB::table('settings')->insert([
                    'key' => $setting['key'],
                    'value' => $setting['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'website_tax_enabled',
            'website_tax_rate',
            'website_delivery_fee_enabled',
            'website_delivery_fee',
            'website_free_delivery_threshold',
            'website_loyalty_enabled',
        ])->delete();
    }
};
