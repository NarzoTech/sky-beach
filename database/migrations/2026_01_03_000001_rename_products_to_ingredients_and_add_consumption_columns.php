<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run if both tables exist (ingredients created in module, unit_types in settings module)
        if (!Schema::hasTable('ingredients') || !Schema::hasTable('unit_types')) {
            return;
        }

        // Add new columns to ingredients table for consumption calculations
        Schema::table('ingredients', function (Blueprint $table) {
            if (!Schema::hasColumn('ingredients', 'purchase_unit_id')) {
                $table->unsignedBigInteger('purchase_unit_id')->nullable()->after('unit_id');
            }
            if (!Schema::hasColumn('ingredients', 'consumption_unit_id')) {
                $table->unsignedBigInteger('consumption_unit_id')->nullable()->after('purchase_unit_id');
            }
            if (!Schema::hasColumn('ingredients', 'conversion_rate')) {
                $table->decimal('conversion_rate', 15, 4)->nullable()->default(1)->after('consumption_unit_id');
            }
            if (!Schema::hasColumn('ingredients', 'purchase_price')) {
                $table->decimal('purchase_price', 15, 4)->nullable()->default(0)->after('conversion_rate');
            }
            if (!Schema::hasColumn('ingredients', 'consumption_unit_cost')) {
                $table->decimal('consumption_unit_cost', 15, 4)->nullable()->default(0)->after('purchase_price');
            }
        });

        // Add foreign keys if they don't exist
        Schema::table('ingredients', function (Blueprint $table) {
            // Check if foreign key exists before adding
            $foreignKeys = $this->getTableForeignKeys('ingredients');

            if (!in_array('ingredients_purchase_unit_id_foreign', $foreignKeys)) {
                $table->foreign('purchase_unit_id')->references('id')->on('unit_types')->onDelete('set null');
            }
            if (!in_array('ingredients_consumption_unit_id_foreign', $foreignKeys)) {
                $table->foreign('consumption_unit_id')->references('id')->on('unit_types')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ingredients')) {
            return;
        }

        Schema::table('ingredients', function (Blueprint $table) {
            $foreignKeys = $this->getTableForeignKeys('ingredients');

            if (in_array('ingredients_purchase_unit_id_foreign', $foreignKeys)) {
                $table->dropForeign(['purchase_unit_id']);
            }
            if (in_array('ingredients_consumption_unit_id_foreign', $foreignKeys)) {
                $table->dropForeign(['consumption_unit_id']);
            }

            $columns = [];
            if (Schema::hasColumn('ingredients', 'purchase_unit_id')) {
                $columns[] = 'purchase_unit_id';
            }
            if (Schema::hasColumn('ingredients', 'consumption_unit_id')) {
                $columns[] = 'consumption_unit_id';
            }
            if (Schema::hasColumn('ingredients', 'conversion_rate')) {
                $columns[] = 'conversion_rate';
            }
            if (Schema::hasColumn('ingredients', 'purchase_price')) {
                $columns[] = 'purchase_price';
            }
            if (Schema::hasColumn('ingredients', 'consumption_unit_cost')) {
                $columns[] = 'consumption_unit_cost';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }

    /**
     * Get foreign keys for a table
     */
    private function getTableForeignKeys(string $tableName): array
    {
        $foreignKeys = [];

        try {
            $results = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ", [$tableName]);

            foreach ($results as $result) {
                $foreignKeys[] = $result->CONSTRAINT_NAME;
            }
        } catch (\Exception $e) {
            // If query fails, return empty array
        }

        return $foreignKeys;
    }
};
