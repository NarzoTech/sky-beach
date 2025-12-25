<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('assets/images/placeholder.png');
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_addons', 'addon_id', 'menu_item_id')
            ->withPivot('max_quantity', 'is_required')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
