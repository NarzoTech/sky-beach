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
        // Add occupied_seats to restaurant_tables for partial occupancy tracking
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->integer('occupied_seats')->default(0)->after('capacity');
        });

        // Add guest_count to sales for tracking how many guests per order
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('guest_count')->default(1)->after('table_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropColumn('occupied_seats');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('guest_count');
        });
    }
};
