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
        if (Schema::hasTable('ingredients') && !Schema::hasColumn('ingredients', 'warranty')) {
            Schema::table('ingredients', function (Blueprint $table) {
                $table->string('warranty')->nullable()->after('stock_alert');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ingredients') && Schema::hasColumn('ingredients', 'warranty')) {
            Schema::table('ingredients', function (Blueprint $table) {
                $table->dropColumn('warranty');
            });
        }
    }
};
