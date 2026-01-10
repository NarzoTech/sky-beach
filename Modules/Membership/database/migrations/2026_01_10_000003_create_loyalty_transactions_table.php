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
        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_customer_id')->constrained('loyalty_customers')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');

            // Transaction Details
            $table->enum('transaction_type', ['earn', 'redeem', 'adjust', 'expire'])->default('earn');
            $table->decimal('points_amount', 10, 2);
            $table->decimal('points_balance_before', 10, 2)->nullable();
            $table->decimal('points_balance_after', 10, 2)->nullable();

            // Source of Transaction
            $table->enum('source_type', ['sale', 'manual_adjust', 'refund', 'expiry'])->default('sale');
            $table->unsignedBigInteger('source_id')->nullable();

            // Redemption Details (if applicable)
            $table->enum('redemption_method', ['discount', 'free_item', 'cashback'])->nullable();
            $table->decimal('redemption_value', 10, 2)->nullable();

            // Metadata
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamps();

            $table->index('loyalty_customer_id');
            $table->index('warehouse_id');
            $table->index('transaction_type');
            $table->index('source_type');
            $table->index(['source_type', 'source_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
    }
};
