<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('info_cards', function (Blueprint $table) {
            $table->id();
            $table->string('page', 100);
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('icon')->nullable();
            $table->string('icon_image', 500)->nullable();
            $table->string('link', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('page');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('info_cards');
    }
};
