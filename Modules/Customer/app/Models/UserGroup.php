<?php

namespace Modules\Customer\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\Database\factories\CustomerGroupFactory;

class UserGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_group';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'discount',
        'type',
        'status',
        'created_by',
        'updated_by',
    ];
}
