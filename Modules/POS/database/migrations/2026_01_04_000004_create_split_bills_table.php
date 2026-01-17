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
        Schema::create('split_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->string('label')->nullable(); // Guest 1, Guest 2, etc.
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });

        Schema::create('split_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('split_bill_id')->constrained('split_bills')->cascadeOnDelete();
            $table->foreignId('product_sale_id')->constrained('ingredient_sales')->cascadeOnDelete();
            $table->integer('quantity')->default(1); // Partial quantity assignment
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('split_bill_items');
        Schema::dropIfExists('split_bills');
    }
};
