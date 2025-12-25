<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Warehouse;

class BranchMenuPrice extends Model
{

    protected $fillable = [
        'warehouse_id',
        'menu_item_id',
        'variant_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MenuVariant::class, 'variant_id');
    }

    public function scopeForBranch($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeForItem($query, $menuItemId)
    {
        return $query->where('menu_item_id', $menuItemId);
    }
}
