<?php

namespace Modules\Attendance\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Attendance\Database\factories\WeekendSetupFactory;

class WeekendSetup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'weekend_setup';
    protected $fillable = [
        'name',
        'is_weekend',
        'status',
    ];
}
