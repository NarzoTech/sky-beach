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
            $table->json('addons')->nullable()->after('attributes');
            $table->decimal('addons_price', 15, 2)->default(0)->after('addons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredient_sales', function (Blueprint $table) {
            $table->dropColumn(['addons', 'addons_price']);
        });
    }
};
