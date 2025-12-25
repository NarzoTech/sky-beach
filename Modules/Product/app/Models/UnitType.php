<?php

namespace Modules\Product\app\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitType extends Model
{
    use HasFactory;
    protected $table = "unit_types";

    protected $fillable = [
        "name",
        "description",
        "status",
        'ShortName',
        'base_unit',
        'operator',
        'operator_value',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(UnitType::class, 'base_unit', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(UnitType::class, 'base_unit', 'id');
    }

    /**
     * Get the base unit (self if no parent, or parent)
     */
    public function getBaseUnit()
    {
        return $this->base_unit ? $this->parent : $this;
    }

    /**
     * Check if this unit is a base unit
     */
    public function isBaseUnit()
    {
        return is_null($this->base_unit);
    }
}
