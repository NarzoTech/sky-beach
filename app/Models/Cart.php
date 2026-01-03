<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\Variant;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ingredient_id',
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

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class)->withDefault();
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class)->withDefault();
    }
}
