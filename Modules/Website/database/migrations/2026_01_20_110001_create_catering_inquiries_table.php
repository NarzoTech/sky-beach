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
        Schema::create('catering_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_number')->unique();
            $table->foreignId('package_id')->nullable()->constrained('catering_packages')->onDelete('set null');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('event_type');
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->integer('guest_count');
            $table->text('venue_address')->nullable();
            $table->text('special_requirements')->nullable();
            $table->enum('status', ['pending', 'contacted', 'quoted', 'confirmed', 'cancelled'])->default('pending');
            $table->decimal('quoted_amount', 10, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('phone');
            $table->index('status');
            $table->index('event_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catering_inquiries');
    }
};
