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
        Schema::table('product_sales', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id')->nullable()->after('product_id');
            $table->decimal('base_quantity', 20, 4)->nullable()->after('quantity')->comment('Quantity converted to product base unit');
            
            $table->foreign('unit_id')->references('id')->on('unit_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_sales', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'base_quantity']);
        });
    }
};
