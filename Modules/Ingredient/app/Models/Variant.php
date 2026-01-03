<?php

namespace Modules\Ingredient\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'price',
        'cost',
        'sku',
    ];

    protected $appends = ['attribute_ids', 'attribute_and_value_ids', 'attributes'];
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class)->withDefault();
    }

    public function options()
    {
        return $this->hasMany(VariantOption::class);
    }

    public function variantOptions()
    {
        return $this->hasMany(VariantOption::class);
    }

    public function optionValues()
    {
        return $this->hasManyThrough(AttributeValue::class, VariantOption::class, 'variant_id', 'id', 'id', 'attribute_value_id');
    }

    public function getAttributeIdsAttribute()
    {
        return $this->options->pluck('attributeValue.attribute_id')->toArray();
    }

    public function attributes()
    {
        // get attributes and values of this variant
        return $this->options->map(function ($option) {
            return $option->attributeValue->attribute->name . ': ' . $option->attributeValue->name;
        })->implode(', ');
    }


    // get attribute and attribute value ids
    public function getAttributeAndValueIdsAttribute()
    {
        return $this->options->map(function ($option) {
            return [
                'attribute_id' => $option->attributeValue->attribute_id,
                'attribute' => $option->attributeValue->attribute->name,
                'attribute_value_id' => $option->attribute_value_id,
                'attribute_value' => $option->attributeValue->name,
            ];
        });
    }

    public function getAttributesAttribute()
    {
        return $this->options->map(function ($option) {
            return [
                'attribute_id' => $option->attributeValue->attribute_id,
                'attribute' => $option->attributeValue->attribute->name,
                'attribute_value_id' => $option->attribute_value_id,
                'attribute_value' => $option->attributeValue->name,
            ];
        });
    }
}
