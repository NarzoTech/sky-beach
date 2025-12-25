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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('area_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('membership')->nullable();
            $table->date('date')->nullable();
            $table->string('address')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('guest')->default(0);
            $table->decimal('wallet_balance', 8, 2)->default(0.00)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
