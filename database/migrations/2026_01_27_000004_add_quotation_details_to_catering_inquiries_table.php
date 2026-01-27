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
        Schema::table('catering_inquiries', function (Blueprint $table) {
            $table->json('quotation_items')->nullable()->after('quoted_amount');
            $table->decimal('quotation_subtotal', 12, 2)->nullable()->after('quotation_items');
            $table->decimal('quotation_discount', 12, 2)->default(0)->after('quotation_subtotal');
            $table->string('quotation_discount_type')->default('fixed')->after('quotation_discount'); // fixed or percentage
            $table->decimal('quotation_tax_rate', 5, 2)->default(0)->after('quotation_discount_type');
            $table->decimal('quotation_tax_amount', 12, 2)->default(0)->after('quotation_tax_rate');
            $table->decimal('quotation_delivery_fee', 12, 2)->default(0)->after('quotation_tax_amount');
            $table->text('quotation_notes')->nullable()->after('quotation_delivery_fee');
            $table->date('quotation_valid_until')->nullable()->after('quotation_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catering_inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'quotation_items',
                'quotation_subtotal',
                'quotation_discount',
                'quotation_discount_type',
                'quotation_tax_rate',
                'quotation_tax_amount',
                'quotation_delivery_fee',
                'quotation_notes',
                'quotation_valid_until',
            ]);
        });
    }
};
