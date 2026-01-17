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
            $table->enum('kitchen_status', ['pending', 'preparing', 'ready', 'served', 'cancelled'])->default('pending')->after('sub_total');
            $table->timestamp('status_updated_at')->nullable()->after('kitchen_status');
            $table->text('void_reason')->nullable()->after('status_updated_at');
            $table->boolean('is_voided')->default(false)->after('void_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredient_sales', function (Blueprint $table) {
            $table->dropColumn(['kitchen_status', 'status_updated_at', 'void_reason', 'is_voided']);
        });
    }
};
