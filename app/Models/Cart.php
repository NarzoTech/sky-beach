<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\app\Models\Product;
use Modules\Product\app\Models\Variant;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'qty',
        'price',
        'total',
        'status',
        'variant_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class)->withDefault();
    }
}
