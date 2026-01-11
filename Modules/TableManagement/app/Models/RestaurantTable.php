<?php

namespace Modules\TableManagement\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Sales\app\Models\Sale;

class RestaurantTable extends Model
{
    use SoftDeletes;

    protected $table = 'restaurant_tables';

    protected $fillable = [
        'name',
        'table_number',
        'capacity',
        'occupied_seats',
        'floor',
        'section',
        'shape',
        'position_x',
        'position_y',
        'status',
        'current_sale_id',
        'notes',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'occupied_seats' => 'integer',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'sort_order' => 'integer',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_RESERVED = 'reserved';
    const STATUS_MAINTENANCE = 'maintenance';

    const STATUSES = [
        self::STATUS_AVAILABLE => 'Available',
        self::STATUS_OCCUPIED => 'Occupied',
        self::STATUS_RESERVED => 'Reserved',
        self::STATUS_MAINTENANCE => 'Maintenance',
    ];

    const SHAPES = [
        'square' => 'Square',
        'round' => 'Round',
        'rectangle' => 'Rectangle',
    ];

    public function currentSale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'current_sale_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'table_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(TableReservation::class, 'table_id');
    }

    public function todayReservations(): HasMany
    {
        return $this->reservations()
            ->whereDate('reservation_date', today())
            ->whereNotIn('status', ['cancelled', 'completed', 'no_show']);
    }

    public function upcomingReservation()
    {
        return $this->reservations()
            ->whereDate('reservation_date', today())
            ->where('reservation_time', '>=', now()->format('H:i:s'))
            ->whereNotIn('status', ['cancelled', 'completed', 'no_show', 'seated'])
            ->orderBy('reservation_time')
            ->first();
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE && $this->is_active;
    }

    /**
     * Check if table has available seats
     */
    public function hasAvailableSeats(): bool
    {
        return $this->is_active && $this->getAvailableSeats() > 0;
    }

    /**
     * Get number of available seats
     */
    public function getAvailableSeats(): int
    {
        return max(0, $this->capacity - $this->occupied_seats);
    }

    /**
     * Get available seats attribute for easy access
     */
    public function getAvailableSeatsAttribute(): int
    {
        return $this->getAvailableSeats();
    }

    public function isOccupied(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    /**
     * Check if table is fully occupied
     */
    public function isFullyOccupied(): bool
    {
        return $this->occupied_seats >= $this->capacity;
    }

    /**
     * Check if table is partially occupied
     */
    public function isPartiallyOccupied(): bool
    {
        return $this->occupied_seats > 0 && $this->occupied_seats < $this->capacity;
    }

    public function isReserved(): bool
    {
        return $this->status === self::STATUS_RESERVED;
    }

    /**
     * Occupy seats on the table
     *
     * @param Sale $sale The sale/order
     * @param int|null $guestCount Number of guests (defaults to sale's guest_count or 1)
     */
    public function occupy(Sale $sale, ?int $guestCount = null): void
    {
        // Refresh to get current state from database
        $this->refresh();

        $initialSeats = $this->occupied_seats;
        $guestCount = (int) ($guestCount ?? $sale->guest_count ?? 1);

        \Illuminate\Support\Facades\Log::info('Table occupy - before', [
            'table_id' => $this->id,
            'initial_occupied_seats' => $initialSeats,
            'guest_count_to_add' => $guestCount,
            'capacity' => $this->capacity,
            'sale_guest_count' => $sale->guest_count
        ]);

        // Add guests to occupied seats
        $this->occupied_seats = min($this->capacity, $initialSeats + $guestCount);

        // Set status based on occupancy
        if ($this->occupied_seats > 0) {
            $this->status = self::STATUS_OCCUPIED;
        }

        // Keep track of current sale (for backward compatibility)
        // Note: For multiple orders, this will be the latest sale
        $this->current_sale_id = $sale->id;
        $this->save();

        \Illuminate\Support\Facades\Log::info('Table occupy - after', [
            'table_id' => $this->id,
            'new_occupied_seats' => $this->occupied_seats,
            'status' => $this->status
        ]);
    }

    /**
     * Release seats from a specific order
     *
     * @param int|null $guestCount Number of guests to release (null = release all)
     */
    public function release(?int $guestCount = null): void
    {
        if ($guestCount === null) {
            // Release all seats
            $this->occupied_seats = 0;
        } else {
            // Release specific number of seats
            $this->occupied_seats = max(0, $this->occupied_seats - $guestCount);
        }

        // Update status based on remaining occupancy
        if ($this->occupied_seats <= 0) {
            $this->status = self::STATUS_AVAILABLE;
            $this->current_sale_id = null;
            $this->occupied_seats = 0;
        }

        $this->save();
    }

    /**
     * Release seats for a specific sale/order
     */
    public function releaseForSale(Sale $sale): void
    {
        $guestCount = $sale->guest_count ?? 1;
        $this->release($guestCount);
    }

    public function reserve(): void
    {
        $this->status = self::STATUS_RESERVED;
        $this->save();
    }

    /**
     * Get all active orders on this table
     */
    public function activeOrders()
    {
        return $this->sales()
            ->whereIn('status', ['pending', 'processing'])
            ->where('order_type', 'dine_in');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_AVAILABLE => 'success',
            self::STATUS_OCCUPIED => 'danger',
            self::STATUS_RESERVED => 'warning',
            self::STATUS_MAINTENANCE => 'secondary',
            default => 'primary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE)->where('is_active', true);
    }

    /**
     * Scope to get tables with available seats (including partially occupied)
     */
    public function scopeWithAvailableSeats($query)
    {
        return $query->where('is_active', true)
            ->whereRaw('occupied_seats < capacity')
            ->whereIn('status', [self::STATUS_AVAILABLE, self::STATUS_OCCUPIED]);
    }

    public function scopeByFloor($query, $floor)
    {
        return $query->where('floor', $floor);
    }

    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }

    public static function generateTableNumber(): string
    {
        $lastTable = self::withTrashed()->latest('id')->first();
        $nextNumber = $lastTable ? intval(preg_replace('/\D/', '', $lastTable->table_number)) + 1 : 1;
        return 'T' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
