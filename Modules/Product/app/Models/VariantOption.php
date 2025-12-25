<?php

namespace Modules\Product\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'attribute_id',
        'attribute_value_id',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class)->withDefault();
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class)->withDefault();
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class)->withDefault();
    }
}
