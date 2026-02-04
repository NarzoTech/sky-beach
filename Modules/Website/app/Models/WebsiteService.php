<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'icon',
        'image',
        'price',
        'duration',
        'order',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'boolean',
        'price' => 'decimal:2',
        'duration' => 'integer',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function contacts()
    {
        return $this->hasMany(ServiceContact::class, 'service_id');
    }

    public function faqs()
    {
        return $this->hasMany(ServiceFaq::class, 'service_id');
    }

    /**
     * Get the image URL
     */
    public function getImageUrlAttribute()
    {
        return image_url($this->image, 'website/images/service_1.jpg');
    }

    /**
     * Get the icon URL
     */
    public function getIconUrlAttribute()
    {
        return image_url($this->icon);
    }
}
