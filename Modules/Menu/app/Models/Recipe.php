<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\UnitType;

class Recipe extends Model
{

    protected $fillable = [
        'menu_item_id',
        'ingredient_id',
        'quantity_required',
        'unit_id',
        'notes',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:4',
    ];

    protected $appends = ['ingredient_cost'];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }

    // Alias for backward compatibility
    public function product(): BelongsTo
    {
        return $this->ingredient();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_id');
    }

    public function getIngredientCostAttribute()
    {
        if ($this->ingredient) {
            // Use consumption_unit_cost for accurate costing
            $costPerUnit = $this->ingredient->consumption_unit_cost ?? $this->ingredient->cost ?? 0;
            return $costPerUnit * $this->quantity_required;
        }
        return 0;
    }

    public function hasEnoughStock($quantity = 1)
    {
        if ($this->ingredient) {
            $requiredStock = $this->quantity_required * $quantity;
            return $this->ingredient->stock >= $requiredStock;
        }
        return true;
    }

    public function deductStock($quantity = 1)
    {
        if ($this->ingredient) {
            $requiredStock = $this->quantity_required * $quantity;
            $this->ingredient->stock -= $requiredStock;

            if ($this->ingredient->stock <= 0) {
                $this->ingredient->stock_status = 'out_of_stock';
            } elseif ($this->ingredient->stock <= $this->ingredient->stock_alert) {
                // Stock is low - could trigger notification here
            }

            $this->ingredient->save();
            return true;
        }
        return false;
    }
}
