<?php

namespace Modules\Sales\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Menu\app\Models\Combo;
use Modules\Menu\app\Models\MenuItem;
use Modules\Sales\Database\factories\ProductSaleFactory;
use Modules\Service\app\Models\Service;

class ProductSale extends Model
{
    use HasFactory;

    protected $table = 'ingredient_sales';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'ingredient_id',
        'menu_item_id',
        'service_id',
        'combo_id',
        'combo_name',
        'quantity',
        'sale_unit_id',
        'unit_id',
        'base_quantity',
        'product_sku',
        'variant_id',
        'attributes',
        'addons',
        'addons_price',
        'price',
        'tax',
        'discount',
        'sub_total',
        'cogs_amount',
        'profit_amount',
        'source',
        'selling_price',
        'purchase_price',
        'note'
    ];

    protected $casts = [
        'cogs_amount' => 'decimal:4',
        'profit_amount' => 'decimal:4',
        'addons' => 'array',
        'addons_price' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id')->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id')->withDefault();
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id')->withDefault();
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id')->withDefault();
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id')->withDefault();
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class, 'combo_id')->withDefault();
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
