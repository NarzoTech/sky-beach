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
        Schema::create('product_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('sale_id');
            $table->integer('product_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('sale_unit_id')->nullable();
            $table->string('product_sku')->nullable();
            $table->string('variant_id')->nullable();
            $table->string('attributes')->nullable();
            $table->tinyInteger('source')->default(1)->comment(
                '1: From Stock, 2: From Out side'
            );

            $table->double('price');
            $table->double('purchase_price')->nullable();
            $table->double('selling_price')->nullable();
            $table->double('tax')->default(0);
            $table->double('discount')->default(0);
            $table->double('sub_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sales');
    }
};
