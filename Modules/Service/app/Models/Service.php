<?php

namespace Modules\Service\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Service\Database\factories\ServiceFactory;

class Service extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'image',
        'description',
        'status',
    ];

    protected $appends = ['singleImage'];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function getSingleImageAttribute()
    {
        if (!$this->image) {
            return asset('backend/img/service.png');
        }
        return asset($this->image);
    }
}
