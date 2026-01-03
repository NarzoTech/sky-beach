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
        'product_id',
        'service_id',
        'source',
        'quantity',
        'price',
        'sub_total',
    ];


    public function saleReturn()
    {
        return $this->belongsTo(SalesReturn::class, 'sale_return_id')->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Ingredient::class, 'product_id')->withDefault();
    }
}
