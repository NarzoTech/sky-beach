<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'date',
        'note',
        'reference_note',
        'subtotal',
        'discount',
        'after_discount',
        'vat',
        'total',
        'created_by',
        'updated_by',
        'warehouse_id',
        'quotation_no'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id')->withDefault(['name' => 'Guest']);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by')->withDefault();
    }

    public function details()
    {
        return $this->hasMany(QuotationDetails::class);
    }
}
