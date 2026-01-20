<?php

namespace Modules\Website\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_number',
        'user_id',
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
        'confirmation_code',
        'reminder_sent',
        'cancelled_at',
        'cancelled_reason',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'number_of_guests' => 'integer',
        'reminder_sent' => 'boolean',
        'cancelled_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_NO_SHOW = 'no_show';

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_NO_SHOW => 'No Show',
    ];

    const TABLE_PREFERENCES = [
        'any' => 'Any Available',
        'indoor' => 'Indoor',
        'outdoor' => 'Outdoor',
        'window' => 'Window Side',
        'private' => 'Private Room',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = self::generateBookingNumber();
            }
            if (empty($booking->confirmation_code)) {
                $booking->confirmation_code = self::generateConfirmationCode();
            }
        });
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /**
     * Scope: Pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: Confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope: Upcoming bookings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Scope: Past bookings
     */
    public function scopePast($query)
    {
        return $query->where('booking_date', '<', now()->toDateString());
    }

    /**
     * Scope: For a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('booking_date', $date);
    }

    /**
     * Generate unique booking number
     */
    public static function generateBookingNumber()
    {
        $prefix = 'BK';
        $date = now()->format('Ymd');
        $lastBooking = self::whereDate('created_at', now())->latest()->first();
        $number = $lastBooking ? intval(substr($lastBooking->booking_number, -4)) + 1 : 1;

        return $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique confirmation code
     */
    public static function generateConfirmationCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('confirmation_code', $code)->exists());

        return $code;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_CONFIRMED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            self::STATUS_COMPLETED => 'bg-info',
            self::STATUS_NO_SHOW => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get table preference label
     */
    public function getTablePreferenceLabelAttribute()
    {
        return self::TABLE_PREFERENCES[$this->table_preference] ?? ucfirst($this->table_preference);
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled()
    {
        // Can only cancel pending or confirmed bookings that are in the future
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])) {
            return false;
        }

        // Must be at least 2 hours before booking time
        $bookingDateTime = $this->booking_date->setTimeFromTimeString($this->booking_time->format('H:i:s'));
        return $bookingDateTime->gt(now()->addHours(2));
    }

    /**
     * Check if booking is upcoming
     */
    public function isUpcoming()
    {
        return $this->booking_date->gte(now()->toDateString())
            && in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateTimeAttribute()
    {
        return $this->booking_date->format('M d, Y') . ' at ' . $this->booking_time->format('h:i A');
    }
}
