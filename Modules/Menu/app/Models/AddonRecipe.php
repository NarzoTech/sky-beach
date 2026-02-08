<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\UnitType;

class AddonRecipe extends Model
{
    protected $fillable = [
        'menu_addon_id',
        'ingredient_id',
        'quantity_required',
        'unit_id',
        'notes',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:4',
    ];

    public function addon(): BelongsTo
    {
        return $this->belongsTo(MenuAddon::class, 'menu_addon_id');
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(UnitType::class, 'unit_id');
    }
}
