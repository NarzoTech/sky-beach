<?php

namespace Modules\Membership\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyCustomerSegment extends Model
{
    use HasFactory;

    protected $table = 'loyalty_customer_segments';

    protected $fillable = [
        'loyalty_program_id',
        'name',
        'description',
        'min_lifetime_points',
        'max_lifetime_points',
        'min_transactions',
        'min_spent',
        'is_active',
    ];

    protected $casts = [
        'min_lifetime_points' => 'decimal:2',
        'max_lifetime_points' => 'decimal:2',
        'min_spent' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relations
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class, 'loyalty_program_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('loyalty_program_id', $programId);
    }

    /**
     * Methods
     */
    public function isActive()
    {
        return $this->is_active === true;
    }

    public function customerQualifies(LoyaltyCustomer $customer)
    {
        // Check lifetime points
        if ($this->min_lifetime_points && $customer->lifetime_points < $this->min_lifetime_points) {
            return false;
        }

        if ($this->max_lifetime_points && $customer->lifetime_points > $this->max_lifetime_points) {
            return false;
        }

        // Check transaction count
        $transactionCount = $customer->transactions()->earnings()->count();
        if ($transactionCount < $this->min_transactions) {
            return false;
        }

        return true;
    }
}
