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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('id')->constrained('admins')->nullOnDelete();
            $table->boolean('is_waiter')->default(false)->after('status');
            $table->string('pin_code', 6)->nullable()->after('is_waiter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['admin_id', 'is_waiter', 'pin_code']);
        });
    }
};
