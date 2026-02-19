<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedBigInteger('loyalty_customer_id')->nullable()->after('is_active');
            $table->boolean('is_loyalty_reward')->default(false)->after('loyalty_customer_id');

            $table->foreign('loyalty_customer_id')
                  ->references('id')
                  ->on('loyalty_customers')
                  ->onDelete('set null');

            $table->index('is_loyalty_reward');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->string('coupon_code')->nullable()->after('order_discount');
            $table->unsignedBigInteger('coupon_id')->nullable()->after('coupon_code');
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropForeign(['loyalty_customer_id']);
            $table->dropIndex(['is_loyalty_reward']);
            $table->dropColumn(['loyalty_customer_id', 'is_loyalty_reward']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'coupon_id']);
        });
    }
};
