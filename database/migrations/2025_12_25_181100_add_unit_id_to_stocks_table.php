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
        if (Schema::hasTable('stocks') && Schema::hasTable('unit_types')) {
            Schema::table('stocks', function (Blueprint $table) {
                if (!Schema::hasColumn('stocks', 'unit_id')) {
                    $table->unsignedBigInteger('unit_id')->nullable()->after('ingredient_id');
                }
                if (!Schema::hasColumn('stocks', 'base_in_quantity')) {
                    $table->decimal('base_in_quantity', 20, 4)->nullable()->after('in_quantity')->comment('In quantity in product base unit');
                }
                if (!Schema::hasColumn('stocks', 'base_out_quantity')) {
                    $table->decimal('base_out_quantity', 20, 4)->nullable()->after('out_quantity')->comment('Out quantity in product base unit');
                }
            });

            // Add foreign key separately to avoid issues
            if (Schema::hasColumn('stocks', 'unit_id')) {
                Schema::table('stocks', function (Blueprint $table) {
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
        if (Schema::hasTable('stocks')) {
            Schema::table('stocks', function (Blueprint $table) {
                if (Schema::hasColumn('stocks', 'unit_id')) {
                    $table->dropForeign(['unit_id']);
                    $table->dropColumn(['unit_id']);
                }
                if (Schema::hasColumn('stocks', 'base_in_quantity')) {
                    $table->dropColumn(['base_in_quantity']);
                }
                if (Schema::hasColumn('stocks', 'base_out_quantity')) {
                    $table->dropColumn(['base_out_quantity']);
                }
            });
        }
    }
};
