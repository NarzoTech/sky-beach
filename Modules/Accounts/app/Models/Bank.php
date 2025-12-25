<?php

namespace Modules\Accounts\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Accounts\Database\factories\BankFactory;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name'];
}
