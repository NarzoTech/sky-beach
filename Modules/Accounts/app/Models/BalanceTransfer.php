<?php

namespace Modules\Accounts\app\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Accounts\Database\factories\BalanceTransferFactory;

class BalanceTransfer extends Model
{
    use HasFactory;

    protected $table = 'balance_transfers';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'date',
        'amount',
        'note',
        'created_by',
    ];

    public function fromAccount()
    {
        return $this->belongsTo(Account::class, 'from_account_id')->withDefault();
    }

    public function toAccount()
    {
        return $this->belongsTo(Account::class, 'to_account_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by')->withDefault();
    }
}
