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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('purchase_id')->nullable();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('purchase_return_id')->nullable();
            $table->unsignedBigInteger('sale_return_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->date('date')->nullable();
            $table->integer('in_quantity')->default(0);
            $table->integer('out_quantity')->default(0);
            $table->string('invoice')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('type')->nullable();
            $table->string('sku')->nullable();
            $table->integer('available_qty')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('profit', 10, 2)->default(0);
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->foreignId('created_by')->constrained('admins');
            $table->foreignId('updated_by')->nullable()->constrained('admins');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
