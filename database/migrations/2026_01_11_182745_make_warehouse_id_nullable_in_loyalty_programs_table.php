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
        Schema::table('loyalty_programs', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['warehouse_id']);

            // Make column nullable
            $table->unsignedBigInteger('warehouse_id')->nullable()->change();

            // Re-add foreign key with nullOnDelete
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_programs', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->unsignedBigInteger('warehouse_id')->nullable(false)->change();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
        });
    }
};
