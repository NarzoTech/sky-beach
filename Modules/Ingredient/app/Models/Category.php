<?php

namespace Modules\Ingredient\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Media\app\Models\Media;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'parent_id',
        'status',
        'name'
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'category_id');
    }

    // calculate query time and total query

    public function calcQuery()
    {
        $this->ingredients->get();
        $this->ingredients->count();
    }


    protected function ensureIngredientsLoaded()
    {
        if (!$this->relationLoaded('ingredients')) {
            $this->load('ingredients');
        }
    }

    public function getPurchaseSummaryAttribute()
    {
        $this->ensureIngredientsLoaded();
        // Initialize count and amount
        $count = $this->ingredients->sum(function ($ingredient) {
            return $ingredient->total_purchase['qty'] ?? 0; // Handle null values
        });

        $amount = $this->ingredients->sum(function ($ingredient) {
            return $ingredient->total_purchase['price'] ?? 0; // Handle null values
        });

        // Return as an array
        return [
            'count' => $count,
            'amount' => $amount,
        ];
    }

    public function getSalesCountAttribute()
    {
        $this->ensureIngredientsLoaded();
        $count = 0;
        foreach ($this->ingredients as $ingredient) {
            $count += $ingredient->sales['qty'];
        }

        return $count;
    }


    public function getSalesAmountAttribute()
    {
        $this->ensureIngredientsLoaded();
        $amount = 0;
        foreach ($this->ingredients as $ingredient) {
            $amount += $ingredient->sales['price'];
        }

        return $amount;
    }


    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')->withDefault();
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
