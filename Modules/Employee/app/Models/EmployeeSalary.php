<?php

namespace Modules\Employee\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounts\app\Models\Account;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'month',
        'year',
        'salary',
        'payment_type',
        'account_id',
        'amount',
        'note',
        'type',
        'payable_salary'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class)->withDefault();
    }

    public function account()
    {
        return $this->belongsTo(Account::class)->withDefault();
    }
}
