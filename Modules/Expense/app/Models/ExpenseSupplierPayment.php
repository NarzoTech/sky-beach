<?php

namespace Modules\Expense\app\Models;

use App\Models\Admin;
use App\Models\Ledger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Accounts\app\Models\Account;

class ExpenseSupplierPayment extends Model
{
    use HasFactory;

    protected $table = 'expense_supplier_payments';

    protected $fillable = [
        'expense_id',
        'invoice',
        'expense_supplier_id',
        'account_id',
        'is_received',
        'is_paid',
        'payment_type',
        'account_type',
        'amount',
        'payment_date',
        'note',
        'memo',
        'ledger_id',
        'created_by',
        'updated_by',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id', 'id')->withDefault();
    }

    public function expenseSupplier()
    {
        return $this->belongsTo(ExpenseSupplier::class, 'expense_supplier_id', 'id')->withDefault();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id')->withDefault();
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id', 'id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by', 'id')->withDefault();
    }
}
