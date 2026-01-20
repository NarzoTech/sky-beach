<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CateringInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_number',
        'package_id',
        'name',
        'email',
        'phone',
        'event_type',
        'event_date',
        'event_time',
        'guest_count',
        'venue_address',
        'special_requirements',
        'status',
        'quoted_amount',
        'admin_notes',
        'contacted_at',
        'quoted_at',
        'confirmed_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime',
        'guest_count' => 'integer',
        'quoted_amount' => 'decimal:2',
        'contacted_at' => 'datetime',
        'quoted_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONTACTED = 'contacted';
    const STATUS_QUOTED = 'quoted';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONTACTED => 'Contacted',
        self::STATUS_QUOTED => 'Quoted',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    const EVENT_TYPES = [
        'wedding' => 'Wedding',
        'corporate' => 'Corporate Event',
        'birthday' => 'Birthday Party',
        'anniversary' => 'Anniversary',
        'graduation' => 'Graduation',
        'holiday' => 'Holiday Party',
        'reunion' => 'Family Reunion',
        'conference' => 'Conference/Meeting',
        'other' => 'Other',
    ];

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            if (empty($inquiry->inquiry_number)) {
                $inquiry->inquiry_number = self::generateInquiryNumber();
            }
        });
    }

    /**
     * Generate unique inquiry number
     */
    public static function generateInquiryNumber()
    {
        $prefix = 'CTR';
        $date = now()->format('Ymd');
        $lastInquiry = self::whereDate('created_at', now())->latest()->first();
        $number = $lastInquiry ? intval(substr($lastInquiry->inquiry_number, -4)) + 1 : 1;

        return $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationship to CateringPackage
     */
    public function package()
    {
        return $this->belongsTo(CateringPackage::class, 'package_id')->withDefault([
            'name' => 'Custom Package',
        ]);
    }

    /**
     * Scope: Pending inquiries
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope: By status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now()->toDateString())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONTACTED, self::STATUS_QUOTED, self::STATUS_CONFIRMED]);
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
            self::STATUS_CONTACTED => 'bg-info',
            self::STATUS_QUOTED => 'bg-primary',
            self::STATUS_CONFIRMED => 'bg-success',
            self::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    /**
     * Get event type label
     */
    public function getEventTypeLabelAttribute()
    {
        return self::EVENT_TYPES[$this->event_type] ?? ucfirst($this->event_type);
    }

    /**
     * Get formatted event date time
     */
    public function getFormattedEventDateTimeAttribute()
    {
        $formatted = $this->event_date->format('M d, Y');
        if ($this->event_time) {
            $formatted .= ' at ' . $this->event_time->format('h:i A');
        }
        return $formatted;
    }

    /**
     * Calculate estimated price based on package and guest count
     */
    public function getEstimatedPriceAttribute()
    {
        if ($this->package && $this->package->price_per_person) {
            return $this->package->calculatePrice($this->guest_count);
        }
        return null;
    }
}
