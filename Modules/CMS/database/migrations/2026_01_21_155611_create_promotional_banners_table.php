<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotional_banners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title', 500)->nullable();
            $table->string('subtitle', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('background_image', 500)->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link', 500)->nullable();
            $table->string('position', 100);
            $table->string('badge_text', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotional_banners');
    }
};
