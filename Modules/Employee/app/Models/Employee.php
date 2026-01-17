<?php

namespace Modules\Employee\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Attendance\app\Models\Attendance;
use Modules\Employee\Database\factories\EmployeeFactory;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'nid',
        'designation',
        'address',
        'join_date',
        'salary',
        'yearly_leaves',
        'image',
        'status',
        'admin_id',
        'is_waiter',
        'pin_code',
    ];

    protected $casts = [
        'is_waiter' => 'boolean',
    ];

    /**
     * Get the admin account linked to this employee
     */
    public function admin()
    {
        return $this->belongsTo(\App\Models\Admin::class);
    }

    /**
     * Scope to get only waiters
     */
    public function scopeWaiters($query)
    {
        return $query->where('is_waiter', true);
    }

    /**
     * Scope to get active waiters
     */
    public function scopeActiveWaiters($query)
    {
        return $query->where('is_waiter', true)->where('status', 'active');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset($this->image) : null;
    }
    public function employeeSalary()
    {
        $month = request('month');

        $month = ($month != null && $month != '' && $month != '0') ? now()->parse($month)->format('F') : now()->format('F');

        // dd($month);
        return $this->hasMany(EmployeeSalary::class, 'employee_id', 'id')->where('month', $month)->where('year', request('year'));
    }

    public function currentSalary()
    {
        return $this->hasMany(EmployeeSalary::class, 'employee_id', 'id');
    }
    public function getAdvanceAmountAttribute($month = null, $year = null)
    {
        $month = $month ?? now()->format('F');
        $year = $year ?? now()->format('Y');

        return $this->currentSalary
            ->where('type', 'advance')
            ->where('month', $month)
            ->where('year', $year)
            ->sum('amount');
    }

    // due amount

    public function getDueAmountAttribute($month = null, $year = null)
    {
        $month = $month ?? now()->format('F');
        $year = $year ?? now()->format('Y');
        return $this->salary - $this->getPaidAmountAttribute($month, $year);
    }

    public function getPaidAmountAttribute($month = null, $year = null)
    {
        $month = $month ?? now()->format('F');
        $year = $year ?? now()->format('Y');


        return $this->currentSalary->where('month', $month)->where('year', $year)->sum('amount');
    }

    public function attendance()
    {
        $month_year = request()->month_year ?? now()->format('m/Y');

        $date = \Carbon\Carbon::createFromFormat('m/Y', $month_year);

        $month = $date->month;
        $year = $date->year;


        return $this->hasMany(Attendance::class, 'employee_id', 'id')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where(function ($query) {
                $query->where('status', 'present')->orWhere('status', 'weekend');
            });
    }
}
