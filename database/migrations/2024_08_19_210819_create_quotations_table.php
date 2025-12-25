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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('quotation_no')->default(1);
            $table->date('date');
            $table->text('note')->nullable();
            $table->text('reference_note')->nullable();
            $table->float('subtotal');
            $table->string('discount')->default(0);
            $table->float('after_discount')->default(0);
            $table->string('vat')->default(0);
            $table->float('total');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
