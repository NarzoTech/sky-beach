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
            $table->json('table_transfer_log')->nullable()->after('table_id');
            $table->foreignId('original_table_id')->nullable()->after('table_transfer_log')->constrained('restaurant_tables')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['original_table_id']);
            $table->dropColumn(['table_transfer_log', 'original_table_id']);
        });
    }
};
