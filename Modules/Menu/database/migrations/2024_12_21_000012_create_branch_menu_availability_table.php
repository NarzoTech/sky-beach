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
        Schema::create('branch_menu_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id')->comment('Branch/Location');
            $table->unsignedBigInteger('menu_item_id');
            $table->boolean('is_available')->default(1);
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->unique(['warehouse_id', 'menu_item_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_menu_availability');
    }
};
