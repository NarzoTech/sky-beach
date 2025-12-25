<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Warehouse;

class BranchMenuAvailability extends Model
{

    protected $table = 'branch_menu_availability';

    protected $fillable = [
        'warehouse_id',
        'menu_item_id',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
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

    public function scopeForBranch($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', 1);
    }

    public function scopeUnavailable($query)
    {
        return $query->where('is_available', 0);
    }
}
