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
        Schema::table('stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_id')->nullable()->after('product_id');
            $table->decimal('base_in_quantity', 20, 4)->nullable()->after('in_quantity')->comment('In quantity in product base unit');
            $table->decimal('base_out_quantity', 20, 4)->nullable()->after('out_quantity')->comment('Out quantity in product base unit');
            
            $table->foreign('unit_id')->references('id')->on('unit_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'base_in_quantity', 'base_out_quantity']);
        });
    }
};
