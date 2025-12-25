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
        Schema::create('balances', function (Blueprint $table) {
            $table->id();
            $table->enum('balance_type', ['deposit', 'withdraw'])->default('deposit');
            $table->string('payment_type');
            $table->date('date');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balances');
    }
};
