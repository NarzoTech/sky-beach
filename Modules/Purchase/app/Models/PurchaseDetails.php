<?php

namespace Modules\Purchase\app\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\app\Models\Product;
use Modules\Purchase\Database\factories\PurchaseDetailsFactory;

class PurchaseDetails extends Model
{
    use HasFactory;

    protected $table = 'purchase_details';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_id',
        'product_id',
        'unit_id',
        'quantity',
        'base_quantity',
        'purchase_price',
        'sub_total',
        'profit',
        'sale_price',
        'discount',
        'tax',
        'created_by',
        'updated_by',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id')->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withDefault();
    }

    public function purchaseReturn()
    {
        return $this->hasMany(PurchaseReturn::class, 'purchase_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\Product\app\Models\UnitType::class, 'unit_id')->withDefault();
    }
}
