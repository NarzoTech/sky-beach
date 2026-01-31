<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            'frontend_logo' => '',
            'frontend_favicon' => '',
            'frontend_footer_logo' => '',
        ];

        foreach ($settings as $key => $value) {
            // Check if setting already exists
            $exists = DB::table('settings')->where('key', $key)->exists();

            if (!$exists) {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
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
            'frontend_logo',
            'frontend_favicon',
            'frontend_footer_logo',
        ])->delete();
    }
};
