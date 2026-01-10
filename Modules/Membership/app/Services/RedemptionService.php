<?php

namespace Modules\Membership\app\Services;

use Modules\Membership\app\Models\LoyaltyCustomer;
use Modules\Membership\app\Models\LoyaltyProgram;
use Modules\Membership\app\Models\LoyaltyRedemption;

class RedemptionService
{
    protected $pointCalculationService;

    public function __construct(PointCalculationService $pointCalculationService)
    {
        $this->pointCalculationService = $pointCalculationService;
    }

    /**
     * Create a redemption record
     *
     * @param  LoyaltyCustomer  $customer
     * @param  LoyaltyProgram  $program
     * @param  array  $data
     * @return array
     */
    public function createRedemption(LoyaltyCustomer $customer, LoyaltyProgram $program, $data)
    {
        // Validate redemption type
        if (! in_array($data['redemption_type'], ['discount', 'free_item', 'cashback'])) {
            return [
                'success' => false,
                'error' => 'Invalid redemption type',
            ];
        }

        // Validate customer
        if ($customer->isBlocked()) {
            return [
                'success' => false,
                'error' => 'Customer is blocked from redemptions',
            ];
        }

        // Calculate redemption value
        $pointsToRedeem = $data['points_to_redeem'] ?? 0;
        $calculation = $this->pointCalculationService->calculateRedemptionValue($program, $pointsToRedeem);

        if (! $calculation['valid']) {
            return [
                'success' => false,
                'error' => $calculation['error'],
            ];
        }

        // Validate customer has points
        if (! $customer->canRedeem($pointsToRedeem)) {
            return [
                'success' => false,
                'error' => 'Insufficient points for redemption',
                'available_points' => $customer->total_points,
                'requested_points' => $pointsToRedeem,
            ];
        }

        // Create redemption record
        $redemptionData = [
            'loyalty_customer_id' => $customer->id,
            'points_used' => $pointsToRedeem,
            'redemption_type' => $data['redemption_type'],
            'status' => 'pending',
            'created_by' => $data['created_by'] ?? null,
        ];

        // Handle type-specific data
        switch ($data['redemption_type']) {
            case 'discount':
                $redemptionData['amount_value'] = $calculation['redemption_value'];
                break;

            case 'free_item':
                $redemptionData['menu_item_id'] = $data['menu_item_id'] ?? null;
                $redemptionData['ingredient_id'] = $data['ingredient_id'] ?? null;
                $redemptionData['quantity'] = $data['quantity'] ?? 1;
                break;

            case 'cashback':
                $redemptionData['amount_value'] = $calculation['redemption_value'];
                break;
        }

        if (isset($data['sale_id'])) {
            $redemptionData['sale_id'] = $data['sale_id'];
        }

        $redemption = LoyaltyRedemption::create($redemptionData);

        return [
            'success' => true,
            'message' => 'Redemption created successfully',
            'redemption_id' => $redemption->id,
            'redemption' => $redemption,
            'calculation' => $calculation,
        ];
    }

    /**
     * Apply redemption to sale
     *
     * @param  LoyaltyRedemption  $redemption
     * @param  array  $saleData
     * @return array
     */
    public function applyRedemption($redemption, $saleData = [])
    {
        // Check if already applied
        if ($redemption->isApplied()) {
            return [
                'success' => false,
                'error' => 'Redemption already applied',
            ];
        }

        $customer = $redemption->customer;

        // Deduct points
        $pointResult = $this->pointCalculationService->redeemPoints(
            $customer,
            $redemption->points_used,
            [
                'warehouse_id' => $saleData['warehouse_id'] ?? null,
                'sale_id' => $redemption->sale_id,
                'redemption_type' => $redemption->redemption_type,
                'redemption_value' => $redemption->amount_value,
                'description' => "Redemption: {$redemption->redemption_type}",
                'created_by' => $saleData['created_by'] ?? null,
            ]
        );

        if (! $pointResult['success']) {
            return [
                'success' => false,
                'error' => $pointResult['error'],
            ];
        }

        // Update redemption status
        $redemption->update(['status' => 'applied']);

        return [
            'success' => true,
            'message' => 'Redemption applied successfully',
            'redemption' => $redemption,
            'point_deduction' => $pointResult,
        ];
    }

    /**
     * Cancel redemption
     *
     * @param  LoyaltyRedemption  $redemption
     * @return array
     */
    public function cancelRedemption($redemption)
    {
        // Check if can be cancelled
        if ($redemption->isCancelled()) {
            return [
                'success' => false,
                'error' => 'Redemption is already cancelled',
            ];
        }

        // If redemption was applied, reverse the point deduction
        if ($redemption->isApplied()) {
            $customer = $redemption->customer;

            // Restore points
            $customer->total_points += $redemption->points_used;
            $customer->redeemed_points -= $redemption->points_used;
            $customer->save();

            // Create reversal transaction
            \Modules\Membership\app\Models\LoyaltyTransaction::create([
                'loyalty_customer_id' => $customer->id,
                'warehouse_id' => null,
                'transaction_type' => 'adjust',
                'points_amount' => $redemption->points_used,
                'points_balance_before' => $customer->total_points - $redemption->points_used,
                'points_balance_after' => $customer->total_points,
                'source_type' => 'refund',
                'description' => "Redemption cancelled - {$redemption->id}",
            ]);
        }

        // Update status
        $redemption->update(['status' => 'cancelled']);

        return [
            'success' => true,
            'message' => 'Redemption cancelled successfully',
            'redemption' => $redemption,
        ];
    }

    /**
     * Get redemption eligibility
     *
     * @param  LoyaltyCustomer  $customer
     * @param  LoyaltyProgram  $program
     * @return array
     */
    public function getRedemptionEligibility(LoyaltyCustomer $customer, LoyaltyProgram $program)
    {
        $redemptionRules = $program->redemption_rules ?? [];
        $minPoints = $redemptionRules['min_points'] ?? 0;
        $maxPoints = $redemptionRules['max_per_transaction'] ?? $customer->total_points;

        return [
            'is_eligible' => $customer->isActive() && $customer->total_points >= $minPoints,
            'current_balance' => $customer->total_points,
            'min_required' => $minPoints,
            'max_allowed' => min($maxPoints, $customer->total_points),
            'redemption_type' => $program->redemption_type,
            'rate' => $program->points_per_unit,
            'status' => $customer->status,
        ];
    }

    /**
     * Get available redemption value
     *
     * @param  LoyaltyProgram  $program
     * @param  float  $points
     * @return float
     */
    public function getRedemptionValue(LoyaltyProgram $program, $points)
    {
        return round($points / $program->points_per_unit, 2);
    }

    /**
     * Get redemption history
     *
     * @param  LoyaltyCustomer  $customer
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRedemptionHistory(LoyaltyCustomer $customer, $limit = 20)
    {
        return $customer->redemptions()
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get redemption statistics
     *
     * @param  LoyaltyCustomer  $customer
     * @return array
     */
    public function getRedemptionStats(LoyaltyCustomer $customer)
    {
        $totalRedemptions = $customer->redemptions()->count();
        $appliedRedemptions = $customer->redemptions()->applied()->count();
        $pendingRedemptions = $customer->redemptions()->pending()->count();
        $cancelledRedemptions = $customer->redemptions()->cancelled()->count();

        $redemptionsByType = $customer->redemptions()
            ->selectRaw('redemption_type, COUNT(*) as count')
            ->groupBy('redemption_type')
            ->get()
            ->pluck('count', 'redemption_type');

        return [
            'total' => $totalRedemptions,
            'applied' => $appliedRedemptions,
            'pending' => $pendingRedemptions,
            'cancelled' => $cancelledRedemptions,
            'by_type' => $redemptionsByType,
        ];
    }
}
