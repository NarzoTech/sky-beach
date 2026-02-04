<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Modules\Ingredient\app\Models\Ingredient;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'long_description',
        'category_id',
        'cuisine_type',
        'image',
        'gallery',
        'base_price',
        'discount_price',
        'cost_price',
        'preparation_time',
        'calories',
        'is_vegetarian',
        'is_vegan',
        'is_spicy',
        'spice_level',
        'allergens',
        'is_featured',
        'is_new',
        'is_popular',
        'is_available',
        'available_in_pos',
        'available_in_website',
        'status',
        'sku',
        'barcode',
        'display_order',
    ];

    protected $casts = [
        'gallery' => 'array',
        'allergens' => 'array',
        'base_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_spicy' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_popular' => 'boolean',
        'is_available' => 'boolean',
        'available_in_pos' => 'boolean',
        'available_in_website' => 'boolean',
        'status' => 'boolean',
    ];

    protected $appends = ['image_url', 'final_price', 'profit_margin', 'price', 'discount_percentage'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
            if (empty($model->sku)) {
                $model->sku = 'MENU-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function getImageUrlAttribute()
    {
        return image_url($this->image, 'assets/images/placeholder.png');
    }

    public function getGalleryUrlsAttribute()
    {
        if ($this->gallery && is_array($this->gallery)) {
            return array_map(fn($img) => image_url($img), $this->gallery);
        }
        return [];
    }

    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->base_price;
    }

    public function getPriceAttribute()
    {
        return $this->base_price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_price && $this->base_price > 0) {
            return round((($this->base_price - $this->discount_price) / $this->base_price) * 100);
        }
        return 0;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->cost_price > 0) {
            return round((($this->base_price - $this->cost_price) / $this->base_price) * 100, 2);
        }
        return 100;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id')->withDefault();
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MenuVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(MenuVariant::class)->where('status', 1);
    }

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(MenuAddon::class, 'menu_item_addons', 'menu_item_id', 'addon_id')
            ->withPivot('max_quantity', 'is_required')
            ->withTimestamps();
    }

    public function activeAddons(): BelongsToMany
    {
        return $this->belongsToMany(MenuAddon::class, 'menu_item_addons', 'menu_item_id', 'addon_id')
            ->where('menu_addons.status', 1)
            ->withPivot('max_quantity', 'is_required')
            ->withTimestamps();
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipes', 'menu_item_id', 'ingredient_id')
            ->withPivot('quantity_required', 'unit_id', 'notes')
            ->withTimestamps();
    }

    public function branchPrices(): HasMany
    {
        return $this->hasMany(BranchMenuPrice::class);
    }

    public function branchAvailability(): HasMany
    {
        return $this->hasMany(BranchMenuAvailability::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MenuItemTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

    public function getTranslatedNameAttribute()
    {
        $translation = $this->translation();
        return $translation ? $translation->name : $this->name;
    }

    public function getTranslatedShortDescriptionAttribute()
    {
        $translation = $this->translation();
        return $translation ? $translation->short_description : $this->short_description;
    }

    public function getTranslatedLongDescriptionAttribute()
    {
        $translation = $this->translation();
        return $translation ? $translation->long_description : $this->long_description;
    }

    public function comboItems(): HasMany
    {
        return $this->hasMany(ComboItem::class);
    }

    public function getPriceForBranch($warehouseId, $variantId = null)
    {
        $branchPrice = $this->branchPrices()
            ->where('warehouse_id', $warehouseId)
            ->where('variant_id', $variantId)
            ->first();

        if ($branchPrice) {
            return $branchPrice->price;
        }

        if ($variantId) {
            $variant = $this->variants()->find($variantId);
            if ($variant) {
                return $this->base_price + $variant->price_adjustment;
            }
        }

        return $this->base_price;
    }

    public function isAvailableAtBranch($warehouseId)
    {
        $availability = $this->branchAvailability()
            ->where('warehouse_id', $warehouseId)
            ->first();

        if ($availability) {
            return $availability->is_available;
        }

        return $this->is_available;
    }

    public function calculateCostFromRecipe()
    {
        $totalCost = 0;
        foreach ($this->recipes as $recipe) {
            if ($recipe->ingredient) {
                // Use consumption_unit_cost for accurate costing
                $costPerUnit = $recipe->ingredient->consumption_unit_cost ?? $recipe->ingredient->cost ?? 0;
                $totalCost += $costPerUnit * $recipe->quantity_required;
            }
        }
        return $totalCost;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', 1);
    }

    public function scopeVegan($query)
    {
        return $query->where('is_vegan', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeForPos($query)
    {
        return $query->where('available_in_pos', true);
    }

    public function scopeForWebsite($query)
    {
        return $query->where('available_in_website', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeByCuisine($query, $cuisineType)
    {
        return $query->where('cuisine_type', $cuisineType);
    }
}
