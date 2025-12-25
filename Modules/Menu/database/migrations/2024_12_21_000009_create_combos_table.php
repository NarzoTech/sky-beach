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
        Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('combo_price', 12, 2)->default(0);
            $table->decimal('original_price', 12, 2)->default(0)->comment('Sum of individual items');
            $table->enum('discount_type', ['percentage', 'fixed'])->default('fixed');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combos');
    }
};
