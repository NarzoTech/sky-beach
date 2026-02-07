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
        'uid',
        'user_id',
        'customer_id',
        'warehouse_id',
        'quantity',
        'total_price',
        'status',
        'order_type',
        'table_id',
        'waiter_id',
        'guest_count',
        'estimated_prep_minutes',
        'delivery_address',
        'delivery_phone',
        'delivery_notes',
        'payment_status',
        'payment_method',
        'payment_details',
        'order_discount',
        'total_tax',
        'tax_rate',
        'grand_total',
        'total_cogs',
        'gross_profit',
        'profit_margin',
        'notes',
        'invoice',
        'shipping_cost',
        'currency_id',
        'exchange_rate',
        'paid_amount',
        'sale_note',
        'special_instructions',
        'staff_note',
        'order_date',
        'created_by',
        'updated_by',
        'receive_amount',
        'due_amount',
        'discount_amount',
        'due_date',
        'return_amount',
        'original_table_id',
        'table_transfer_log',
    ];

    const ORDER_TYPE_DINE_IN = 'dine_in';
    const ORDER_TYPE_TAKE_AWAY = 'take_away';
    const ORDER_TYPE_WEBSITE = 'website';

    const ORDER_TYPES = [
        self::ORDER_TYPE_DINE_IN => 'Dine In',
        self::ORDER_TYPE_TAKE_AWAY => 'Take Away',
        self::ORDER_TYPE_WEBSITE => 'Website',
    ];

    const ORDER_STATUSES = [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'preparing' => 'Preparing',
        'ready' => 'Ready',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    protected $casts = [
        'order_date' => 'date',
        'payment_method' => 'array',
        'table_transfer_log' => 'array',
    ];

    protected $appends = ['total'];

    /**
     * Get total attribute (alias for grand_total)
     * Calculates from items if grand_total is 0 or null
     */
    public function getTotalAttribute()
    {
        // Return grand_total if it's set and not 0
        if (!empty($this->grand_total) && $this->grand_total > 0) {
            return $this->grand_total;
        }

        // Return total_price if it's set and not 0
        if (!empty($this->total_price) && $this->total_price > 0) {
            $discount = $this->order_discount ?? 0;
            $tax = $this->total_tax ?? 0;
            return $this->total_price - $discount + $tax;
        }

        // Calculate from items as last resort
        if ($this->relationLoaded('details') && $this->details->count() > 0) {
            $subtotal = $this->details->sum('sub_total');
            $discount = $this->order_discount ?? 0;
            $tax = $this->total_tax ?? 0;
            return $subtotal - $discount + $tax;
        }

        return 0;
    }

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

    /**
     * Alias for payment relationship
     */
    public function payments()
    {
        return $this->payment();
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

    public function table()
    {
        return $this->belongsTo(\Modules\TableManagement\app\Models\RestaurantTable::class, 'table_id');
    }

    public function waiter()
    {
        return $this->belongsTo(\Modules\Employee\app\Models\Employee::class, 'waiter_id');
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return self::ORDER_TYPES[$this->order_type] ?? $this->order_type;
    }

    public function isDineIn(): bool
    {
        return $this->order_type === self::ORDER_TYPE_DINE_IN;
    }

    public function isTakeAway(): bool
    {
        return $this->order_type === self::ORDER_TYPE_TAKE_AWAY;
    }

    public function isWebsite(): bool
    {
        return $this->order_type === self::ORDER_TYPE_WEBSITE;
    }

    /**
     * Get status label attribute
     */
    public function getStatusLabelAttribute(): string
    {
        return self::ORDER_STATUSES[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'confirmed' => 'bg-info',
            'preparing' => 'bg-primary',
            'ready' => 'bg-success',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Scope for website orders
     */
    public function scopeWebsiteOrders($query)
    {
        return $query->where(function ($q) {
            $q->where('order_type', self::ORDER_TYPE_WEBSITE)
              ->orWhereRaw("JSON_EXTRACT(notes, '$.source') = 'website'");
        });
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
