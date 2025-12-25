<?php

namespace Modules\Purchase\app\Models;

use App\Models\Admin;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Purchase\Database\factories\PurchaseReturnFactory;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;

class PurchaseReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'purchase_returns';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'purchase_id',
        'return_type_id',
        'warehouse_id',
        'return_date',
        'note',
        'payment_method',
        'received_amount',
        'return_amount',
        'payment_status',
        'shipping_cost',
        'created_by',
        'updated_by',
        'supplier_id',
        'invoice',
    ];

    // relationships
    public function purchase()
    {
        return $this->belongsTo(Purchase::class)->withDefault();
    }
    public function returnType()
    {
        return $this->belongsTo(PurchaseReturnType::class, 'return_type_id', 'id')->withDefault();
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class)->withDefault();
    }
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseReturnDetails::class);
    }

    public function payment()
    {
        return $this->hasOne(SupplierPayment::class, 'purchase_return_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by', 'id')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by', 'id')->withDefault();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id')->withDefault(['name' => 'Guest']);
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'purchase_return_id');
    }
}
