<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuVariant extends Model
{

    protected $fillable = [
        'menu_item_id',
        'name',
        'price_adjustment',
        'is_default',
        'status',
    ];

    protected $casts = [
        'price_adjustment' => 'decimal:2',
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    protected $appends = ['final_price'];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function getFinalPriceAttribute()
    {
        if ($this->menuItem) {
            return $this->menuItem->base_price + $this->price_adjustment;
        }
        return $this->price_adjustment;
    }

    public function branchPrices(): HasMany
    {
        return $this->hasMany(BranchMenuPrice::class, 'variant_id');
    }

    public function comboItems(): HasMany
    {
        return $this->hasMany(ComboItem::class, 'variant_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', 1);
    }
}
