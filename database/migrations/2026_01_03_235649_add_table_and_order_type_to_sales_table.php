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
            $table->enum('order_type', ['dine_in', 'take_away', 'delivery'])->default('dine_in')->after('status');
            $table->foreignId('table_id')->nullable()->after('order_type');
            $table->string('delivery_address')->nullable()->after('table_id');
            $table->string('delivery_phone')->nullable()->after('delivery_address');
            $table->text('delivery_notes')->nullable()->after('delivery_phone');
        });

        // Add foreign key separately to handle existing data
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('table_id')->references('id')->on('restaurant_tables')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['table_id']);
            $table->dropColumn(['order_type', 'table_id', 'delivery_address', 'delivery_phone', 'delivery_notes']);
        });
    }
};
