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
        Schema::table('sales', function (Blueprint $table) {
            // Total COGS for this sale
            $table->decimal('total_cogs', 15, 4)->nullable()->after('grand_total');
            // Total Gross Profit (Revenue - COGS)
            $table->decimal('gross_profit', 15, 4)->nullable()->after('total_cogs');
            // Profit margin percentage
            $table->decimal('profit_margin', 8, 2)->nullable()->after('gross_profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['total_cogs', 'gross_profit', 'profit_margin']);
        });
    }
};
