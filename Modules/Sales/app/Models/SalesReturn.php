<?php

namespace Modules\Sales\app\Models;

use App\Models\Ledger;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Sales\Database\factories\SalesReturnFactory;

class SalesReturn extends Model
{
    use HasFactory;

    protected $table = 'sales_return';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sale_id',
        'customer_id',
        'order_date',
        'return_date',
        'return_amount',
        'return_due',
        'invoice',
        'note',
        'status',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'sale_id')->withDefault();
    }

    public function ledger()
    {
        return $this->hasOne(Ledger::class, 'sale_return_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'sale_return_id');
    }
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id')->withDefault(['name' => 'Guest']);
    }

    public function details()
    {
        return $this->hasMany(SalesReturnDetails::class, 'sale_return_id');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'sale_return_id');
    }
}
