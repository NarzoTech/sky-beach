<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Ingredient\app\Models\Ingredient;

class QuotationDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'ingredient_id',
        'quantity',
        'price',
        'sub_total',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class)->withDefault();
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class)->withDefault();
    }
}
