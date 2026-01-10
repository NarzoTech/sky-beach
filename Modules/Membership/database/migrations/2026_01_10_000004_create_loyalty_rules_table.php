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
        Schema::create('loyalty_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loyalty_program_id')->constrained('loyalty_programs')->onDelete('cascade');

            // Rule Details
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            // Condition Type
            $table->enum('condition_type', ['category', 'item', 'amount', 'time_period', 'customer_group'])->default('amount');
            $table->json('condition_value')->nullable();

            // Action Type
            $table->enum('action_type', ['earn_points', 'bonus_points', 'multiply_points', 'redeem_discount'])->default('earn_points');
            $table->decimal('action_value', 8, 2);

            // Timing
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('day_of_week')->nullable()->comment('["MON", "TUE", ...] or null for all days');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Applicability
            $table->enum('applies_to', ['all', 'specific_items', 'specific_categories', 'specific_customers'])->default('all');
            $table->json('applicable_items')->nullable();
            $table->json('applicable_categories')->nullable();
            $table->json('applicable_customer_segments')->nullable();

            // Priority
            $table->integer('priority')->default(0);

            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->index('loyalty_program_id');
            $table->index('is_active');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_rules');
    }
};
