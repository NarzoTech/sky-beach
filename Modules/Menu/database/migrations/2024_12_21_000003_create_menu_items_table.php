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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->integer('preparation_time')->nullable()->comment('in minutes');
            $table->integer('calories')->nullable();
            $table->boolean('is_vegetarian')->default(0);
            $table->boolean('is_vegan')->default(0);
            $table->boolean('is_spicy')->default(0);
            $table->tinyInteger('spice_level')->default(0)->comment('0-5 scale');
            $table->json('allergens')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_available')->default(1);
            $table->boolean('status')->default(1);
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->integer('display_order')->default(0);
            $table->foreign('category_id')->references('id')->on('menu_categories')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
