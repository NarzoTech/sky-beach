<?php

namespace Modules\Sales\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Sales\Database\factories\IngredientSaleFactory;
use Modules\Service\app\Models\Service;

class IngredientSale extends Model
{
    use HasFactory;

    protected $table = 'ingredient_sales';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'ingredient_id',
        'service_id',
        'quantity',
        'sale_unit_id',
        'unit_id',
        'base_quantity',
        'ingredient_sku',
        'variant_id',
        'attributes',
        'price',
        'tax',
        'discount',
        'sub_total',
        'source',
        'selling_price',
        'purchase_price'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id')->withDefault();
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id')->withDefault();
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id')->withDefault();
    }

    public function getSaleReturnAttribute()
    {
        return 0;
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\Ingredient\app\Models\UnitType::class, 'unit_id')->withDefault();
    }
}
