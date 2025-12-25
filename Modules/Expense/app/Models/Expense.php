<?php
namespace Modules\Expense\app\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounts\app\Models\Account;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'invoice',
        'date',
        'payment_type',
        'account_id',
        'account_details',
        'payment_status',
        'note',
        'memo',
        'document',
        'amount',
        'paid_amount',
        'due_amount',
        'expense_type_id',
        'sub_expense_type_id',
        'expense_supplier_id',
        'created_by',
        'updated_by',
    ];

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id')->withDefault();
    }

    public function subExpenseType()
    {
        return $this->belongsTo(ExpenseType::class, 'sub_expense_type_id')->withDefault();
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }

    public function expenseSupplier()
    {
        return $this->belongsTo(ExpenseSupplier::class, 'expense_supplier_id')->withDefault();
    }

    public function payments()
    {
        return $this->hasMany(ExpenseSupplierPayment::class, 'expense_id');
    }

    public function scopePaid($query)
    {
        return $query->where('due_amount', 0)->where('paid_amount', '>', 0);
    }

    public function scopeDue($query)
    {
        return $query->where('due_amount', '>', 0)->where('paid_amount', 0);
    }

    public function scopePartial($query)
    {
        return $query->where('due_amount', '>', 0)->where('paid_amount', '>', 0);
    }

    public function getPaymentStatusLabelAttribute()
    {
        if ($this->due_amount <= 0 && $this->amount > 0) {
            return 'paid';
        } elseif ($this->paid_amount > 0 && $this->due_amount > 0) {
            return 'partial';
        } elseif ($this->due_amount > 0) {
            return 'due';
        }
        return 'paid';
    }
}
