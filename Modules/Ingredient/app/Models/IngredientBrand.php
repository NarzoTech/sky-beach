<?php

namespace Modules\Ingredient\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Media\app\Models\Media;

class IngredientBrand extends Model
{
    use HasFactory;

    protected $table = 'ingredient_brands';

    protected $fillable = [
        'name',
        'image',
        'description',
        'status',
    ];

    protected $appends = [
        'image_url',
    ];
    public function ingredients()
    {
        return $this->hasMany(Ingredient::class, 'brand_id', 'id');
    }

    public function getImageUrlAttribute()
    {
        $img = Media::find($this->image)?->path;

        return asset($img);
    }
}
