<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounts\app\Models\Account;
use Modules\Purchase\app\Models\Purchase;
use Modules\Sales\app\Models\Sale;
use Modules\Supplier\app\Models\Supplier;
use Mollie\Api\Resources\Customer;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'purchase_id',
        'sale_id',
        'expense_id',
        'supplier_id',
        'customer_id',
        'account_id',
        'payment_type',
        'amount',
        'payment_date',
        'note',
        'is_received',
        'is_paid',
        'created_by',
        'updated_by',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id')->withDefault();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withDefault(['name' => 'Guest']);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id')->withDefault(['name' => 'Guest']);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by')->withDefault();
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id')->withDefault();
    }
}
