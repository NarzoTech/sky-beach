<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('page', 100);
            $table->string('section_key', 100);
            $table->string('title', 500)->nullable();
            $table->string('subtitle', 500)->nullable();
            $table->text('content')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('background_image', 500)->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link', 500)->nullable();
            $table->json('extra_data')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['page', 'section_key']);
            $table->index('page');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
