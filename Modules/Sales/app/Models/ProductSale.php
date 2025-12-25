<?php

namespace Modules\Sales\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\app\Models\Product;
use Modules\Sales\Database\factories\ProductSaleFactory;
use Modules\Service\app\Models\Service;

class ProductSale extends Model
{
    use HasFactory;

    protected $table = 'product_sales';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'service_id',
        'quantity',
        'sale_unit_id',
        'unit_id',
        'base_quantity',
        'product_sku',
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withDefault();
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id')->withDefault();
    }

    public function getSaleReturnAttribute()
    {
        // return $this->sale->saleReturns;
        // return $this->sale->saleReturns()->where('product_id', $this->product_id)->first();
        return 0;
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\Product\app\Models\UnitType::class, 'unit_id')->withDefault();
    }
}
