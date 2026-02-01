<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CmsPage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'banner_image',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the banner image URL
     */
    public function getBannerImageUrlAttribute()
    {
        if ($this->banner_image) {
            if (str_starts_with($this->banner_image, 'storage/') || str_starts_with($this->banner_image, 'website/')) {
                return asset($this->banner_image);
            }
            return asset('storage/' . $this->banner_image);
        }
        return null;
    }
}
