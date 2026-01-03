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
        Schema::create('order_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('ingredient_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('status')->default(true);
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            // Foreign key for ingredient_id will be handled by the ingredient module
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_reviews');
    }
};
