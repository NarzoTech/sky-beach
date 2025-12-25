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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('supplier');
            $table->integer('warehouse_id')->nullable();
            $table->string('invoice_number');
            $table->string('memo_no')->nullable();
            $table->string('reference_no')->nullable();
            $table->date('purchase_date');
            $table->integer('items');
            $table->string('attachment')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2);
            $table->decimal('due_amount', 10, 2);
            $table->string('payment_status')->default('due');
            $table->json('payment_type')->nullable();
            $table->text('note')->nullable();
            $table->enum('status', ['pending', 'completed', 'canceled', 'return'])->default('pending');
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
        Schema::dropIfExists('purchases');
    }
};
