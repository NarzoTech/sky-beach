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
        Schema::create('pos_printers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['cash_counter', 'kitchen'])->default('cash_counter');
            $table->enum('connection_type', ['network', 'usb', 'bluetooth', 'browser'])->default('browser');
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->integer('paper_width')->default(80); // 80mm or 58mm
            $table->boolean('is_active')->default(true);
            $table->json('print_categories')->nullable(); // Categories to print for kitchen printer
            $table->string('location_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_printers');
    }
};
