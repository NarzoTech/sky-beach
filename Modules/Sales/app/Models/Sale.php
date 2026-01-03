<?php

namespace Modules\Sales\app\Models;

use App\Models\Admin;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Currency\app\Models\MultiCurrency;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Sales\Database\factories\SaleFactory;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'customer_id',
        'warehouse_id',
        'quantity',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'payment_details',
        'order_discount',
        'total_tax',
        'grand_total',
        'notes',
        'invoice',
        'shipping_cost',
        'currency_id',
        'exchange_rate',
        'paid_amount',
        'sale_note',
        'staff_note',
        'order_date',
        'created_by',
        'updated_by',
        'receive_amount',
        'due_amount',
        'discount_amount',
        'due_date',
        'return_amount',

    ];

    protected $casts = [
        'order_date' => 'date',
        'payment_method' => 'array',
    ];

    public function details()
    {
        return $this->hasMany(ProductSale::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id')->withDefault(['name' => 'Guest']);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id')->withDefault();
    }
    public function stock()
    {
        return $this->hasMany(Stock::class, 'sale_id', 'id');
    }

    public function currency()
    {
        return $this->belongsTo(MultiCurrency::class, 'currency_id', 'id')->withDefault();
    }

    public function products()
    {
        return $this->hasMany(ProductSale::class, 'sale_id')->whereNotNull('ingredient_id');
    }

    public function menuItems()
    {
        return $this->hasMany(ProductSale::class, 'sale_id')->whereNotNull('menu_item_id');
    }

    public function services()
    {
        return $this->hasMany(ProductSale::class, 'sale_id')->whereNotNull('service_id');
    }

    public function payment()
    {
        return $this->hasMany(CustomerPayment::class, 'sale_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }

    public function customer_due()
    {
        return $this->hasOne(CustomerDue::class, 'invoice', 'invoice');
    }

    public function saleReturns()
    {
        return $this->hasMany(SalesReturn::class, 'sale_id');
    }

    public function saleReturnDetails()
    {
        return $this->hasManyThrough(SalesReturnDetails::class, SalesReturn::class, 'sale_id', 'sale_return_id', 'id', 'id');
    }
}
