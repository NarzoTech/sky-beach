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
        //delete attendances table if exists

        Schema::dropIfExists('attendances');

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'weekend']);
            $table->boolean('is_leave')->default(false);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
