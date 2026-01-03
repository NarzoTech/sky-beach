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

    public function isOccupied(): bool
    {
        return $this->status === self::STATUS_OCCUPIED;
    }

    public function isReserved(): bool
    {
        return $this->status === self::STATUS_RESERVED;
    }

    public function occupy(Sale $sale): void
    {
        $this->status = self::STATUS_OCCUPIED;
        $this->current_sale_id = $sale->id;
        $this->save();
    }

    public function release(): void
    {
        $this->status = self::STATUS_AVAILABLE;
        $this->current_sale_id = null;
        $this->save();
    }

    public function reserve(): void
    {
        $this->status = self::STATUS_RESERVED;
        $this->save();
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
