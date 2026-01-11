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
        Schema::table('purchase_return_details', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_return_details', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('ingredient_id');
            }
            if (!Schema::hasColumn('purchase_return_details', 'base_quantity')) {
                $table->decimal('base_quantity', 15, 4)->nullable()->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_return_details', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_return_details', 'unit_id')) {
                $table->dropColumn('unit_id');
            }
            if (Schema::hasColumn('purchase_return_details', 'base_quantity')) {
                $table->dropColumn('base_quantity');
            }
        });
    }
};
