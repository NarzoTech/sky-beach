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
        Schema::table('combos', function (Blueprint $table) {
            $table->json('gallery')->nullable()->after('image');
            $table->integer('display_order')->default(0)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('combos', function (Blueprint $table) {
            $table->dropColumn(['gallery', 'display_order']);
        });
    }
};
