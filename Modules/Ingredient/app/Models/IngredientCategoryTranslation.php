<?php

namespace Modules\Ingredient\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientCategoryTranslation extends Model
{
    use HasFactory;

    protected $table = 'ingredient_category_translations';

    protected $fillable = [
        'name', 'description', 'ingredient_category_id', 'lang_code',
    ];

    public function ingredientCategory()
    {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id', 'id')->withDefault();
    }

}
