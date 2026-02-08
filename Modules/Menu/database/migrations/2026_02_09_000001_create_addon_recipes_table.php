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
        Schema::create('addon_recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_addon_id');
            $table->unsignedBigInteger('ingredient_id')->comment('Ingredient used in addon recipe');
            $table->decimal('quantity_required', 12, 4)->default(0);
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('menu_addon_id')->references('id')->on('menu_addons')->onDelete('cascade');
            $table->unique(['menu_addon_id', 'ingredient_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addon_recipes');
    }
};
