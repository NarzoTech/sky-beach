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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->integer('user_id')->nullable();
            $table->boolean('walk_in_customer')->nullable();
            $table->integer('address_id')->nullable();
            $table->double('delivery_fee')->nullable();
            $table->double('tax')->nullable();
            $table->double('discount')->nullable();
            $table->date('order_delivery_date')->nullable();
            $table->text('payment_details')->nullable();
            $table->text('payment_notes')->nullable();
            $table->text('order_note')->nullable();
            $table->decimal('total_amount', 8, 2);
            $table->decimal('paid_amount', 8, 2);
            $table->date('due_date')->nullable();
            $table->decimal('due_amount', 8, 2)->default(0);
            $table->decimal('receive_amount', 8, 2)->default(0);
            $table->decimal('return_amount', 8, 2)->default(0);
            $table->string('transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('created_by')->nullable();
            $table->enum('payment_status', ['pending', 'success', 'rejected'])->default('pending');
            $table->enum('order_status', ['pending', 'success', 'rejected', 'cancelled'])->default('pending');
            $table->integer('delivery_method')->nullable()->default(1)->comment('1- Delivery, 2- Pickup');
            $table->integer('delivery_status')->nullable()->default(1)->comment('1- Pending, 2- Accept, 3- Progress, 4- On the way, 5- Delivered, 6- Cancelled');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
