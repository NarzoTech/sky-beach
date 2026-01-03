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
        Schema::create('sales_return_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_return_id');
            $table->unsignedBigInteger('ingredient_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->tinyInteger('source')->default(1)->comment(
                '1: From Stock, 2: From Out side'
            );
            $table->string('quantity');
            $table->string('price');
            $table->string('sub_total');
            $table->foreign('sale_return_id')->references('id')->on('sales_return')->onDelete('cascade');
            // Foreign key for ingredient_id will be handled by the ingredient module
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_details');
    }
};
