<?php

namespace Modules\Attendance\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Attendance\Database\factories\AttendanceFactory;
use Modules\Employee\app\Models\Employee;

class Attendance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'employee_id',
        'in_time',
        'out_time',
        'date',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
