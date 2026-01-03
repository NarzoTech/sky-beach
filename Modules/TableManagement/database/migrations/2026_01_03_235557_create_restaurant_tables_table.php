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
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Table name (e.g., "Table 1", "VIP 1")
            $table->string('table_number')->unique();  // Unique table number
            $table->integer('capacity')->default(4);   // Number of seats
            $table->string('floor')->nullable();       // Floor/Section (e.g., "Ground Floor", "Rooftop")
            $table->string('section')->nullable();     // Section/Area (e.g., "Indoor", "Outdoor", "VIP")
            $table->enum('shape', ['square', 'round', 'rectangle'])->default('square');
            $table->integer('position_x')->default(0); // X position for visual layout
            $table->integer('position_y')->default(0); // Y position for visual layout
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->foreignId('current_sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'is_active']);
            $table->index(['floor', 'section']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
