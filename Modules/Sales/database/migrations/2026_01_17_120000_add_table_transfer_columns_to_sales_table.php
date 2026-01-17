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
            if (!Schema::hasColumn('sales', 'original_table_id')) {
                $table->unsignedBigInteger('original_table_id')->nullable()->after('table_id');
            }
            if (!Schema::hasColumn('sales', 'table_transfer_log')) {
                $table->json('table_transfer_log')->nullable()->after('original_table_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'original_table_id')) {
                $table->dropColumn('original_table_id');
            }
            if (Schema::hasColumn('sales', 'table_transfer_log')) {
                $table->dropColumn('table_transfer_log');
            }
        });
    }
};
