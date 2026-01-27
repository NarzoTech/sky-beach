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
        Schema::table('website_carts', function (Blueprint $table) {
            $table->unsignedBigInteger('combo_id')->nullable()->after('menu_item_id');
            $table->foreign('combo_id')->references('id')->on('combos')->onDelete('cascade');

            // Make menu_item_id nullable since cart can have either menu item OR combo
            $table->unsignedBigInteger('menu_item_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_carts', function (Blueprint $table) {
            $table->dropForeign(['combo_id']);
            $table->dropColumn('combo_id');
        });
    }
};
