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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_item_id');
            $table->unsignedBigInteger('product_id')->comment('Product used as ingredient');
            $table->decimal('quantity_required', 12, 4)->default(0);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('unit_types')->onDelete('set null');
            $table->unique(['menu_item_id', 'product_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
