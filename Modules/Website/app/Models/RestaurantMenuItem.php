<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'image',
        'category',
        'cuisine_type',
        'is_vegetarian',
        'is_spicy',
        'spice_level',
        'ingredients',
        'allergens',
        'preparation_time',
        'calories',
        'available_in_pos',
        'available_in_website',
        'is_featured',
        'is_new',
        'is_popular',
        'order',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_vegetarian' => 'boolean',
        'is_spicy' => 'boolean',
        'available_in_pos' => 'boolean',
        'available_in_website' => 'boolean',
        'is_featured' => 'boolean',
        'is_new' => 'boolean',
        'is_popular' => 'boolean',
        'status' => 'boolean',
        'preparation_time' => 'integer',
        'calories' => 'integer',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeForWebsite($query)
    {
        return $query->where('available_in_website', true);
    }

    public function scopeForPos($query)
    {
        return $query->where('available_in_pos', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_price && $this->price > 0) {
            return round((($this->price - $this->discount_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getFinalPriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }
}
