<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Customer\Database\factories\AreaFactory;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];
}
