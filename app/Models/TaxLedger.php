<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Sales\app\Models\Sale;
use Modules\Purchase\app\Models\Purchase;
use Modules\Tax\app\Models\Tax;

class TaxLedger extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tax_ledger';

    protected $fillable = [
        'sale_id',
        'purchase_id',
        'tax_id',
        'tax_name',
        'tax_rate',
        'type',
        'reference_type',
        'reference_number',
        'taxable_amount',
        'tax_amount',
        'transaction_date',
        'period_start',
        'period_end',
        'description',
        'status',
        'created_by',
        'voided_by',
        'voided_at',
        'void_reason',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'transaction_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'voided_at' => 'datetime',
    ];

    // Relationships
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    // Scopes
    public function scopeCollected($query)
    {
        return $query->where('type', 'collected');
    }

    public function scopePaid($query)
    {
        return $query->where('type', 'paid');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeForSale($query, $saleId)
    {
        return $query->where('sale_id', $saleId);
    }

    // Static methods for creating entries
    public static function recordSaleTax(Sale $sale, $taxRate = null, $taxAmount = null, $taxId = null)
    {
        $taxRate = $taxRate ?? $sale->tax_rate ?? 0;
        $taxAmount = $taxAmount ?? $sale->total_tax ?? 0;

        if ($taxAmount <= 0) {
            return null;
        }

        // Calculate taxable amount
        $taxableAmount = $taxRate > 0 ? ($taxAmount / $taxRate) * 100 : $sale->total_price;

        // Determine period (monthly)
        $transactionDate = $sale->created_at ?? now();
        $periodStart = $transactionDate->copy()->startOfMonth();
        $periodEnd = $transactionDate->copy()->endOfMonth();

        return self::create([
            'sale_id' => $sale->id,
            'tax_id' => $taxId,
            'tax_name' => $taxId ? Tax::find($taxId)?->name : 'Sales Tax',
            'tax_rate' => $taxRate,
            'type' => 'collected',
            'reference_type' => 'sale',
            'reference_number' => $sale->invoice,
            'taxable_amount' => $taxableAmount,
            'tax_amount' => $taxAmount,
            'transaction_date' => $transactionDate->toDateString(),
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'description' => 'Tax collected from sale ' . $sale->invoice,
            'status' => 'active',
            'created_by' => auth()->id() ?? $sale->created_by ?? 1,
        ]);
    }

    public static function recordPurchaseTax(Purchase $purchase, $taxRate, $taxAmount, $taxId = null)
    {
        if ($taxAmount <= 0) {
            return null;
        }

        $taxableAmount = $taxRate > 0 ? ($taxAmount / $taxRate) * 100 : $purchase->total_amount;

        $transactionDate = $purchase->date ?? now();
        $periodStart = $transactionDate->copy()->startOfMonth();
        $periodEnd = $transactionDate->copy()->endOfMonth();

        return self::create([
            'purchase_id' => $purchase->id,
            'tax_id' => $taxId,
            'tax_name' => $taxId ? Tax::find($taxId)?->name : 'Purchase Tax',
            'tax_rate' => $taxRate,
            'type' => 'paid',
            'reference_type' => 'purchase',
            'reference_number' => $purchase->invoice,
            'taxable_amount' => $taxableAmount,
            'tax_amount' => $taxAmount,
            'transaction_date' => $transactionDate->toDateString(),
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'description' => 'Tax paid on purchase ' . $purchase->invoice,
            'status' => 'active',
            'created_by' => auth()->id() ?? 1,
        ]);
    }

    public static function voidSaleTax(Sale $sale, $reason = null)
    {
        return self::where('sale_id', $sale->id)
            ->where('status', 'active')
            ->update([
                'status' => 'voided',
                'voided_by' => auth()->id(),
                'voided_at' => now(),
                'void_reason' => $reason ?? 'Sale cancelled/voided',
            ]);
    }

    public static function getTaxSummaryForPeriod($startDate, $endDate)
    {
        $collected = self::active()
            ->collected()
            ->forPeriod($startDate, $endDate)
            ->sum('tax_amount');

        $paid = self::active()
            ->paid()
            ->forPeriod($startDate, $endDate)
            ->sum('tax_amount');

        $taxablesSales = self::active()
            ->collected()
            ->forPeriod($startDate, $endDate)
            ->sum('taxable_amount');

        $taxablePurchases = self::active()
            ->paid()
            ->forPeriod($startDate, $endDate)
            ->sum('taxable_amount');

        $transactionCount = self::active()
            ->forPeriod($startDate, $endDate)
            ->count();

        return [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'total_tax_collected' => $collected,
            'total_tax_paid' => $paid,
            'net_tax_payable' => $collected - $paid,
            'total_taxable_sales' => $taxablesSales,
            'total_taxable_purchases' => $taxablePurchases,
            'total_transactions' => $transactionCount,
        ];
    }
}
