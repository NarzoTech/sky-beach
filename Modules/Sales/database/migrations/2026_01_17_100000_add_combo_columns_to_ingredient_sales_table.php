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
        Schema::table('ingredient_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('ingredient_sales', 'combo_id')) {
                $table->unsignedBigInteger('combo_id')->nullable()->after('service_id');
                $table->foreign('combo_id')->references('id')->on('combos')->nullOnDelete();
            }
            if (!Schema::hasColumn('ingredient_sales', 'combo_name')) {
                $table->string('combo_name')->nullable()->after('combo_id');
            }
            if (!Schema::hasColumn('ingredient_sales', 'note')) {
                $table->text('note')->nullable()->after('sub_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredient_sales', function (Blueprint $table) {
            if (Schema::hasColumn('ingredient_sales', 'combo_id')) {
                $table->dropForeign(['combo_id']);
                $table->dropColumn('combo_id');
            }
            if (Schema::hasColumn('ingredient_sales', 'combo_name')) {
                $table->dropColumn('combo_name');
            }
            if (Schema::hasColumn('ingredient_sales', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
