<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComboItem extends Model
{

    protected $fillable = [
        'combo_id',
        'menu_item_id',
        'variant_id',
        'quantity',
    ];

    protected $appends = ['item_price', 'total_price'];

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MenuVariant::class, 'variant_id');
    }

    public function getItemPriceAttribute()
    {
        if (!$this->menuItem) {
            return 0;
        }

        $price = $this->menuItem->base_price;

        if ($this->variant) {
            $price += $this->variant->price_adjustment;
        }

        return $price;
    }

    public function getTotalPriceAttribute()
    {
        return $this->item_price * $this->quantity;
    }
}
