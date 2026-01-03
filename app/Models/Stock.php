<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Purchase\app\Models\Purchase;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ingredient_id',
        'purchase_id',
        'warehouse_id',
        'unit_id',
        'in_quantity',
        'base_in_quantity',
        'out_quantity',
        'base_out_quantity',
        'invoice',
        'type',
        'available_qty',
        'purchase_price',
        'sale_price',
        'profit',
        'discount',
        'tax',
        'created_by',
        'updated_by',
        'sku',
        'rate',
        'date',
        'purchase_return_id',
        'sale_id',
        'sale_return_id',
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id')->withDefault();
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id')->withDefault();
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by')->withDefault();
    }

    public function unit()
    {
        return $this->belongsTo(\Modules\Ingredient\app\Models\UnitType::class, 'unit_id')->withDefault();
    }
}
