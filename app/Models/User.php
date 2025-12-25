<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Customer\app\Models\Area;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Customer\app\Models\UserGroup;
use Modules\LiveChat\app\Models\Message;
use Modules\Order\app\Models\OrderReview;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'group_id',
        'area_id',
        'membership',
        'date',
        'address',
        'status',
        'wallet_balance',
        'plate_number',
        'guest',
    ];

    protected $appends = ['total_due'];

    public function group()
    {
        return $this->belongsTo(UserGroup::class, 'group_id')->withDefault();
    }
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id')->withDefault();
    }

    public function due()
    {
        return $this->hasMany(CustomerDue::class, 'customer_id');
    }
    public function getTotalSalesAttribute()
    {
        return $this->sales->sum('grand_total');
    }

    public function getTotalDueAttribute()
    {
        $prevDue = $this->wallet_balance ?? 0;
        $totalSales = $this->total_sales;

        // Payments that reduce customer due (sale payments + due receive)
        $totalPaid = $this->payment
            ->whereIn('payment_type', ['sale', 'due_receive'])
            ->sum('amount');

        // Sale returns reduce the amount customer owes
        $totalSaleReturn = $this->sales->sum(function ($sale) {
            return $sale->saleReturns->sum('return_amount');
        });

        return $totalSales - $totalPaid - $totalSaleReturn + $prevDue;
    }

    public function sales()
    {
        $sales = $this->hasMany(Sale::class, 'customer_id');

        // Only filter by date if dates are provided
        if (request()->from_date || request()->to_date) {
            $from_date = request()->from_date ? now()->parse(request()->from_date) : now()->subYear();
            $to_date = request()->to_date ? now()->parse(request()->to_date) : now();
            $sales = $sales->whereBetween('order_date', [$from_date, $to_date]);
        }

        return $sales->with('saleReturns');
    }


    public function saleReturn()
    {
        $return = $this->hasManyThrough(Sale::class, SalesReturn::class, 'customer_id', 'id', 'id', 'sale_id');
        return $return;
    }

    public function getTotalPaidAttribute()
    {
        $payment = $this->payment->where('is_received', 1);
        return $payment->sum('amount');
    }


    // public function payment()
    // {
    //     $from_date = null;
    //     $to_date = null;
    //     if (request()->from_date) {
    //         $from_date = now()->parse(request()->from_date);
    //     }
    //     if (request()->to_date) {
    //         $to_date = now()->parse(request()->to_date);
    //     }

    //     // current route
    //     $route = request()->route()->getName();
    //     $payment = $this->hasMany(CustomerPayment::class, 'customer_id');

    //     if ($from_date || $to_date) {
    //         $payment = $payment->whereBetween('payment_date', [$from_date, $to_date]);
    //     }

    //     if ($route == 'admin.report.customers') {
    //         return $payment->whereBetween('payment_date', [$from_date, $to_date]);
    //     }

    //     return $payment;
    // }

    public function payment()
    {
        // Initialize the relationship query
        $payment = $this->hasMany(CustomerPayment::class, 'customer_id');

        // Only apply date range filter if dates are provided
        if (request()->from_date || request()->to_date) {
            $from_date = request()->from_date ? \Carbon\Carbon::parse(request()->from_date)->startOfDay() : now()->subYear()->startOfDay();
            $to_date = request()->to_date ? \Carbon\Carbon::parse(request()->to_date)->endOfDay() : now()->endOfDay();
            $payment->whereBetween('payment_date', [$from_date, $to_date]);
        }

        return $payment;
    }

    public function advances()
    {
        $advance = $this->payment->where('payment_type', 'advance_receive')->sum('amount');
        $advanceRefund = $this->payment->where('payment_type', 'advance_refund')->sum('amount');
        return $advance - $advanceRefund;
    }

    public function orderReviews()
    {
        return $this->hasMany(OrderReview::class, 'user_id');
    }
}
