<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('short_description')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('image')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->float('stock_alert', 10, 0)->nullable()->default(0);
            $table->boolean('is_imei')->default(0);
            $table->boolean('not_selling')->default(0);
            $table->integer('stock')->default(0);
            $table->enum('stock_status', ['in_stock', 'out_of_stock'])->default('in_stock');
            $table->string('sku')->nullable();
            $table->boolean('status')->default(1);
            $table->string("tax_type", 50)->nullable();
            $table->double("tax", 16, 2)->default(0)->nullable();
            $table->boolean('is_favorite')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
