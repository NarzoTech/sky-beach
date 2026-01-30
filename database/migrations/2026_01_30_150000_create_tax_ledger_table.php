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
        Schema::create('tax_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->onDelete('cascade');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('cascade');
            $table->foreignId('tax_id')->nullable()->constrained('taxes')->onDelete('set null');
            $table->string('tax_name')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->enum('type', ['collected', 'paid', 'adjustment'])->default('collected');
            $table->string('reference_type')->nullable(); // 'sale', 'purchase', 'refund', 'adjustment'
            $table->string('reference_number')->nullable(); // Invoice number
            $table->decimal('taxable_amount', 15, 2)->default(0); // Amount before tax
            $table->decimal('tax_amount', 15, 2)->default(0); // Tax amount
            $table->date('transaction_date');
            $table->date('period_start')->nullable(); // For reporting period
            $table->date('period_end')->nullable();
            $table->string('description')->nullable();
            $table->enum('status', ['active', 'voided', 'adjusted'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('voided_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('voided_at')->nullable();
            $table->string('void_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for reporting
            $table->index('transaction_date');
            $table->index('type');
            $table->index('status');
            $table->index(['period_start', 'period_end']);
        });

        // Tax period summary table for quick reporting
        Schema::create('tax_period_summaries', function (Blueprint $table) {
            $table->id();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_tax_collected', 15, 2)->default(0);
            $table->decimal('total_tax_paid', 15, 2)->default(0);
            $table->decimal('net_tax_payable', 15, 2)->default(0);
            $table->decimal('total_taxable_sales', 15, 2)->default(0);
            $table->decimal('total_taxable_purchases', 15, 2)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->enum('status', ['open', 'closed', 'filed'])->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_period_summaries');
        Schema::dropIfExists('tax_ledger');
    }
};
