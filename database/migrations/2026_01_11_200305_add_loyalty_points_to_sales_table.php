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
            $table->decimal('points_redeemed', 12, 2)->default(0)->after('grand_total');
            $table->decimal('points_discount', 12, 2)->default(0)->after('points_redeemed');
            $table->decimal('points_earned', 12, 2)->default(0)->after('points_discount');
            $table->unsignedBigInteger('loyalty_customer_id')->nullable()->after('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['points_redeemed', 'points_discount', 'points_earned', 'loyalty_customer_id']);
        });
    }
};
