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
        Schema::table('ingredient_sales', function (Blueprint $table) {
            // COGS = Cost of Goods Sold (total ingredient cost for this line item)
            $table->decimal('cogs_amount', 15, 4)->nullable()->after('sub_total');
            // Profit = Revenue - COGS
            $table->decimal('profit_amount', 15, 4)->nullable()->after('cogs_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredient_sales', function (Blueprint $table) {
            $table->dropColumn(['cogs_amount', 'profit_amount']);
        });
    }
};
