<?php

namespace Modules\POS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Employee\app\Models\Employee;
use Modules\Sales\app\Models\Sale;

class OrderNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'waiter_id',
        'type',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    const TYPE_ORDER_READY = 'order_ready';
    const TYPE_ITEM_READY = 'item_ready';
    const TYPE_ORDER_CANCELLED = 'order_cancelled';
    const TYPE_TABLE_TRANSFER = 'table_transfer';

    /**
     * Get the sale/order
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the waiter
     */
    public function waiter()
    {
        return $this->belongsTo(Employee::class, 'waiter_id');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for a specific waiter
     */
    public function scopeForWaiter($query, $waiterId)
    {
        return $query->where('waiter_id', $waiterId);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Create order ready notification
     */
    public static function notifyOrderReady(Sale $sale): self
    {
        return self::create([
            'sale_id' => $sale->id,
            'waiter_id' => $sale->waiter_id,
            'type' => self::TYPE_ORDER_READY,
            'message' => "Order #{$sale->id} for Table {$sale->table?->name} is ready!",
        ]);
    }

    /**
     * Create item ready notification
     */
    public static function notifyItemReady(Sale $sale, string $itemName): self
    {
        return self::create([
            'sale_id' => $sale->id,
            'waiter_id' => $sale->waiter_id,
            'type' => self::TYPE_ITEM_READY,
            'message' => "{$itemName} is ready for Table {$sale->table?->name}",
        ]);
    }

    /**
     * Create table transfer notification
     */
    public static function notifyTableTransfer(Sale $sale, string $fromTable, string $toTable): self
    {
        return self::create([
            'sale_id' => $sale->id,
            'waiter_id' => $sale->waiter_id,
            'type' => self::TYPE_TABLE_TRANSFER,
            'message' => "Order #{$sale->id} transferred from {$fromTable} to {$toTable}",
        ]);
    }
}
