<?php

namespace Modules\POS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Sales\app\Models\ProductSale;

class SplitBillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'split_bill_id',
        'product_sale_id',
        'quantity',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the split bill
     */
    public function splitBill()
    {
        return $this->belongsTo(SplitBill::class);
    }

    /**
     * Get the product sale (order item)
     */
    public function productSale()
    {
        return $this->belongsTo(ProductSale::class);
    }
}
