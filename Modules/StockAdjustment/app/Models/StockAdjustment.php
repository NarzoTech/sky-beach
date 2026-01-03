<?php

namespace Modules\StockAdjustment\app\Models;

use App\Models\Admin;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\UnitType;

class StockAdjustment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'adjustment_number',
        'ingredient_id',
        'warehouse_id',
        'adjustment_type',
        'quantity',
        'unit_id',
        'cost_per_unit',
        'total_cost',
        'adjustment_date',
        'reason',
        'notes',
        'created_by',
        'approved_by',
        'status',
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'quantity' => 'decimal:4',
        'cost_per_unit' => 'decimal:4',
        'total_cost' => 'decimal:4',
    ];

    const TYPES = [
        'wastage' => 'Wastage/Spoilage',
        'damage' => 'Damage',
        'theft' => 'Theft',
        'correction' => 'Correction',
        'transfer_out' => 'Transfer Out',
        'transfer_in' => 'Transfer In',
        'consumption' => 'Internal Consumption',
        'other' => 'Other',
    ];

    const DECREASE_TYPES = ['wastage', 'damage', 'theft', 'transfer_out', 'consumption'];
    const INCREASE_TYPES = ['correction', 'transfer_in'];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id')->withDefault();
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id')->withDefault();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_id')->withDefault();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by')->withDefault();
    }

    public function getAdjustmentTypeLabelAttribute(): string
    {
        return self::TYPES[$this->adjustment_type] ?? $this->adjustment_type;
    }

    public function isDecrease(): bool
    {
        return in_array($this->adjustment_type, self::DECREASE_TYPES);
    }

    public function isIncrease(): bool
    {
        return in_array($this->adjustment_type, self::INCREASE_TYPES) ||
               ($this->adjustment_type === 'correction' && $this->quantity > 0);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('adjustment_type', $type);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('adjustment_date', [$from, $to]);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public static function generateAdjustmentNumber(): string
    {
        $prefix = 'ADJ-';
        $lastAdjustment = self::latest()->first();

        if ($lastAdjustment) {
            $lastNumber = intval(str_replace($prefix, '', $lastAdjustment->adjustment_number));
            return $prefix . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
        }

        return $prefix . '000001';
    }
}
