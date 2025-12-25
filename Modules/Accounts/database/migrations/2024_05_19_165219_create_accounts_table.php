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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_type');
            $table->string('mobile_bank_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->integer('bank_id')->nullable();
            $table->decimal('service_charge')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_holder_name')->nullable();
            $table->string('card_number')->nullable();
            $table->string('bank_account_type')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_branch')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
