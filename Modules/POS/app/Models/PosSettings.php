<?php

namespace Modules\POS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\POS\Database\factories\PosSettingsFactory;

class PosSettings extends Model
{
    use HasFactory;

    protected $table = 'pos_settings';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'note_customer',
        'show_note',
        'show_barcode',
        'show_discount',
        'show_customer',
        'show_warehouse',
        'show_email',
        'show_phone',
        'show_address',
        'is_printable',
        'merge_cart_items',
        'pos_tax_rate',
    ];
}
