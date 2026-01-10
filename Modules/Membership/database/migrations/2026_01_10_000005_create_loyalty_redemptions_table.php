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
        Schema::create('loyalty_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_customer_id')->constrained('loyalty_customers')->onDelete('cascade');
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();

            // Redemption Details
            $table->decimal('points_used', 10, 2);
            $table->enum('redemption_type', ['discount', 'free_item', 'cashback']);
            $table->decimal('amount_value', 10, 2)->nullable()->comment('Discount amount or cashback value');

            // Free Item (if applicable)
            $table->unsignedBigInteger('menu_item_id')->nullable();
            $table->unsignedBigInteger('ingredient_id')->nullable();
            $table->integer('quantity')->default(1);

            // Status
            $table->enum('status', ['pending', 'applied', 'cancelled'])->default('applied');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index('loyalty_customer_id');
            $table->index('sale_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_redemptions');
    }
};
