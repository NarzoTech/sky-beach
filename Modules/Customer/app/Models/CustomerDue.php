<?php

namespace Modules\Customer\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Customer\Database\factories\CustomerDueFactory;
use Modules\Sales\app\Models\Sale;

class CustomerDue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer_dues';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_id',
        'invoice',
        'due_date',
        'due_amount',
        'paid_amount',
        'status',
    ];



    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id')->withDefault(['name' => 'Guest']);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'invoice', 'invoice')->withDefault();
    }
}
