<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('uid', 36)->nullable()->unique()->after('id');
        });

        // Generate UIDs for existing orders
        $sales = \Modules\Sales\app\Models\Sale::whereNull('uid')->get();
        foreach ($sales as $sale) {
            $sale->update(['uid' => Str::uuid()->toString()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('uid');
        });
    }
};
