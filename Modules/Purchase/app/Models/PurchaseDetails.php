<?php

namespace Modules\Purchase\app\Models;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Ingredient\app\Models\Ingredient;
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
        'ingredient_id',
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

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id')->withDefault();
    }

    public function purchaseReturn()
    {
        return $this->hasMany(PurchaseReturn::class, 'purchase_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\Ingredient\app\Models\UnitType::class, 'unit_id')->withDefault();
    }
}
