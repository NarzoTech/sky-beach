<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change stock from int to decimal to support fractional quantities
        // Stock is stored in purchase units (kg, ltr, etc.) and deductions
        // are often fractional (e.g., 0.8 kg for 800g recipe)
        DB::statement('ALTER TABLE ingredients MODIFY stock DECIMAL(15,4) NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE ingredients MODIFY stock INT NOT NULL DEFAULT 0');
    }
};
