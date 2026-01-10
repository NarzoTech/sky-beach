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
        Schema::create('loyalty_customer_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_program_id')->constrained('loyalty_programs')->onDelete('cascade');

            $table->string('name');
            $table->text('description')->nullable();

            // Segment Criteria
            $table->decimal('min_lifetime_points', 10, 2)->default(0);
            $table->decimal('max_lifetime_points', 10, 2)->nullable();
            $table->integer('min_transactions')->default(0);
            $table->decimal('min_spent', 10, 2)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('loyalty_program_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_customer_segments');
    }
};
