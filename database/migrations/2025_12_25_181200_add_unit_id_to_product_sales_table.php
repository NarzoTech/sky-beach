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
        if (Schema::hasTable('ingredient_sales') && Schema::hasTable('unit_types')) {
            Schema::table('ingredient_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('ingredient_sales', 'unit_id')) {
                    $table->unsignedBigInteger('unit_id')->nullable()->after('ingredient_id');
                }
                if (!Schema::hasColumn('ingredient_sales', 'base_quantity')) {
                    $table->decimal('base_quantity', 20, 4)->nullable()->after('quantity')->comment('Quantity converted to product base unit');
                }
            });

            // Add foreign key separately to avoid issues
            if (Schema::hasColumn('ingredient_sales', 'unit_id')) {
                Schema::table('ingredient_sales', function (Blueprint $table) {
                    $table->foreign('unit_id')->references('id')->on('unit_types')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ingredient_sales')) {
            Schema::table('ingredient_sales', function (Blueprint $table) {
                if (Schema::hasColumn('ingredient_sales', 'unit_id')) {
                    $table->dropForeign(['unit_id']);
                    $table->dropColumn(['unit_id']);
                }
                if (Schema::hasColumn('ingredient_sales', 'base_quantity')) {
                    $table->dropColumn(['base_quantity']);
                }
            });
        }
    }
};
