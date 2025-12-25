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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->string('payment_type')->default('cash');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->text('account_details')->nullable();
            $table->boolean('payment_status')->default(1);
            $table->string('note')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->unsignedBigInteger('expense_type_id')->nullable();
            $table->foreign('expense_type_id')->references('id')->on('expense_types')->onDelete('set null');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
