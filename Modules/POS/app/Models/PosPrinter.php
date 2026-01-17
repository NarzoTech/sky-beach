<?php

namespace Modules\POS\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PosPrinter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'connection_type',
        'ip_address',
        'port',
        'paper_width',
        'is_active',
        'print_categories',
        'location_name',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'print_categories' => 'array',
    ];

    const TYPE_CASH_COUNTER = 'cash_counter';
    const TYPE_KITCHEN = 'kitchen';

    const CONNECTION_NETWORK = 'network';
    const CONNECTION_USB = 'usb';
    const CONNECTION_BLUETOOTH = 'bluetooth';
    const CONNECTION_BROWSER = 'browser';

    /**
     * Get all print jobs for this printer
     */
    public function printJobs()
    {
        return $this->hasMany(PrintJob::class, 'printer_id');
    }

    /**
     * Scope to get only active printers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get kitchen printers
     */
    public function scopeKitchen($query)
    {
        return $query->where('type', self::TYPE_KITCHEN);
    }

    /**
     * Scope to get cash counter printers
     */
    public function scopeCashCounter($query)
    {
        return $query->where('type', self::TYPE_CASH_COUNTER);
    }

    /**
     * Check if this is a kitchen printer
     */
    public function isKitchen(): bool
    {
        return $this->type === self::TYPE_KITCHEN;
    }

    /**
     * Check if this is a cash counter printer
     */
    public function isCashCounter(): bool
    {
        return $this->type === self::TYPE_CASH_COUNTER;
    }

    /**
     * Check if printer uses browser printing
     */
    public function usesBrowserPrinting(): bool
    {
        return $this->connection_type === self::CONNECTION_BROWSER;
    }
}
