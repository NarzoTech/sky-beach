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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('return_type_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->date('return_date')->nullable();
            $table->text('note')->nullable();
            $table->string('payment_method')->default('cash');
            $table->string('attachment')->nullable();
            $table->string('invoice')->nullable();
            $table->integer('received_amount')->default(0);
            $table->integer('return_amount')->default(0);
            $table->boolean('payment_status')->default(1);
            $table->float('shipping_cost', 10, 0)->default(0);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
