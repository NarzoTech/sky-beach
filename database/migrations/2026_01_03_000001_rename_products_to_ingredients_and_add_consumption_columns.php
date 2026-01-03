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
        // Add new columns to products table for consumption calculations
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_unit_id')->nullable()->after('unit_id');
            $table->unsignedBigInteger('consumption_unit_id')->nullable()->after('purchase_unit_id');
            $table->decimal('conversion_rate', 15, 4)->nullable()->default(1)->after('consumption_unit_id');
            $table->decimal('purchase_price', 15, 4)->nullable()->default(0)->after('conversion_rate');
            $table->decimal('consumption_unit_cost', 15, 4)->nullable()->default(0)->after('purchase_price');

            // Add foreign keys
            $table->foreign('purchase_unit_id')->references('id')->on('unit_types')->onDelete('set null');
            $table->foreign('consumption_unit_id')->references('id')->on('unit_types')->onDelete('set null');
        });

        // Rename products table to ingredients
        Schema::rename('products', 'ingredients');

        // Rename product_brands table to ingredient_brands
        if (Schema::hasTable('product_brands')) {
            Schema::rename('product_brands', 'ingredient_brands');
        }

        // Rename product_categories table to ingredient_categories
        if (Schema::hasTable('product_categories')) {
            Schema::rename('product_categories', 'ingredient_categories');
        }

        // Rename product_attributes table to ingredient_attributes
        if (Schema::hasTable('product_attributes')) {
            Schema::rename('product_attributes', 'ingredient_attributes');
        }

        // Rename product_sales table to ingredient_sales
        if (Schema::hasTable('product_sales')) {
            Schema::rename('product_sales', 'ingredient_sales');
        }

        // Rename product_id to ingredient_id in related tables
        $this->renameProductIdColumn('stocks');
        $this->renameProductIdColumn('purchase_details');
        $this->renameProductIdColumn('order_details');
        $this->renameProductIdColumn('carts');
        $this->renameProductIdColumn('quotation_details');
        $this->renameProductIdColumn('ingredient_sales');
        $this->renameProductIdColumn('sales_return_details');
        $this->renameProductIdColumn('purchase_return_details');
        $this->renameProductIdColumn('ingredient_attributes');
        $this->renameProductIdColumn('variants');

        // Rename brand_id references in ingredients table to ingredient_brand_id if needed
        // The brand_id column can remain as is since it references ingredient_brands.id
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename ingredient_id back to product_id in related tables
        $this->renameIngredientIdColumn('stocks');
        $this->renameIngredientIdColumn('purchase_details');
        $this->renameIngredientIdColumn('order_details');
        $this->renameIngredientIdColumn('carts');
        $this->renameIngredientIdColumn('quotation_details');
        $this->renameIngredientIdColumn('ingredient_sales');
        $this->renameIngredientIdColumn('sales_return_details');
        $this->renameIngredientIdColumn('purchase_return_details');
        $this->renameIngredientIdColumn('ingredient_attributes');
        $this->renameIngredientIdColumn('variants');

        // Rename tables back
        if (Schema::hasTable('ingredient_sales')) {
            Schema::rename('ingredient_sales', 'product_sales');
        }

        if (Schema::hasTable('ingredient_attributes')) {
            Schema::rename('ingredient_attributes', 'product_attributes');
        }

        if (Schema::hasTable('ingredient_categories')) {
            Schema::rename('ingredient_categories', 'product_categories');
        }

        if (Schema::hasTable('ingredient_brands')) {
            Schema::rename('ingredient_brands', 'product_brands');
        }

        Schema::rename('ingredients', 'products');

        // Remove added columns
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['purchase_unit_id']);
            $table->dropForeign(['consumption_unit_id']);
            $table->dropColumn(['purchase_unit_id', 'consumption_unit_id', 'conversion_rate', 'purchase_price', 'consumption_unit_cost']);
        });
    }

    /**
     * Rename product_id column to ingredient_id in a table
     */
    private function renameProductIdColumn(string $tableName): void
    {
        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'product_id')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('product_id', 'ingredient_id');
            });
        }
    }

    /**
     * Rename ingredient_id column back to product_id in a table
     */
    private function renameIngredientIdColumn(string $tableName): void
    {
        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'ingredient_id')) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->renameColumn('ingredient_id', 'product_id');
            });
        }
    }
};
