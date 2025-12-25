<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
