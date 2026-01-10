<?php

namespace Modules\Membership\app\Services;

use Modules\Membership\app\Models\LoyaltyCustomer;
use Modules\Membership\app\Models\LoyaltyProgram;
use Modules\Membership\app\Models\LoyaltyTransaction;

class PointCalculationService
{
    protected $ruleEngineService;

    public function __construct(RuleEngineService $ruleEngineService)
    {
        $this->ruleEngineService = $ruleEngineService;
    }

    /**
     * Calculate and earn points for a sale
     *
     * @param  LoyaltyCustomer  $customer
     * @param  LoyaltyProgram  $program
     * @param  array  $context
     * @return array
     */
    public function earnPoints(LoyaltyCustomer $customer, LoyaltyProgram $program, $context)
    {
        // Evaluate rules to get total points
        $evaluation = $this->ruleEngineService->evaluateRules($program, $context);
        $pointsToEarn = $evaluation['total_points'];

        if ($pointsToEarn <= 0) {
            return [
                'success' => false,
                'message' => 'No points earned',
                'points_earned' => 0,
                'total_points' => $customer->total_points,
                'breakdown' => [],
            ];
        }

        // Record previous balance
        $balanceBefore = $customer->total_points;

        // Update customer points
        $customer->total_points += $pointsToEarn;
        $customer->lifetime_points += $pointsToEarn;
        $customer->last_purchase_at = now();
        $customer->save();

        // Create transaction record
        $transaction = LoyaltyTransaction::create([
            'loyalty_customer_id' => $customer->id,
            'warehouse_id' => $context['warehouse_id'] ?? null,
            'transaction_type' => 'earn',
            'points_amount' => $pointsToEarn,
            'points_balance_before' => $balanceBefore,
            'points_balance_after' => $customer->total_points,
            'source_type' => 'sale',
            'source_id' => $context['sale_id'] ?? null,
            'description' => $context['description'] ?? 'Points earned from sale',
            'notes' => json_encode($evaluation['breakdown']),
            'created_by' => $context['created_by'] ?? null,
        ]);

        return [
            'success' => true,
            'message' => 'Points earned successfully',
            'points_earned' => $pointsToEarn,
            'total_points' => $customer->total_points,
            'breakdown' => $evaluation['breakdown'],
            'transaction_id' => $transaction->id,
        ];
    }

    /**
     * Calculate points for redemption
     *
     * @param  LoyaltyProgram  $program
     * @param  float  $pointsToRedeem
     * @return array
     */
    public function calculateRedemptionValue(LoyaltyProgram $program, $pointsToRedeem)
    {
        // Validate redemption constraints
        $redemptionRules = $program->redemption_rules ?? [];
        $minPoints = $redemptionRules['min_points'] ?? 0;
        $maxPerTransaction = $redemptionRules['max_per_transaction'] ?? PHP_FLOAT_MAX;

        if ($pointsToRedeem < $minPoints) {
            return [
                'valid' => false,
                'error' => "Minimum {$minPoints} points required for redemption",
                'min_points' => $minPoints,
            ];
        }

        if ($pointsToRedeem > $maxPerTransaction) {
            return [
                'valid' => false,
                'error' => "Maximum {$maxPerTransaction} points allowed per transaction",
                'max_points' => $maxPerTransaction,
            ];
        }

        // Calculate redemption value based on program settings
        $redemptionValue = $pointsToRedeem / $program->points_per_unit;

        return [
            'valid' => true,
            'points_redeemed' => $pointsToRedeem,
            'redemption_value' => round($redemptionValue, 2),
            'redemption_type' => $program->redemption_type,
            'rate' => $program->points_per_unit,
        ];
    }

    /**
     * Deduct points after redemption
     *
     * @param  LoyaltyCustomer  $customer
     * @param  float  $pointsToRedeem
     * @param  array  $context
     * @return array
     */
    public function redeemPoints(LoyaltyCustomer $customer, $pointsToRedeem, $context)
    {
        // Validate customer has enough points
        if ($customer->total_points < $pointsToRedeem) {
            return [
                'success' => false,
                'error' => 'Insufficient points',
                'available_points' => $customer->total_points,
                'requested_points' => $pointsToRedeem,
            ];
        }

        // Record previous balance
        $balanceBefore = $customer->total_points;

        // Deduct points
        $customer->total_points -= $pointsToRedeem;
        $customer->redeemed_points += $pointsToRedeem;
        $customer->last_redemption_at = now();
        $customer->save();

        // Create transaction record
        $transaction = LoyaltyTransaction::create([
            'loyalty_customer_id' => $customer->id,
            'warehouse_id' => $context['warehouse_id'] ?? null,
            'transaction_type' => 'redeem',
            'points_amount' => -$pointsToRedeem,
            'points_balance_before' => $balanceBefore,
            'points_balance_after' => $customer->total_points,
            'source_type' => 'sale',
            'source_id' => $context['sale_id'] ?? null,
            'redemption_method' => $context['redemption_type'] ?? null,
            'redemption_value' => $context['redemption_value'] ?? null,
            'description' => $context['description'] ?? 'Points redeemed',
            'created_by' => $context['created_by'] ?? null,
        ]);

        return [
            'success' => true,
            'message' => 'Points redeemed successfully',
            'points_redeemed' => $pointsToRedeem,
            'remaining_points' => $customer->total_points,
            'transaction_id' => $transaction->id,
        ];
    }

    /**
     * Manually adjust points (admin only)
     *
     * @param  LoyaltyCustomer  $customer
     * @param  float  $pointsAdjustment (negative or positive)
     * @param  array  $context
     * @return array
     */
    public function adjustPoints(LoyaltyCustomer $customer, $pointsAdjustment, $context)
    {
        // Validate adjustment won't make points negative (optional policy)
        if ($customer->total_points + $pointsAdjustment < 0) {
            return [
                'success' => false,
                'error' => 'Adjustment would result in negative points',
                'current_points' => $customer->total_points,
                'adjustment' => $pointsAdjustment,
            ];
        }

        // Record previous balance
        $balanceBefore = $customer->total_points;

        // Apply adjustment
        $customer->total_points += $pointsAdjustment;

        // Update lifetime or redeemed points based on adjustment direction
        if ($pointsAdjustment > 0) {
            $customer->lifetime_points += $pointsAdjustment;
        } else {
            $customer->redeemed_points += abs($pointsAdjustment);
        }

        $customer->save();

        // Create transaction record
        $transaction = LoyaltyTransaction::create([
            'loyalty_customer_id' => $customer->id,
            'warehouse_id' => $context['warehouse_id'] ?? null,
            'transaction_type' => 'adjust',
            'points_amount' => $pointsAdjustment,
            'points_balance_before' => $balanceBefore,
            'points_balance_after' => $customer->total_points,
            'source_type' => 'manual_adjust',
            'description' => $context['reason'] ?? 'Manual points adjustment',
            'notes' => $context['notes'] ?? null,
            'created_by' => $context['created_by'] ?? null,
        ]);

        return [
            'success' => true,
            'message' => 'Points adjusted successfully',
            'adjustment' => $pointsAdjustment,
            'previous_balance' => $balanceBefore,
            'new_balance' => $customer->total_points,
            'transaction_id' => $transaction->id,
        ];
    }

    /**
     * Get customer points summary
     *
     * @param  LoyaltyCustomer  $customer
     * @return array
     */
    public function getPointsSummary(LoyaltyCustomer $customer)
    {
        $transactionCount = $customer->transactions()->count();
        $earningsCount = $customer->transactions()->earnings()->count();
        $redemptionsCount = $customer->transactions()->redemptions()->count();

        $lastTransaction = $customer->transactions()
            ->latest()
            ->first();

        return [
            'current_balance' => $customer->total_points,
            'lifetime_earned' => $customer->lifetime_points,
            'total_redeemed' => $customer->redeemed_points,
            'available' => $customer->total_points,
            'transaction_count' => $transactionCount,
            'earnings_count' => $earningsCount,
            'redemptions_count' => $redemptionsCount,
            'member_since' => $customer->joined_at,
            'last_purchase' => $customer->last_purchase_at,
            'last_redemption' => $customer->last_redemption_at,
            'last_transaction' => $lastTransaction ? [
                'type' => $lastTransaction->transaction_type,
                'amount' => $lastTransaction->points_amount,
                'date' => $lastTransaction->created_at,
            ] : null,
        ];
    }

    /**
     * Get transaction history
     *
     * @param  LoyaltyCustomer  $customer
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactionHistory(LoyaltyCustomer $customer, $limit = 20)
    {
        return $customer->transactions()
            ->latest()
            ->take($limit)
            ->get();
    }
}
