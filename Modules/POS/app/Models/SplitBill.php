<?php

namespace Modules\POS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Sales\app\Models\Sale;

class SplitBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'label',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'paid_amount',
        'payment_status',
        'payment_method',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    const STATUS_UNPAID = 'unpaid';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';

    /**
     * Get the parent sale
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the split bill items
     */
    public function items()
    {
        return $this->hasMany(SplitBillItem::class);
    }

    /**
     * Calculate totals based on items
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum('amount');
        $taxRate = $this->sale->tax_rate ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);

        $this->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $subtotal + $taxAmount,
        ]);
    }

    /**
     * Process payment for this split
     */
    public function processPayment(float $amount, string $method): void
    {
        $newPaidAmount = $this->paid_amount + $amount;

        $status = self::STATUS_UNPAID;
        if ($newPaidAmount >= $this->total) {
            $status = self::STATUS_PAID;
        } elseif ($newPaidAmount > 0) {
            $status = self::STATUS_PARTIAL;
        }

        $this->update([
            'paid_amount' => $newPaidAmount,
            'payment_status' => $status,
            'payment_method' => $method,
        ]);
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAttribute(): float
    {
        return max(0, $this->total - $this->paid_amount);
    }

    /**
     * Check if fully paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === self::STATUS_PAID;
    }
}
