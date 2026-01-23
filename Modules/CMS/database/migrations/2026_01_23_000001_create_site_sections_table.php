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
        Schema::create('site_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_name');                    // hero_banner, popular_categories, etc.
            $table->string('page_name')->default('home');      // home, about, contact, menu, etc.
            $table->string('image')->nullable();               // Single image
            $table->text('images')->nullable();                // JSON array of images
            $table->string('background_image')->nullable();    // Background image
            $table->string('video')->nullable();               // Video URL
            $table->integer('quantity')->nullable();           // Number of items to show
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('button_text_2')->nullable();       // Secondary button
            $table->string('button_link_2')->nullable();
            $table->text('extra_data')->nullable();            // JSON for additional settings
            $table->boolean('section_status')->default(true);  // Show/hide section
            $table->boolean('show_search')->default(false);    // Show search bar (for hero)
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['section_name', 'page_name']);
            $table->index('page_name');
            $table->index('section_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_sections');
    }
};
