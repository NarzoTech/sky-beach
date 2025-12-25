<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItemAddon extends Model
{

    protected $fillable = [
        'menu_item_id',
        'addon_id',
        'max_quantity',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(MenuAddon::class, 'addon_id');
    }
}
