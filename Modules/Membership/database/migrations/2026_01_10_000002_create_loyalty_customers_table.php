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
        Schema::create('loyalty_customers', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->unique()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();
            $table->string('email')->nullable();

            // Points Balance
            $table->decimal('total_points', 10, 2)->default(0);
            $table->decimal('lifetime_points', 10, 2)->default(0);
            $table->decimal('redeemed_points', 10, 2)->default(0);

            // Status & Preferences
            $table->enum('status', ['active', 'blocked', 'suspended'])->default('active');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('last_purchase_at')->nullable();
            $table->timestamp('last_redemption_at')->nullable();

            // Preferences
            $table->boolean('opt_in_sms')->default(true);
            $table->boolean('opt_in_email')->default(true);

            $table->timestamps();

            $table->index('phone');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_customers');
    }
};
