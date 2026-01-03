<?php

namespace Modules\Ingredient\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientAttribute extends Model
{
    use HasFactory;

    protected $table = 'ingredient_attributes';

    protected $fillable = ['ingredient_id', 'attribute_id', 'attribute_value_id'];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id')->withDefault();
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id')->withDefault();
    }

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id', 'id')->withDefault();
    }
}
