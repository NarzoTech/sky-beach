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
        Schema::create('sales_return', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->date('order_date');
            $table->date('return_date');
            $table->float('return_amount');
            $table->float('return_due')->default(0);
            $table->string('note')->nullable();
            $table->boolean('status')->default(1);
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return');
    }
};
