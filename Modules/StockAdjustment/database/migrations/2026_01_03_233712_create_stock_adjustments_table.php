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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique();
            $table->foreignId('ingredient_id')->constrained('ingredients')->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->enum('adjustment_type', [
                'wastage',      // Spoilage, expired
                'damage',       // Physical damage
                'theft',        // Stolen
                'correction',   // Manual correction (can be + or -)
                'transfer_out', // Transfer to another warehouse
                'transfer_in',  // Received from another warehouse
                'consumption',  // Internal use (staff meal, testing)
                'other'         // Other adjustments
            ]);
            $table->decimal('quantity', 15, 4);  // Positive = increase, Negative = decrease
            $table->foreignId('unit_id')->nullable()->constrained('unit_types')->nullOnDelete();
            $table->decimal('cost_per_unit', 15, 4)->nullable(); // Cost at time of adjustment
            $table->decimal('total_cost', 15, 4)->nullable();    // quantity × cost_per_unit
            $table->date('adjustment_date');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ingredient_id', 'adjustment_date']);
            $table->index(['adjustment_type', 'adjustment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
