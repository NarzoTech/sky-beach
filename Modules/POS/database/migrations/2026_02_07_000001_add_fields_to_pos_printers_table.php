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
        Schema::table('pos_printers', function (Blueprint $table) {
            $table->string('capability_profile')->default('default')->after('paper_width');
            $table->integer('char_per_line')->default(42)->after('capability_profile');
            $table->string('path')->nullable()->after('port');
        });

        // Change connection_type from enum to string to support windows/linux
        Schema::table('pos_printers', function (Blueprint $table) {
            $table->string('connection_type')->default('browser')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_printers', function (Blueprint $table) {
            $table->dropColumn(['capability_profile', 'char_per_line', 'path']);
        });
    }
};
