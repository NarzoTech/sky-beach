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
        Schema::create('menu_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_category_id');
            $table->string('locale', 10);
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreign('menu_category_id')->references('id')->on('menu_categories')->onDelete('cascade');
            $table->unique(['menu_category_id', 'locale']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_category_translations');
    }
};
