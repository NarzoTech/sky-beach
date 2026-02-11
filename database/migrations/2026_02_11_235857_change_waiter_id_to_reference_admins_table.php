<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Map existing waiter_id (employee IDs) to admin IDs
        // Using employees.admin_id to find the corresponding admin
        DB::statement('
            UPDATE sales
            SET waiter_id = (
                SELECT employees.admin_id
                FROM employees
                WHERE employees.id = sales.waiter_id
            )
            WHERE waiter_id IS NOT NULL
        ');

        DB::statement('
            UPDATE order_notifications
            SET waiter_id = (
                SELECT employees.admin_id
                FROM employees
                WHERE employees.id = order_notifications.waiter_id
            )
            WHERE waiter_id IS NOT NULL
        ');

        // Step 2: Drop old foreign keys and add new ones referencing admins
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['waiter_id']);
            $table->foreign('waiter_id')->references('id')->on('admins')->onDelete('set null');
        });

        Schema::table('order_notifications', function (Blueprint $table) {
            $table->dropForeign(['waiter_id']);
            $table->foreign('waiter_id')->references('id')->on('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Map admin IDs back to employee IDs
        DB::statement('
            UPDATE sales
            SET waiter_id = (
                SELECT employees.id
                FROM employees
                WHERE employees.admin_id = sales.waiter_id
            )
            WHERE waiter_id IS NOT NULL
        ');

        DB::statement('
            UPDATE order_notifications
            SET waiter_id = (
                SELECT employees.id
                FROM employees
                WHERE employees.admin_id = order_notifications.waiter_id
            )
            WHERE waiter_id IS NOT NULL
        ');

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['waiter_id']);
            $table->foreign('waiter_id')->references('id')->on('employees')->onDelete('set null');
        });

        Schema::table('order_notifications', function (Blueprint $table) {
            $table->dropForeign(['waiter_id']);
            $table->foreign('waiter_id')->references('id')->on('employees')->nullOnDelete();
        });
    }
};
