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
        Schema::create('menu_item_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_item_id');
            $table->unsignedBigInteger('addon_id');
            $table->integer('max_quantity')->default(1);
            $table->boolean('is_required')->default(0);
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->onDelete('cascade');
            $table->foreign('addon_id')->references('id')->on('menu_addons')->onDelete('cascade');
            $table->unique(['menu_item_id', 'addon_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_addons');
    }
};
