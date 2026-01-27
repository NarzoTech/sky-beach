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
        // First, convert existing integer status values to string values
        // 0 = pending, 1 = completed, 2 = cancelled
        DB::table('sales')->where('status', 0)->update(['status' => DB::raw("'pending'")]);
        DB::table('sales')->where('status', 1)->update(['status' => DB::raw("'completed'")]);
        DB::table('sales')->where('status', 2)->update(['status' => DB::raw("'cancelled'")]);

        // Change the column type from integer to string
        Schema::table('sales', function (Blueprint $table) {
            $table->string('status', 50)->nullable()->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert string values back to integers
        DB::table('sales')->where('status', 'pending')->update(['status' => 0]);
        DB::table('sales')->where('status', 'confirmed')->update(['status' => 0]);
        DB::table('sales')->where('status', 'preparing')->update(['status' => 0]);
        DB::table('sales')->where('status', 'ready')->update(['status' => 0]);
        DB::table('sales')->where('status', 'out_for_delivery')->update(['status' => 0]);
        DB::table('sales')->where('status', 'delivered')->update(['status' => 1]);
        DB::table('sales')->where('status', 'completed')->update(['status' => 1]);
        DB::table('sales')->where('status', 'cancelled')->update(['status' => 2]);

        // Change the column type back to integer
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('status')->nullable()->change();
        });
    }
};
