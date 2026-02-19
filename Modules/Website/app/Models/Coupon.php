<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'loyalty_customer_id',
        'is_loyalty_reward',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'is_loyalty_reward' => 'boolean',
    ];

    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';

    /**
     * Coupon usages
     */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Loyalty customer who redeemed points for this coupon
     */
    public function loyaltyCustomer()
    {
        return $this->belongsTo(\Modules\Membership\app\Models\LoyaltyCustomer::class);
    }

    /**
     * Scope: Loyalty reward coupons
     */
    public function scopeLoyaltyRewards($query)
    {
        return $query->where('is_loyalty_reward', true);
    }

    /**
     * Create a one-time coupon from loyalty points redemption
     */
    public static function createFromLoyaltyRedemption(int $loyaltyCustomerId, float $discountAmount): self
    {
        do {
            $code = 'LR-' . strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return self::create([
            'code' => $code,
            'name' => __('Loyalty Reward'),
            'description' => __('Auto-generated from loyalty points redemption'),
            'type' => self::TYPE_FIXED,
            'value' => $discountAmount,
            'min_order_amount' => 0,
            'max_discount' => null,
            'usage_limit' => 1,
            'usage_limit_per_user' => 1,
            'used_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addHours(24),
            'is_active' => true,
            'loyalty_customer_id' => $loyaltyCustomerId,
            'is_loyalty_reward' => true,
        ]);
    }

    /**
     * Scope: Active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Valid coupons (within date range)
     */
    public function scopeValid($query)
    {
        $today = now()->toDateString();

        return $query->where(function ($q) use ($today) {
            $q->whereNull('valid_from')
              ->orWhere('valid_from', '<=', $today);
        })->where(function ($q) use ($today) {
            $q->whereNull('valid_until')
              ->orWhere('valid_until', '>=', $today);
        });
    }

    /**
     * Scope: Available coupons (not exhausted)
     */
    public function scopeAvailable($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('used_count < usage_limit');
        });
    }

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->valid_from && $this->valid_from->toDateString() > $today) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->toDateString() < $today) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if user can use this coupon
     */
    public function canBeUsedBy(string $userIdentifier): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $userUsageCount = $this->usages()
            ->where('user_identifier', $userIdentifier)
            ->count();

        return $userUsageCount < $this->usage_limit_per_user;
    }

    /**
     * Calculate discount for given order amount
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if ($orderAmount < $this->min_order_amount) {
            return 0;
        }

        if ($this->type === self::TYPE_PERCENTAGE) {
            $discount = ($orderAmount * $this->value) / 100;
        } else {
            $discount = $this->value;
        }

        // Apply max discount cap if set
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }

        // Discount cannot exceed order amount
        if ($discount > $orderAmount) {
            $discount = $orderAmount;
        }

        return round($discount, 2);
    }

    /**
     * Record coupon usage
     */
    public function recordUsage(string $userIdentifier, float $discountAmount, ?int $saleId = null): CouponUsage
    {
        $usage = $this->usages()->create([
            'user_identifier' => $userIdentifier,
            'discount_amount' => $discountAmount,
            'sale_id' => $saleId,
        ]);

        $this->increment('used_count');

        return $usage;
    }

    /**
     * Get validation error message
     */
    public function getValidationError(string $userIdentifier, float $orderAmount): ?string
    {
        if (!$this->is_active) {
            return __('This coupon is no longer active.');
        }

        $today = now()->toDateString();

        if ($this->valid_from && $this->valid_from->toDateString() > $today) {
            return __('This coupon is not yet valid. It starts on :date.', ['date' => $this->valid_from->format('M d, Y')]);
        }

        if ($this->valid_until && $this->valid_until->toDateString() < $today) {
            return __('This coupon has expired.');
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return __('This coupon has reached its usage limit.');
        }

        $userUsageCount = $this->usages()
            ->where('user_identifier', $userIdentifier)
            ->count();

        if ($userUsageCount >= $this->usage_limit_per_user) {
            return __('You have already used this coupon the maximum number of times.');
        }

        if ($orderAmount < $this->min_order_amount) {
            return __('Minimum order amount of $:amount required for this coupon.', ['amount' => number_format($this->min_order_amount, 2)]);
        }

        return null;
    }

    /**
     * Get formatted discount display
     */
    public function getDiscountDisplayAttribute(): string
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return $this->value . '%';
        }
        return '$' . number_format($this->value, 2);
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === self::TYPE_PERCENTAGE ? __('Percentage') : __('Fixed Amount');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return __('Inactive');
        }

        $today = now()->toDateString();

        if ($this->valid_from && $this->valid_from->toDateString() > $today) {
            return __('Scheduled');
        }

        if ($this->valid_until && $this->valid_until->toDateString() < $today) {
            return __('Expired');
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return __('Exhausted');
        }

        return __('Active');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $status = $this->status_label;

        return match ($status) {
            __('Active') => 'success',
            __('Scheduled') => 'info',
            __('Expired'), __('Exhausted') => 'secondary',
            __('Inactive') => 'warning',
            default => 'secondary',
        };
    }
}
