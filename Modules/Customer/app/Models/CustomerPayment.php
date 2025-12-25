<?php

namespace Modules\Customer\app\Models;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Accounts\app\Models\Account;
use Modules\Customer\Database\factories\CustomerPaymentFactory;
use Modules\Sales\app\Models\Sale;

class CustomerPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'customer_id',
        'account_id',
        'invoice',
        'is_guest',
        'is_received',
        'is_paid',
        'payment_type',
        'account_type',
        'amount',
        'payment_date',
        'note',
        'created_by',
        'updated_by',
        'sale_return_id'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id')->withDefault();
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
}
