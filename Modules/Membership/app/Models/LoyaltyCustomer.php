<?php

namespace Modules\Membership\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyCustomer extends Model
{
    use HasFactory;

    protected $table = 'loyalty_customers';

    protected $fillable = [
        'phone',
        'user_id',
        'name',
        'email',
        'total_points',
        'lifetime_points',
        'redeemed_points',
        'status',
        'joined_at',
        'last_purchase_at',
        'last_redemption_at',
        'opt_in_sms',
        'opt_in_email',
    ];

    protected $casts = [
        'total_points' => 'decimal:2',
        'lifetime_points' => 'decimal:2',
        'redeemed_points' => 'decimal:2',
        'opt_in_sms' => 'boolean',
        'opt_in_email' => 'boolean',
        'joined_at' => 'datetime',
        'last_purchase_at' => 'datetime',
        'last_redemption_at' => 'datetime',
    ];

    /**
     * Relations
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyTransaction::class, 'loyalty_customer_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(LoyaltyRedemption::class, 'loyalty_customer_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', $phone);
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Accessors
     */
    public function getAvailablePointsAttribute()
    {
        return $this->total_points;
    }

    /**
     * Methods
     */
    public function canRedeem($points)
    {
        return $this->status === 'active' && $this->total_points >= $points;
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isBlocked()
    {
        return $this->status === 'blocked';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }
}
