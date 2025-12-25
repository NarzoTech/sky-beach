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
        Schema::create('combo_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('combo_id');
            $table->unsignedBigInteger('menu_item_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->foreign('combo_id')->references('id')->on('combos')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('menu_variants')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combo_items');
    }
};
