<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'ledger_id',
        'invoice',
        'amount',
    ];


    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'ledger_id', 'id')->withDefault();
    }
}
