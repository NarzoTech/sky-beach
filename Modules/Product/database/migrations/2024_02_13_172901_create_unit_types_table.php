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
        Schema::create('unit_types', function (Blueprint $table) {
            $table->id();
            $table->string("name", 250);
            $table->string('ShortName', 192);
            $table->integer('base_unit')->nullable()->index('base_unit');
            $table->char('operator', 192)->nullable()->default('*');
            $table->float('operator_value', 10, 0)->nullable()->default(1);
            $table->boolean("status")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_types');
    }
};
