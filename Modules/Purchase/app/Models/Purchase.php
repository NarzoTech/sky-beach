<?php

namespace Modules\Purchase\app\Models;

use App\Models\Admin;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Purchase\Database\factories\PurchaseFactory;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'memo_no',
        'invoice_number',
        'reference_no',
        'purchase_date',
        'items',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'payment_type',
        'note',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'payment_type' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->withDefault(['name' => 'Guest']);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id')->withDefault();
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetails::class, 'purchase_id', 'id');
    }

    public function purchaseReturn()
    {
        return $this->hasMany(PurchaseReturn::class, 'purchase_id', 'id');
    }

    public function products()
    {
        return $this->belongsToMany(Ingredient::class, 'purchase_details', 'purchase_id', 'product_id')->withPivot('quantity', 'purchase_price', 'sub_total', 'profit', 'sale_price', 'discount', 'tax', 'created_by', 'updated_by')->withDefault();
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'purchase_id');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'purchase_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id')->withDefault();
    }
}
