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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('purchase_id')->nullable()->constrained('purchases');
            $table->foreignId('sale_id')->nullable()->constrained('sales');
            $table->foreignId('expense_id')->nullable()->constrained('expenses');
            $table->foreignId('sale_return_id')->nullable()->constrained('sales');
            $table->foreignId('supplier_id')->nullable()->constrained('supplier');
            $table->foreignId('customer_id')->nullable()->constrained('users');
            $table->foreignId('account_id')->nullable()->constrained('accounts');
            $table->boolean('is_guest')->nullable()->default(false);
            $table->boolean('is_received')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->string('payment_type');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('admins');
            $table->foreignId('updated_by')->nullable()->constrained('admins');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
