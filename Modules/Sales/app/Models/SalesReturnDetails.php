<?php

namespace Modules\Sales\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Sales\Database\factories\SalesReturnDetailsFactory;

class SalesReturnDetails extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_return_id',
        'ingredient_id',
        'menu_item_id',
        'service_id',
        'unit_id',
        'source',
        'quantity',
        'base_quantity',
        'price',
        'sub_total',
    ];


    public function saleReturn()
    {
        return $this->belongsTo(SalesReturn::class, 'sale_return_id')->withDefault();
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id')->withDefault();
    }

    // Alias for backward compatibility
    public function product()
    {
        return $this->ingredient();
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\Ingredient\app\Models\UnitType::class, 'unit_id')->withDefault();
    }

    public function menuItem()
    {
        return $this->belongsTo(\Modules\Menu\app\Models\MenuItem::class, 'menu_item_id')->withDefault();
    }
}
