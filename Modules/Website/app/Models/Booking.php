<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number',
        'name',
        'email',
        'phone',
        'booking_date',
        'booking_time',
        'number_of_guests',
        'table_preference',
        'special_request',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'number_of_guests' => 'integer',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed']);
    }

    public static function generateBookingNumber()
    {
        $prefix = 'BK';
        $date = now()->format('Ymd');
        $lastBooking = self::whereDate('created_at', now())->latest()->first();
        $number = $lastBooking ? intval(substr($lastBooking->booking_number, -4)) + 1 : 1;
        
        return $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
