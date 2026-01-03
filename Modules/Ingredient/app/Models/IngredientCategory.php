<?php

namespace Modules\Ingredient\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientCategory extends Model
{
    use HasFactory;

    protected $table = 'ingredient_categories';

    protected $fillable = ['ingredient_id','category_id'];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id')->withDefault();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->withDefault();
    }
}
