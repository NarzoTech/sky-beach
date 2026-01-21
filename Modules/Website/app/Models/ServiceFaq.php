<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFaq extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_id',
        'question',
        'answer',
        'order',
        'status',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'status' => 'boolean',
        'order' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(WebsiteService::class, 'service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
