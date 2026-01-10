<?php

namespace Modules\Membership\app\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyRedemption extends Model
{
    use HasFactory;

    protected $table = 'loyalty_redemptions';

    protected $fillable = [
        'loyalty_customer_id',
        'sale_id',
        'points_used',
        'redemption_type',
        'amount_value',
        'menu_item_id',
        'ingredient_id',
        'quantity',
        'status',
        'created_by',
    ];

    protected $casts = [
        'points_used' => 'decimal:2',
        'amount_value' => 'decimal:2',
    ];

    /**
     * Relations
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(LoyaltyCustomer::class, 'loyalty_customer_id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(\Modules\Sales\app\Models\Sale::class, 'sale_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeCustomer($query, $customerId)
    {
        return $query->where('loyalty_customer_id', $customerId);
    }

    public function scopeType($query, $type)
    {
        return $query->where('redemption_type', $type);
    }

    /**
     * Methods
     */
    public function isDiscount()
    {
        return $this->redemption_type === 'discount';
    }

    public function isFreeItem()
    {
        return $this->redemption_type === 'free_item';
    }

    public function isCashback()
    {
        return $this->redemption_type === 'cashback';
    }

    public function isApplied()
    {
        return $this->status === 'applied';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}
