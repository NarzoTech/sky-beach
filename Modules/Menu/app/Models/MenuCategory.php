<?php

namespace Modules\Menu\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MenuCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'display_order',
        'status',
        'is_featured',
        'is_popular',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
    ];

    protected $appends = ['image_url'];

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

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id');
    }

    public function activeMenuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id')
            ->where('status', 1)
            ->where('is_available', 1);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'parent_id')->withDefault();
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuCategory::class, 'parent_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MenuCategoryTranslation::class);
    }

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

    public function getTranslatedNameAttribute()
    {
        $translation = $this->translation();
        return $translation ? $translation->name : $this->name;
    }

    public function getTranslatedDescriptionAttribute()
    {
        $translation = $this->translation();
        return $translation ? $translation->description : $this->description;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopePopular($query)
    {
        return $query->where('is_popular', 1);
    }

    public function scopeParentCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
