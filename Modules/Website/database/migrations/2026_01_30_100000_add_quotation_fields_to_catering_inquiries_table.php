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
            // Check and add quotation fields if they don't exist
            if (!Schema::hasColumn('catering_inquiries', 'quotation_items')) {
                $table->json('quotation_items')->nullable()->after('quoted_amount');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_subtotal')) {
                $table->decimal('quotation_subtotal', 12, 2)->nullable()->after('quotation_items');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_discount')) {
                $table->decimal('quotation_discount', 12, 2)->default(0)->after('quotation_subtotal');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_discount_type')) {
                $table->enum('quotation_discount_type', ['fixed', 'percentage'])->default('fixed')->after('quotation_discount');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_tax_rate')) {
                $table->decimal('quotation_tax_rate', 5, 2)->default(0)->after('quotation_discount_type');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_tax_amount')) {
                $table->decimal('quotation_tax_amount', 12, 2)->default(0)->after('quotation_tax_rate');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_delivery_fee')) {
                $table->decimal('quotation_delivery_fee', 12, 2)->default(0)->after('quotation_tax_amount');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_notes')) {
                $table->text('quotation_notes')->nullable()->after('quotation_delivery_fee');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_valid_until')) {
                $table->date('quotation_valid_until')->nullable()->after('quotation_notes');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_terms')) {
                $table->text('quotation_terms')->nullable()->after('quotation_valid_until');
            }
            if (!Schema::hasColumn('catering_inquiries', 'quotation_number')) {
                $table->string('quotation_number')->nullable()->unique()->after('inquiry_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catering_inquiries', function (Blueprint $table) {
            $columns = [
                'quotation_items',
                'quotation_subtotal',
                'quotation_discount',
                'quotation_discount_type',
                'quotation_tax_rate',
                'quotation_tax_amount',
                'quotation_delivery_fee',
                'quotation_notes',
                'quotation_valid_until',
                'quotation_terms',
                'quotation_number',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('catering_inquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
