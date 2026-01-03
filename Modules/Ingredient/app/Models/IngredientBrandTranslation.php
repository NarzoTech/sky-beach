<?php

namespace Modules\Ingredient\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientBrandTranslation extends Model
{
    use HasFactory;

    protected $table = 'ingredient_brand_translations';

    protected $fillable = [
        'name', 'description', 'ingredient_brand_id', 'lang_code',
    ];

    public function ingredientBrand()
    {
        return $this->belongsTo(IngredientBrand::class)->withDefault();
    }
}
