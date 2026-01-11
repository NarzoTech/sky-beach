<?php

namespace Modules\Purchase\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Purchase\Database\factories\PurchaseReturnDetailsFactory;

class PurchaseReturnDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_return_details';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_return_id',
        'ingredient_id',
        'purchase_id',
        'unit_id',
        'quantity',
        'base_quantity',
        'total',
    ];

    // relationship
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class, 'purchase_return_id')->withDefault();
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

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id')->withDefault();
    }
}
