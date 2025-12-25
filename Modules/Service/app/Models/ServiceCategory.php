<?php

namespace Modules\Service\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Service\Database\factories\ServiceCategoryFactory;

class ServiceCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'status'];
}
