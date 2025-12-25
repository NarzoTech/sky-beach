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
        Schema::table('expense_types', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('expense_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_types', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
