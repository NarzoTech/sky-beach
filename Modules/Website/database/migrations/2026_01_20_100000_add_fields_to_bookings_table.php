<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('booking_number')->constrained()->onDelete('set null');
            $table->string('confirmation_code', 10)->unique()->nullable()->after('admin_notes');
            $table->boolean('reminder_sent')->default(false)->after('confirmation_code');
            $table->timestamp('cancelled_at')->nullable()->after('reminder_sent');
            $table->string('cancelled_reason')->nullable()->after('cancelled_at');
        });

        // Update status enum to include 'no_show'
        // Note: This is MySQL-specific. For other databases, you may need different approach
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed', 'no_show') DEFAULT 'pending'");

        // Generate confirmation codes for existing bookings
        DB::table('bookings')->whereNull('confirmation_code')->get()->each(function ($booking) {
            DB::table('bookings')
                ->where('id', $booking->id)
                ->update(['confirmation_code' => strtoupper(\Illuminate\Support\Str::random(8))]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'confirmation_code', 'reminder_sent', 'cancelled_at', 'cancelled_reason']);
        });

        // Revert status enum
        DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending'");
    }
};
