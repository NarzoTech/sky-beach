<?php

namespace Modules\Expense\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\app\Models\Area;

class ExpenseSupplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'expense_suppliers';

    protected $fillable = [
        'name',
        'company',
        'phone',
        'email',
        'area_id',
        'address',
        'status',
        'balance',
        'advance',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id')->withDefault();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'expense_supplier_id');
    }

    public function payments()
    {
        return $this->hasMany(ExpenseSupplierPayment::class, 'expense_supplier_id');
    }

    public function getAdvanceAttribute()
    {
        $payments = $this->payments;

        $totals = $payments->reduce(function ($carry, $payment) {
            if ($payment->payment_type === 'advance_pay') {
                $carry['total_advance'] += $payment->amount;
            } elseif ($payment->payment_type === 'advance_refund') {
                $carry['total_refund'] += $payment->amount;
            }
            return $carry;
        }, ['total_advance' => 0, 'total_refund' => 0]);

        return $totals['total_advance'] - $totals['total_refund'];
    }

    public function getTotalExpenseAttribute()
    {
        return $this->expenses->sum('amount');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments
            ->whereIn('payment_type', ['expense', 'due_pay'])
            ->sum('amount');
    }

    public function getTotalDueAttribute()
    {
        $totalExpense = $this->total_expense;
        $totalPaid = $this->total_paid;

        return $totalExpense - $totalPaid;
    }

    public function dueExpenses()
    {
        return $this->hasMany(Expense::class, 'expense_supplier_id')->where('due_amount', '>', 0);
    }
}
