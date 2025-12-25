<?php

namespace Modules\Attendance\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Attendance\Database\factories\HolidaySetupFactory;

class HolidaySetup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $table = 'holiday_setup';
    protected $fillable = [

        'name',
        'start_date',
        'end_date',
        'description',
        'status',
    ];
}
