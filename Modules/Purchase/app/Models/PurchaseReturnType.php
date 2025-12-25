<?php

namespace Modules\Purchase\app\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Purchase\Database\factories\PurchaseReturnTypeFactory;

class PurchaseReturnType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(Admin::class, 'updated_by')->withDefault();
    }
}
