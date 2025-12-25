<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Supplier\app\Models\Supplier;
use Modules\Expense\app\Models\ExpenseSupplier;
use Modules\Purchase\app\Models\Purchase;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'sale_return_id',
        'supplier_id',
        'expense_supplier_id',
        'amount',
        'total_amount',
        'is_paid',
        'is_received',
        'due_amount',
        'invoice_type',
        'invoice_url',
        'invoice_no',
        'note',
        'date',
        'created_by',
        'updated_by',
    ];


    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id')->withDefault(['name' => 'Guest']);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->withDefault(['name' => 'Guest']);
    }

    public function expenseSupplier()
    {
        return $this->belongsTo(ExpenseSupplier::class, 'expense_supplier_id', 'id')->withDefault(['name' => 'Guest']);
    }

    public function details()
    {
        return $this->hasMany(LedgerDetails::class, 'ledger_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id')->withDefault();
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'invoice_no', 'invoice_number');
    }
}
