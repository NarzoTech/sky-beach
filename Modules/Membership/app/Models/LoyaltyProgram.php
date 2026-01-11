<?php

namespace Modules\Membership\app\Models;

use App\Models\Admin;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyProgram extends Model
{
    use HasFactory;

    protected $table = 'loyalty_programs';

    protected $fillable = [
        'warehouse_id',
        'name',
        'description',
        'is_active',
        'earning_type',
        'earning_rate',
        'min_transaction_amount',
        'redemption_type',
        'points_per_unit',
        'earning_rules',
        'redemption_rules',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'earning_rules' => 'array',
        'redemption_rules' => 'array',
        'earning_rate' => 'decimal:2',
        'min_transaction_amount' => 'decimal:2',
        'points_per_unit' => 'decimal:2',
    ];

    /**
     * Relations
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(LoyaltyRule::class, 'loyalty_program_id');
    }

    public function segments(): HasMany
    {
        return $this->hasMany(LoyaltyCustomerSegment::class, 'loyalty_program_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }
}
