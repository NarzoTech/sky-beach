<?php

namespace Modules\POS\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\POS\Database\factories\CartHoldFactory;

class CartHold extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'contents',
        'status',
        'note',
    ];

    protected $casts = [
        'contents' => 'array',
    ];

    protected function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
