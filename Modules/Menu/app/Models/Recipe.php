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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_id');
    }

    public function getIngredientCostAttribute()
    {
        if ($this->product) {
            return $this->product->cost * $this->quantity_required;
        }
        return 0;
    }

    public function hasEnoughStock($quantity = 1)
    {
        if ($this->product) {
            $requiredStock = $this->quantity_required * $quantity;
            return $this->product->stock >= $requiredStock;
        }
        return true;
    }

    public function deductStock($quantity = 1)
    {
        if ($this->product) {
            $requiredStock = $this->quantity_required * $quantity;
            $this->product->stock -= $requiredStock;

            if ($this->product->stock <= 0) {
                $this->product->stock_status = 'out_of_stock';
            } elseif ($this->product->stock <= $this->product->stock_alert) {
                // Stock is low - could trigger notification here
            }

            $this->product->save();
            return true;
        }
        return false;
    }
}
