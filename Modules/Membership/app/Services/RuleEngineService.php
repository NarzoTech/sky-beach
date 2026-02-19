<?php

namespace Modules\Membership\app\Services;

use Modules\Membership\app\Models\LoyaltyProgram;
use Modules\Membership\app\Models\LoyaltyRule;

class RuleEngineService
{
    /**
     * Evaluate rules and calculate points
     *
     * @param  LoyaltyProgram  $program
     * @param  array  $context
     * @return array
     */
    public function evaluateRules(LoyaltyProgram $program, $context)
    {
        $basePoints = $this->calculateBasePoints($program, $context);
        $breakdown = [];
        $totalMultiplier = 1;

        // Get all active rules ordered by priority
        $rules = $program->rules()
            ->active()
            ->orderByPriority()
            ->get();

        foreach ($rules as $rule) {
            if ($this->ruleApplies($rule, $context)) {
                $ruleResult = $this->applyRule($rule, $basePoints, $context);

                if ($ruleResult['applies']) {
                    $breakdown[] = [
                        'rule_id' => $rule->id,
                        'rule_name' => $rule->name,
                        'action_type' => $rule->action_type,
                        'action_value' => $rule->action_value,
                        'points_generated' => $ruleResult['points'],
                    ];

                    // Handle different action types
                    if ($rule->action_type === 'multiply_points') {
                        $totalMultiplier *= $rule->action_value;
                    } elseif ($rule->action_type === 'bonus_points') {
                        $basePoints += $rule->action_value;
                    }
                }
            }
        }

        // Apply final multiplier
        $finalPoints = floor($basePoints * $totalMultiplier);

        return [
            'total_points' => $finalPoints,
            'base_points' => $basePoints,
            'multiplier' => $totalMultiplier,
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Check if rule applies to context
     *
     * @param  LoyaltyRule  $rule
     * @param  array  $context
     * @return bool
     */
    private function ruleApplies(LoyaltyRule $rule, $context)
    {
        // Check if rule is within date range
        if (! $rule->isWithinDateRange()) {
            return false;
        }

        // Check if rule is within time range
        if (! $rule->isWithinTimeRange()) {
            return false;
        }

        // Check if rule applies on this day of week
        if (! $rule->isApplicableOnDayOfWeek()) {
            return false;
        }

        // Check applicability based on context
        if ($rule->applies_to === 'specific_items' && isset($context['item_id'])) {
            if (! $rule->isApplicableToItem($context['item_id'])) {
                return false;
            }
        }

        if ($rule->applies_to === 'specific_categories' && isset($context['category_id'])) {
            if (! $rule->isApplicableToCategory($context['category_id'])) {
                return false;
            }
        }

        // Check condition
        if (! $this->conditionMet($rule, $context)) {
            return false;
        }

        return true;
    }

    /**
     * Check if condition is met
     *
     * @param  LoyaltyRule  $rule
     * @param  array  $context
     * @return bool
     */
    private function conditionMet(LoyaltyRule $rule, $context)
    {
        switch ($rule->condition_type) {
            case 'amount':
                $amount = $context['amount'] ?? 0;
                return $rule->meetsCondition($amount);

            case 'category':
                $categoryId = $context['category_id'] ?? null;
                if (! $categoryId) {
                    return false;
                }
                $applicableCategories = $rule->condition_value['category_ids'] ?? [];

                return in_array($categoryId, $applicableCategories);

            case 'item':
                $itemId = $context['item_id'] ?? null;
                if (! $itemId) {
                    return false;
                }
                $applicableItems = $rule->condition_value['item_ids'] ?? [];

                return in_array($itemId, $applicableItems);

            case 'time_period':
                return true; // Already checked above

            case 'customer_group':
                $customerId = $context['customer_id'] ?? null;

                return $customerId !== null;

            default:
                return true;
        }
    }

    /**
     * Apply rule and return points generated
     *
     * @param  LoyaltyRule  $rule
     * @param  float  $basePoints
     * @param  array  $context
     * @return array
     */
    private function applyRule(LoyaltyRule $rule, $basePoints, $context)
    {
        $applies = false;
        $points = 0;

        switch ($rule->action_type) {
            case 'earn_points':
                $applies = true;
                $points = $rule->action_value;
                break;

            case 'bonus_points':
                $applies = true;
                $points = $rule->action_value;
                break;

            case 'multiply_points':
                $applies = true;
                $points = $basePoints * $rule->action_value;
                break;

            case 'redeem_discount':
                $applies = true;
                $points = $rule->action_value;
                break;
        }

        return [
            'applies' => $applies,
            'points' => $points,
        ];
    }

    /**
     * Calculate base points from program settings
     *
     * @param  LoyaltyProgram  $program
     * @param  array  $context
     * @return float
     */
    private function calculateBasePoints(LoyaltyProgram $program, $context)
    {
        $amount = $context['amount'] ?? 0;

        // Check minimum transaction amount
        if ($program->min_transaction_amount && $amount < $program->min_transaction_amount) {
            return 0;
        }

        // Use simplified earning_rules if available (spend_amount → points_earned)
        $earningRules = $program->earning_rules;
        if ($earningRules && isset($earningRules['spend_amount'], $earningRules['points_earned'])) {
            $spendAmount = (float) $earningRules['spend_amount'];
            $pointsEarned = (int) $earningRules['points_earned'];
            if ($spendAmount > 0) {
                return floor($amount / $spendAmount) * $pointsEarned;
            }
            return 0;
        }

        if ($program->earning_type === 'per_transaction') {
            return $program->earning_rate;
        }

        // per_amount type (legacy fallback)
        return floor($amount * $program->earning_rate);
    }

    /**
     * Get applicable rules for a program
     *
     * @param  LoyaltyProgram  $program
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getApplicableRules(LoyaltyProgram $program)
    {
        return $program->rules()
            ->active()
            ->orderByPriority()
            ->get();
    }

    /**
     * Validate rule data before saving
     *
     * @param  array  $data
     * @return array
     */
    public function validateRuleData($data)
    {
        $errors = [];

        if (! isset($data['name']) || empty($data['name'])) {
            $errors['name'] = 'Rule name is required';
        }

        if (! isset($data['action_type']) || empty($data['action_type'])) {
            $errors['action_type'] = 'Action type is required';
        }

        if (! isset($data['action_value']) || $data['action_value'] === null) {
            $errors['action_value'] = 'Action value is required';
        }

        if ($data['start_date'] && $data['end_date']) {
            if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
                $errors['date_range'] = 'Start date must be before end date';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
