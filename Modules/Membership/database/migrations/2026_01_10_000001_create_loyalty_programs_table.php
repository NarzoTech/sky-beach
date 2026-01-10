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
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->string('name')->default('Default Loyalty Program');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            // Points Earning Rules
            $table->enum('earning_type', ['per_transaction', 'per_amount'])->default('per_amount');
            $table->decimal('earning_rate', 8, 2)->default(1.00)->comment('e.g., 1 point per $1');
            $table->decimal('min_transaction_amount', 10, 2)->nullable()->comment('Minimum transaction for earning');

            // Redemption Rules
            $table->enum('redemption_type', ['discount', 'free_item', 'cashback'])->default('discount');
            $table->decimal('points_per_unit', 8, 2)->default(100.00)->comment('e.g., 100 points = $1 discount');

            // Rules Configuration (JSON)
            $table->json('earning_rules')->nullable()->comment('Additional earning rules conditions');
            $table->json('redemption_rules')->nullable()->comment('Redemption constraints');

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index('warehouse_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_programs');
    }
};
