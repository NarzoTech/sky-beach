<?php

namespace Modules\Order\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Database\factories\OrderDetailsFactory;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\Variant;

class OrderDetails extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'order_id',
        'ingredient_id',
        'ingredient_name',
        'ingredient_sku',
        'variant_id',
        'price',
        'quantity',
        'total',
        'attributes',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class)->withDefault();
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class)->withDefault();
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class)->withDefault();
    }
}
