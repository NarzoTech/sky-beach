<?php

namespace Modules\TableManagement\app\Models;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TableReservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reservation_number',
        'table_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'reservation_date',
        'reservation_time',
        'duration_minutes',
        'party_size',
        'status',
        'special_requests',
        'notes',
        'created_by',
        'confirmed_by',
        'confirmed_at',
        'seated_at',
        'completed_at',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime:H:i',
        'confirmed_at' => 'datetime',
        'seated_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_minutes' => 'integer',
        'party_size' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_SEATED = 'seated';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_NO_SHOW = 'no_show';

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_SEATED => 'Seated',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_NO_SHOW => 'No Show',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'confirmed_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_SEATED => 'primary',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_CANCELLED => 'danger',
            self::STATUS_NO_SHOW => 'dark',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getReservationDateTimeAttribute(): Carbon
    {
        return Carbon::parse($this->reservation_date->format('Y-m-d') . ' ' . $this->reservation_time->format('H:i:s'));
    }

    public function getEndTimeAttribute(): Carbon
    {
        return $this->reservation_date_time->addMinutes($this->duration_minutes);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isUpcoming(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])
            && $this->reservation_date_time->isFuture();
    }

    public function confirm(): void
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->confirmed_by = auth('admin')->id();
        $this->confirmed_at = now();
        $this->save();

        // Reserve the table if reservation is today
        if ($this->reservation_date->isToday()) {
            $this->table->reserve();
        }
    }

    public function seat(): void
    {
        $this->status = self::STATUS_SEATED;
        $this->seated_at = now();
        $this->save();
    }

    public function complete(): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->save();
    }

    public function cancel(): void
    {
        $this->status = self::STATUS_CANCELLED;
        $this->save();

        // Release table if it was reserved for this reservation
        if ($this->table->status === RestaurantTable::STATUS_RESERVED) {
            $this->table->release();
        }
    }

    public function markNoShow(): void
    {
        $this->status = self::STATUS_NO_SHOW;
        $this->save();

        if ($this->table->status === RestaurantTable::STATUS_RESERVED) {
            $this->table->release();
        }
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reservation_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('reservation_date', '>=', today())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED])
            ->orderBy('reservation_date')
            ->orderBy('reservation_time');
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('reservation_date', $date);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_COMPLETED, self::STATUS_NO_SHOW]);
    }

    public static function generateReservationNumber(): string
    {
        $prefix = 'RES-';
        $date = now()->format('Ymd');
        $lastReservation = self::whereDate('created_at', today())->latest()->first();

        if ($lastReservation) {
            $lastNumber = intval(substr($lastReservation->reservation_number, -4));
            return $prefix . $date . '-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . $date . '-0001';
    }

    public static function checkTableAvailability($tableId, $date, $time, $duration = 120, $excludeId = null)
    {
        $startTime = Carbon::parse($date . ' ' . $time);
        $endTime = $startTime->copy()->addMinutes($duration);

        $query = self::where('table_id', $tableId)
            ->whereDate('reservation_date', $date)
            ->whereNotIn('status', [self::STATUS_CANCELLED, self::STATUS_COMPLETED, self::STATUS_NO_SHOW]);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $conflictingReservations = $query->get()->filter(function ($reservation) use ($startTime, $endTime) {
            $resStart = $reservation->reservation_date_time;
            $resEnd = $reservation->end_time;

            // Check if time ranges overlap
            return $startTime < $resEnd && $endTime > $resStart;
        });

        return $conflictingReservations->isEmpty();
    }
}
