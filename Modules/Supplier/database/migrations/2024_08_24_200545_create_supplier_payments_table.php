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
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases');
            $table->foreignId('supplier_id')->nullable()->constrained('supplier');
            $table->foreignId('account_id')->nullable()->constrained('accounts');
            $table->unsignedBigInteger('purchase_return_id')->nullable();
            $table->string('invoice')->nullable();
            $table->boolean('is_guest')->nullable()->default(false);
            $table->boolean('is_received')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->string('payment_type');
            $table->string('account_type')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->constrained('admins');
            $table->foreignId('updated_by')->nullable()->constrained('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
