<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Accounts\app\Models\Account;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'date', 'amount', 'account_id', 'payment_type', 'note', 'type_id', 'created_by', 'updated_by', 'payment_type'];

    public function account()
    {
        return $this->belongsTo(Account::class)->withDefault();
    }

    public function type()
    {
        return $this->belongsTo(AssetType::class)->withDefault();
    }
}
