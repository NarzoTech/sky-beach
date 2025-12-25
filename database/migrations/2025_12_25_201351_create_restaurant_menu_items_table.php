<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('restaurant_menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->string('cuisine_type')->nullable();
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_spicy')->default(false);
            $table->string('spice_level')->nullable()->comment('mild, medium, hot, extra hot');
            $table->text('ingredients')->nullable();
            $table->text('allergens')->nullable();
            $table->integer('preparation_time')->nullable()->comment('in minutes');
            $table->integer('calories')->nullable();
            $table->boolean('available_in_pos')->default(true);
            $table->boolean('available_in_website')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_popular')->default(false);
            $table->integer('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_menu_items');
    }
};
