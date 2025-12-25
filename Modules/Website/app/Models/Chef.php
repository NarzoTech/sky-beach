<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chef extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'designation',
        'specialization',
        'bio',
        'image',
        'email',
        'phone',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'experience_years',
        'order',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status' => 'boolean',
        'experience_years' => 'integer',
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
}
