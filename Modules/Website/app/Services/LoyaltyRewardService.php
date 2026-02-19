<?php

namespace Modules\Website\app\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Membership\app\Models\LoyaltyCustomer;
use Modules\Membership\app\Models\LoyaltyProgram;
use Modules\Membership\app\Models\LoyaltyRedemption;
use Modules\Membership\app\Services\LoyaltyService;
use Modules\Membership\app\Services\PointCalculationService;
use Modules\Website\app\Models\Coupon;

class LoyaltyRewardService
{
    protected $loyaltyService;
    protected $pointCalculationService;

    public function __construct(
        LoyaltyService $loyaltyService,
        PointCalculationService $pointCalculationService
    ) {
        $this->loyaltyService = $loyaltyService;
        $this->pointCalculationService = $pointCalculationService;
    }

    /**
     * Look up customer by phone and return available coupon tiers
     */
    public function getAvailableTiers(string $phone): array
    {
        // Normalize phone
        $phone = preg_replace('/[\s\-]/', '', $phone);

        if (!$phone) {
            return ['success' => false, 'error' => __('Phone number is required.')];
        }

        // Identify customer
        $customer = $this->loyaltyService->identifyCustomer($phone);

        if (!$customer) {
            return ['success' => false, 'error' => __('No loyalty account found for this phone number.')];
        }

        if ($customer->status !== 'active') {
            return ['success' => false, 'error' => __('Your loyalty account is not active.')];
        }

        // Get active loyalty program
        $program = LoyaltyProgram::where('is_active', true)->first();

        if (!$program) {
            return ['success' => false, 'error' => __('No active loyalty program available.')];
        }

        // Get coupon tiers from redemption_rules
        $redemptionRules = $program->redemption_rules ?? [];
        $couponTiers = $redemptionRules['coupon_tiers'] ?? [];

        if (empty($couponTiers)) {
            return ['success' => false, 'error' => __('No reward tiers configured.')];
        }

        // Sort tiers by points_required ascending
        usort($couponTiers, fn($a, $b) => ($a['points_required'] ?? 0) - ($b['points_required'] ?? 0));

        // Mark eligibility for each tier
        $tiers = [];
        foreach ($couponTiers as $index => $tier) {
            $pointsRequired = (int) ($tier['points_required'] ?? 0);
            $discountAmount = (float) ($tier['discount_amount'] ?? 0);
            $tiers[] = [
                'index' => $index,
                'points_required' => $pointsRequired,
                'discount_amount' => $discountAmount,
                'can_redeem' => $customer->total_points >= $pointsRequired,
            ];
        }

        return [
            'success' => true,
            'customer' => [
                'name' => $customer->name ?? __('Customer'),
                'phone' => $customer->phone,
                'total_points' => $customer->total_points,
            ],
            'tiers' => $tiers,
            'program_name' => $program->name,
        ];
    }

    /**
     * Redeem points for a one-time discount coupon
     */
    public function redeemForCoupon(string $phone, int $tierIndex): array
    {
        $phone = preg_replace('/[\s\-]/', '', $phone);

        if (!$phone) {
            return ['success' => false, 'error' => __('Phone number is required.')];
        }

        $customer = $this->loyaltyService->identifyCustomer($phone);

        if (!$customer || $customer->status !== 'active') {
            return ['success' => false, 'error' => __('Invalid or inactive loyalty account.')];
        }

        $program = LoyaltyProgram::where('is_active', true)->first();

        if (!$program) {
            return ['success' => false, 'error' => __('No active loyalty program.')];
        }

        $redemptionRules = $program->redemption_rules ?? [];
        $couponTiers = $redemptionRules['coupon_tiers'] ?? [];
        usort($couponTiers, fn($a, $b) => ($a['points_required'] ?? 0) - ($b['points_required'] ?? 0));

        if (!isset($couponTiers[$tierIndex])) {
            return ['success' => false, 'error' => __('Invalid reward tier.')];
        }

        $tier = $couponTiers[$tierIndex];
        $pointsRequired = (int) ($tier['points_required'] ?? 0);
        $discountAmount = (float) ($tier['discount_amount'] ?? 0);

        if ($pointsRequired <= 0 || $discountAmount <= 0) {
            return ['success' => false, 'error' => __('Invalid tier configuration.')];
        }

        if ($customer->total_points < $pointsRequired) {
            return ['success' => false, 'error' => __('Insufficient points. You need :points points.', ['points' => $pointsRequired])];
        }

        try {
            return DB::transaction(function () use ($customer, $program, $pointsRequired, $discountAmount) {
                // Deduct points
                $redeemResult = $this->pointCalculationService->redeemPoints($customer, $pointsRequired, [
                    'redemption_type' => 'discount',
                    'redemption_value' => $discountAmount,
                    'description' => __('Points redeemed for :amount discount coupon', ['amount' => $discountAmount]),
                ]);

                if (!($redeemResult['success'] ?? false)) {
                    throw new \Exception($redeemResult['error'] ?? 'Failed to deduct points');
                }

                // Create one-time coupon
                $coupon = Coupon::createFromLoyaltyRedemption($customer->id, $discountAmount);

                // Create redemption record
                LoyaltyRedemption::create([
                    'loyalty_customer_id' => $customer->id,
                    'points_used' => $pointsRequired,
                    'redemption_type' => 'discount',
                    'amount_value' => $discountAmount,
                    'status' => 'applied',
                ]);

                return [
                    'success' => true,
                    'message' => __('Points redeemed successfully!'),
                    'coupon' => $coupon,
                    'coupon_code' => $coupon->code,
                    'discount_amount' => $discountAmount,
                    'points_used' => $pointsRequired,
                    'remaining_points' => $customer->total_points,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Loyalty coupon redemption error: ' . $e->getMessage());
            return ['success' => false, 'error' => __('Failed to redeem points. Please try again.')];
        }
    }
}
