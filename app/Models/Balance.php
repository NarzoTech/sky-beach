<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounts\app\Models\Account;

class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance_type',
        'payment_type',
        'date',
        'branch_id',
        'account_id',
        'created_by',
        'updated_by',
        'amount',
        'note',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class)->withDefault();
    }
}
