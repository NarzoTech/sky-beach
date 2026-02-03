<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Combo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'combo_price',
        'original_price',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'is_active',
        'status',
    ];

    protected $casts = [
        'combo_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'status' => 'boolean',
    ];

    protected $appends = ['image_url', 'savings', 'savings_percentage', 'is_currently_available'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
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

    public function getSavingsAttribute()
    {
        return max(0, $this->original_price - $this->combo_price);
    }

    public function getSavingsPercentageAttribute()
    {
        if ($this->original_price > 0) {
            return round((($this->original_price - $this->combo_price) / $this->original_price) * 100, 1);
        }
        return 0;
    }

    public function getIsCurrentlyAvailableAttribute()
    {
        if (!$this->is_active || !$this->status) {
            return false;
        }

        $today = Carbon::today();

        if ($this->start_date && $today->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $today->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    public function comboItems(): HasMany
    {
        return $this->hasMany(ComboItem::class);
    }

    /**
     * Alias for comboItems relationship
     */
    public function items(): HasMany
    {
        return $this->comboItems();
    }

    public function calculateOriginalPrice()
    {
        $total = 0;
        foreach ($this->comboItems as $item) {
            if ($item->menuItem) {
                $itemPrice = $item->menuItem->base_price;
                if ($item->variant) {
                    $itemPrice += $item->variant->price_adjustment;
                }
                $total += $itemPrice * $item->quantity;
            }
        }
        return $total;
    }

    public function updateOriginalPrice()
    {
        $this->original_price = $this->calculateOriginalPrice();
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1)->where('is_active', 1);
    }

    public function scopeCurrentlyAvailable($query)
    {
        $today = Carbon::today();
        return $query->active()
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
