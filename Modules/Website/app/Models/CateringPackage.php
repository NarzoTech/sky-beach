<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CateringPackage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'long_description',
        'min_guests',
        'max_guests',
        'price_per_person',
        'includes',
        'menu_items',
        'image',
        'is_featured',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'includes' => 'array',
        'menu_items' => 'array',
        'min_guests' => 'integer',
        'max_guests' => 'integer',
        'price_per_person' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });

        static::updating(function ($package) {
            if ($package->isDirty('name') && !$package->isDirty('slug')) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    /**
     * Scope: Active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Featured packages
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: Ordered by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * Get inquiries for this package
     */
    public function inquiries()
    {
        return $this->hasMany(CateringInquiry::class, 'package_id');
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset($this->image);
        }
        return asset('website/images/catering_placeholder.jpg');
    }

    /**
     * Calculate price range
     */
    public function getPriceRangeAttribute()
    {
        $min = $this->price_per_person * $this->min_guests;
        $max = $this->price_per_person * $this->max_guests;
        return [
            'min' => $min,
            'max' => $max,
            'formatted' => '$' . number_format($min, 0) . ' - $' . number_format($max, 0),
        ];
    }

    /**
     * Calculate total price for given guest count
     */
    public function calculatePrice($guestCount)
    {
        $guestCount = max($this->min_guests, min($this->max_guests, $guestCount));
        return $this->price_per_person * $guestCount;
    }

    /**
     * Get route key name for URL
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
