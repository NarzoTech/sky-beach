<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'zip',
        'latitude',
        'longitude',
        'status',
    ];

    
}
