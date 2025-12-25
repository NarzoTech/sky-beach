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
        Schema::create('menu_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_item_id');
            $table->string('name');
            $table->decimal('price_adjustment', 12, 2)->default(0)->comment('+ or - from base price');
            $table->boolean('is_default')->default(0);
            $table->boolean('status')->default(1);
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_variants');
    }
};
