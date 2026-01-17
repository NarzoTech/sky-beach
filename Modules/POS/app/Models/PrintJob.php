<?php

namespace Modules\POS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Sales\app\Models\Sale;

class PrintJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'printer_id',
        'sale_id',
        'type',
        'content',
        'status',
        'attempts',
        'error_message',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
    ];

    const TYPE_NEW_ORDER = 'new_order';
    const TYPE_UPDATE_ORDER = 'update_order';
    const TYPE_RECEIPT = 'receipt';
    const TYPE_VOID = 'void';

    const STATUS_PENDING = 'pending';
    const STATUS_PRINTING = 'printing';
    const STATUS_PRINTED = 'printed';
    const STATUS_FAILED = 'failed';

    /**
     * Get the printer for this job
     */
    public function printer()
    {
        return $this->belongsTo(PosPrinter::class, 'printer_id');
    }

    /**
     * Get the sale/order for this job
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Scope to get pending jobs
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to get failed jobs
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Mark job as printing
     */
    public function markAsPrinting(): void
    {
        $this->update([
            'status' => self::STATUS_PRINTING,
            'attempts' => $this->attempts + 1,
        ]);
    }

    /**
     * Mark job as printed
     */
    public function markAsPrinted(): void
    {
        $this->update([
            'status' => self::STATUS_PRINTED,
            'printed_at' => now(),
        ]);
    }

    /**
     * Mark job as failed
     */
    public function markAsFailed(string $errorMessage = null): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Retry failed job
     */
    public function retry(): void
    {
        $this->update([
            'status' => self::STATUS_PENDING,
            'error_message' => null,
        ]);
    }
}
