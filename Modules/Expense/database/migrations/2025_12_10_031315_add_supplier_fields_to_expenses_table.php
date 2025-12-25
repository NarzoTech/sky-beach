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
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('expense_supplier_id')->nullable()->after('expense_type_id')->constrained('expense_suppliers')->onDelete('set null');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('amount');
            $table->decimal('due_amount', 15, 2)->default(0)->after('paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_supplier_id']);
            $table->dropColumn(['expense_supplier_id', 'paid_amount', 'due_amount']);
        });
    }
};
