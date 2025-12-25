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
        Schema::create('pos_settings', function (Blueprint $table) {
            $table->id();
            $table->string('note_customer', 192)->default('Thank You For Shopping With Us . Please Come Again');
            $table->boolean('show_note')->default(1);
            $table->boolean('show_barcode')->default(1);
            $table->boolean('show_discount')->default(1);
            $table->boolean('show_customer')->default(1);
            $table->boolean('show_warehouse')->default(1);
            $table->boolean('show_email')->default(1);
            $table->boolean('show_phone')->default(1);
            $table->boolean('show_address')->default(1);
            $table->boolean('is_printable')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_settings');
    }
};
