<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxPeriodSummary extends Model
{
    use HasFactory;

    protected $table = 'tax_period_summaries';

    protected $fillable = [
        'period_start',
        'period_end',
        'total_tax_collected',
        'total_tax_paid',
        'net_tax_payable',
        'total_taxable_sales',
        'total_taxable_purchases',
        'total_transactions',
        'status',
        'closed_at',
        'closed_by',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_tax_collected' => 'decimal:2',
        'total_tax_paid' => 'decimal:2',
        'net_tax_payable' => 'decimal:2',
        'total_taxable_sales' => 'decimal:2',
        'total_taxable_purchases' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    // Relationships
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function taxLedgerEntries()
    {
        return TaxLedger::forPeriod($this->period_start, $this->period_end)->get();
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeFiled($query)
    {
        return $query->where('status', 'filed');
    }

    // Methods
    public static function getOrCreateForPeriod($startDate, $endDate)
    {
        $existing = self::where('period_start', $startDate)
            ->where('period_end', $endDate)
            ->first();

        if ($existing) {
            return $existing;
        }

        return self::create([
            'period_start' => $startDate,
            'period_end' => $endDate,
            'status' => 'open',
        ]);
    }

    public function recalculate()
    {
        $summary = TaxLedger::getTaxSummaryForPeriod($this->period_start, $this->period_end);

        $this->update([
            'total_tax_collected' => $summary['total_tax_collected'],
            'total_tax_paid' => $summary['total_tax_paid'],
            'net_tax_payable' => $summary['net_tax_payable'],
            'total_taxable_sales' => $summary['total_taxable_sales'],
            'total_taxable_purchases' => $summary['total_taxable_purchases'],
            'total_transactions' => $summary['total_transactions'],
        ]);

        return $this;
    }

    public function close($notes = null)
    {
        if ($this->status !== 'open') {
            return false;
        }

        $this->recalculate();

        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
            'notes' => $notes,
        ]);

        return true;
    }

    public function markAsFiled($notes = null)
    {
        if ($this->status !== 'closed') {
            return false;
        }

        $this->update([
            'status' => 'filed',
            'notes' => $notes ?? $this->notes,
        ]);

        return true;
    }

    public function getStatusBadgeClass()
    {
        return match ($this->status) {
            'open' => 'badge-warning',
            'closed' => 'badge-info',
            'filed' => 'badge-success',
            default => 'badge-secondary',
        };
    }

    public function getPeriodLabel()
    {
        return $this->period_start->format('M Y');
    }
}
