<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Ingredient\app\Models\Ingredient;

class MenuAddon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'boolean',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return image_url($this->image, 'assets/images/placeholder.png');
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_addons', 'addon_id', 'menu_item_id')
            ->withPivot('max_quantity', 'is_required')
            ->withTimestamps();
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(AddonRecipe::class, 'menu_addon_id');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'addon_recipes', 'menu_addon_id', 'ingredient_id')
            ->withPivot('quantity_required', 'unit_id', 'notes')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
