<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\app\Models\Product;

class QuotationDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'product_id',
        'quantity',
        'price',
        'sub_total',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class)->withDefault();
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }
}
