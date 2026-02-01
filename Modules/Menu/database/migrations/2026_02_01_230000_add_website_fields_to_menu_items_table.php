<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Pricing
            $table->decimal('discount_price', 12, 2)->nullable()->after('base_price');

            // Classification
            $table->string('cuisine_type')->nullable()->after('category_id');

            // Visibility toggles
            $table->boolean('available_in_pos')->default(true)->after('is_available');
            $table->boolean('available_in_website')->default(true)->after('available_in_pos');

            // Marketing flags
            $table->boolean('is_new')->default(false)->after('is_featured');
            $table->boolean('is_popular')->default(false)->after('is_new');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn([
                'discount_price',
                'cuisine_type',
                'available_in_pos',
                'available_in_website',
                'is_new',
                'is_popular',
            ]);
        });
    }
};
