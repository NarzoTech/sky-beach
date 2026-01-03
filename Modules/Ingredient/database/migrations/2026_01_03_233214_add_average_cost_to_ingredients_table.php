<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            // Weighted average cost per purchase unit
            $table->decimal('average_cost', 15, 4)->nullable()->after('purchase_price');
        });

        // Initialize average_cost with current purchase_price
        DB::table('ingredients')->whereNull('average_cost')->update([
            'average_cost' => DB::raw('purchase_price')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('average_cost');
        });
    }
};
