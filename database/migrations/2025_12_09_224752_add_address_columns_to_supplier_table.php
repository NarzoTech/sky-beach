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
        Schema::table('supplier', function (Blueprint $table) {
            $table->string('city', 255)->nullable()->after('updated_at');
            $table->string('state', 255)->nullable()->after('city');
            $table->string('country', 255)->nullable()->after('state');
            $table->string('tax_number', 255)->nullable()->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier', function (Blueprint $table) {
            $table->dropColumn(['city', 'state', 'country', 'tax_number']);
        });
    }
};
