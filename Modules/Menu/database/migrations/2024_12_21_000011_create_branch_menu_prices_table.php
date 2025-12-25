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
        Schema::create('branch_menu_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id')->comment('Branch/Location');
            $table->unsignedBigInteger('menu_item_id');
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('menu_variants')->onDelete('cascade');
            $table->unique(['warehouse_id', 'menu_item_id', 'variant_id'], 'branch_menu_variant_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_menu_prices');
    }
};
