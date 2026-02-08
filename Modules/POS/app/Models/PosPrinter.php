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
        'path',
        'paper_width',
        'capability_profile',
        'char_per_line',
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
    const CONNECTION_WINDOWS = 'windows';
    const CONNECTION_LINUX = 'linux';
    const CONNECTION_USB = 'usb';
    const CONNECTION_BLUETOOTH = 'bluetooth';
    const CONNECTION_BROWSER = 'browser';

    const PROFILE_DEFAULT = 'default';
    const PROFILE_SIMPLE = 'simple';
    const PROFILE_SP2000 = 'SP2000';
    const PROFILE_TEP200M = 'TEP-200M';
    const PROFILE_P822D = 'P822D';

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
     * Scope to get browser printers
     */
    public function scopeBrowser($query)
    {
        return $query->where('connection_type', self::CONNECTION_BROWSER);
    }

    /**
     * Scope to get network printers
     */
    public function scopeNetwork($query)
    {
        return $query->where('connection_type', self::CONNECTION_NETWORK);
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
