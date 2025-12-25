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
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('sub_total', 10, 2);
            $table->decimal('profit', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->decimal('discount', 10, 2);
            $table->decimal('tax', 10, 2);
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
        Schema::dropIfExists('purchase_details');
    }
};
