<?php

namespace Modules\Membership\app\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyRule extends Model
{
    use HasFactory;

    protected $table = 'loyalty_rules';

    protected $fillable = [
        'loyalty_program_id',
        'name',
        'description',
        'is_active',
        'condition_type',
        'condition_value',
        'action_type',
        'action_value',
        'start_date',
        'end_date',
        'day_of_week',
        'start_time',
        'end_time',
        'applies_to',
        'applicable_items',
        'applicable_categories',
        'applicable_customer_segments',
        'priority',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'condition_value' => 'array',
        'action_value' => 'decimal:2',
        'day_of_week' => 'array',
        'applicable_items' => 'array',
        'applicable_categories' => 'array',
        'applicable_customer_segments' => 'array',
    ];

    /**
     * Relations
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class, 'loyalty_program_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('loyalty_program_id', $programId);
    }

    public function scopeOrderByPriority($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Methods
     */
    public function isActive()
    {
        return $this->is_active === true;
    }

    public function isWithinDateRange()
    {
        $now = now()->toDateString();

        if ($this->start_date && $now < $this->start_date) {
            return false;
        }

        if ($this->end_date && $now > $this->end_date) {
            return false;
        }

        return true;
    }

    public function isWithinTimeRange()
    {
        if (! $this->start_time || ! $this->end_time) {
            return true;
        }

        $now = now()->toTimeString();

        return $now >= $this->start_time && $now <= $this->end_time;
    }

    public function isApplicableOnDayOfWeek()
    {
        if (! $this->day_of_week || empty($this->day_of_week)) {
            return true;
        }

        $currentDay = now()->format('D');
        $dayMap = [
            'Mon' => 'MON',
            'Tue' => 'TUE',
            'Wed' => 'WED',
            'Thu' => 'THU',
            'Fri' => 'FRI',
            'Sat' => 'SAT',
            'Sun' => 'SUN',
        ];

        return in_array($dayMap[$currentDay], $this->day_of_week);
    }

    public function isApplicableToItem($itemId)
    {
        if ($this->applies_to === 'all') {
            return true;
        }

        if ($this->applies_to === 'specific_items') {
            return in_array($itemId, $this->applicable_items ?? []);
        }

        return false;
    }

    public function isApplicableToCategory($categoryId)
    {
        if ($this->applies_to === 'all') {
            return true;
        }

        if ($this->applies_to === 'specific_categories') {
            return in_array($categoryId, $this->applicable_categories ?? []);
        }

        return false;
    }

    public function isApplicableToCustomerSegment($segmentId)
    {
        if ($this->applies_to === 'all') {
            return true;
        }

        if ($this->applies_to === 'specific_customers') {
            return in_array($segmentId, $this->applicable_customer_segments ?? []);
        }

        return false;
    }

    public function meetsCondition($value)
    {
        if ($this->condition_type === 'amount') {
            $min = $this->condition_value['min'] ?? 0;
            $max = $this->condition_value['max'] ?? PHP_FLOAT_MAX;

            return $value >= $min && $value <= $max;
        }

        return true;
    }
}
