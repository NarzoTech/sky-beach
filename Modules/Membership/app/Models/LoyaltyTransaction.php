<?php

namespace Modules\Membership\app\Models;

use App\Models\Admin;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyTransaction extends Model
{
    use HasFactory;

    protected $table = 'loyalty_transactions';

    protected $fillable = [
        'loyalty_customer_id',
        'warehouse_id',
        'transaction_type',
        'points_amount',
        'points_balance_before',
        'points_balance_after',
        'source_type',
        'source_id',
        'redemption_method',
        'redemption_value',
        'description',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'points_amount' => 'decimal:2',
        'points_balance_before' => 'decimal:2',
        'points_balance_after' => 'decimal:2',
        'redemption_value' => 'decimal:2',
    ];

    /**
     * Relations
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'loyalty_customer_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeEarnings($query)
    {
        return $query->where('transaction_type', 'earn');
    }

    public function scopeRedemptions($query)
    {
        return $query->where('transaction_type', 'redeem');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('transaction_type', 'adjust');
    }

    public function scopeExpiries($query)
    {
        return $query->where('transaction_type', 'expire');
    }

    public function scopeFromSale($query)
    {
        return $query->where('source_type', 'sale');
    }

    public function scopeFromManualAdjust($query)
    {
        return $query->where('source_type', 'manual_adjust');
    }

    public function scopeCustomer($query, $customerId)
    {
        return $query->where('loyalty_customer_id', $customerId);
    }

    public function scopeWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Methods
     */
    public function isEarning()
    {
        return $this->transaction_type === 'earn';
    }

    public function isRedemption()
    {
        return $this->transaction_type === 'redeem';
    }

    public function isAdjustment()
    {
        return $this->transaction_type === 'adjust';
    }

    public function isFromSale()
    {
        return $this->source_type === 'sale';
    }
}
