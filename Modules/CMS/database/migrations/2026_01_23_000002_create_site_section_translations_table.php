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
        Schema::create('site_section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_section_id')->constrained('site_sections')->onDelete('cascade');
            $table->string('lang_code', 10)->default('en');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();               // JSON for extra translatable content
            $table->timestamps();

            $table->unique(['site_section_id', 'lang_code']);
            $table->index('lang_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_section_translations');
    }
};
