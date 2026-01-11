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
            $table->unsignedBigInteger('waiter_id')->nullable()->after('table_id');
            $table->foreign('waiter_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['waiter_id']);
            $table->dropColumn('waiter_id');
        });
    }
};
