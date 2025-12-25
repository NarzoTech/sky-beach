<?php

namespace Modules\Supplier\app\Models;

use App\Models\Admin;
use App\Models\Ledger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Accounts\app\Models\Account;
use Modules\Purchase\app\Models\Purchase;
use Modules\Supplier\Database\factories\SupplierPaymentFactory;

class SupplierPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_id',
        'invoice',
        'purchase_return_id',
        'supplier_id',
        'account_id',
        'is_guest',
        'is_received',
        'is_paid',
        'payment_type',
        'account_type',
        'amount',
        'payment_date',
        'note',
        'ledger_id',
        'created_by',
        'updated_by',
    ];


    public function purchase()
    {

        return $this->belongsTo(Purchase::class, 'purchase_id', 'id')->withDefault();
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->withDefault(['name' => 'Guest']);
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
