<?php

namespace Modules\Membership\app\Services;

use Modules\Membership\app\Models\LoyaltyCustomer;
use Modules\Membership\app\Models\LoyaltyProgram;

class LoyaltyService
{
    protected $customerIdentificationService;
    protected $pointCalculationService;
    protected $redemptionService;
    protected $ruleEngineService;

    public function __construct(
        CustomerIdentificationService $customerIdentificationService,
        PointCalculationService $pointCalculationService,
        RedemptionService $redemptionService,
        RuleEngineService $ruleEngineService
    ) {
        $this->customerIdentificationService = $customerIdentificationService;
        $this->pointCalculationService = $pointCalculationService;
        $this->redemptionService = $redemptionService;
        $this->ruleEngineService = $ruleEngineService;
    }

    /**
     * Main entry point: Identify customer by phone
     *
     * @param  string  $phone
     * @return LoyaltyCustomer|null
     */
    public function identifyCustomer($phone)
    {
        return $this->customerIdentificationService->identifyByPhone($phone);
    }

    /**
     * Handle sale completion - earn points
     *
     * @param  string  $phone
     * @param  int  $warehouseId
     * @param  array  $saleContext
     * @return array
     */
    public function handleSaleCompletion($phone, $warehouseId, $saleContext)
    {
        // Identify customer
        $customer = $this->identifyCustomer($phone);

        if (! $customer) {
            return [
                'success' => false,
                'error' => 'Failed to identify customer',
            ];
        }

        // Check if eligible
        if (! $this->customerIdentificationService->isEligibleToEarn($customer)) {
            return [
                'success' => false,
                'error' => 'Customer not eligible for points',
                'status' => $customer->status,
            ];
        }

        // Get loyalty program
        $program = LoyaltyProgram::byWarehouse($warehouseId)
            ->active()
            ->first();

        if (! $program) {
            return [
                'success' => false,
                'error' => 'No active loyalty program for this warehouse',
            ];
        }

        // Prepare context for rule evaluation
        $context = array_merge($saleContext, [
            'warehouse_id' => $warehouseId,
            'customer_id' => $customer->id,
            'amount' => $saleContext['amount'] ?? 0,
        ]);

        // Earn points
        $result = $this->pointCalculationService->earnPoints($customer, $program, $context);

        return array_merge($result, [
            'customer_id' => $customer->id,
            'customer' => [
                'phone' => $customer->phone,
                'name' => $customer->name,
                'total_points' => $customer->total_points,
                'lifetime_points' => $customer->lifetime_points,
            ],
        ]);
    }

    /**
     * Handle redemption request
     *
     * @param  string  $phone
     * @param  int  $warehouseId
     * @param  array  $redemptionData
     * @return array
     */
    public function handleRedemption($phone, $warehouseId, $redemptionData)
    {
        // Identify customer
        $customer = $this->identifyCustomer($phone);

        if (! $customer) {
            return [
                'success' => false,
                'error' => 'Failed to identify customer',
            ];
        }

        // Get loyalty program
        $program = LoyaltyProgram::byWarehouse($warehouseId)
            ->active()
            ->first();

        if (! $program) {
            return [
                'success' => false,
                'error' => 'No active loyalty program for this warehouse',
            ];
        }

        // Check eligibility
        $eligibility = $this->redemptionService->getRedemptionEligibility($customer, $program);

        if (! $eligibility['is_eligible']) {
            return [
                'success' => false,
                'error' => 'Customer not eligible for redemption',
                'eligibility' => $eligibility,
            ];
        }

        // Create redemption
        $redemptionResult = $this->redemptionService->createRedemption(
            $customer,
            $program,
            array_merge($redemptionData, ['warehouse_id' => $warehouseId])
        );

        if (! $redemptionResult['success']) {
            return $redemptionResult;
        }

        return array_merge($redemptionResult, [
            'customer' => [
                'phone' => $customer->phone,
                'name' => $customer->name,
                'total_points' => $customer->total_points,
            ],
            'eligibility' => $eligibility,
        ]);
    }

    /**
     * Get customer profile
     *
     * @param  string  $phone
     * @return array|null
     */
    public function getCustomerProfile($phone)
    {
        $customer = $this->identifyCustomer($phone);

        if (! $customer) {
            return null;
        }

        $summary = $this->pointCalculationService->getPointsSummary($customer);

        return array_merge([
            'id' => $customer->id,
            'phone' => $customer->phone,
            'name' => $customer->name,
            'email' => $customer->email,
            'status' => $customer->status,
            'joined_at' => $customer->joined_at,
        ], $summary);
    }

    /**
     * Get customer balance
     *
     * @param  string  $phone
     * @return array|null
     */
    public function getCustomerBalance($phone)
    {
        $customer = $this->identifyCustomer($phone);

        if (! $customer) {
            return null;
        }

        return [
            'customer_id' => $customer->id,
            'phone' => $customer->phone,
            'total_points' => $customer->total_points,
            'available_points' => $customer->total_points,
            'lifetime_earned' => $customer->lifetime_points,
            'redeemed' => $customer->redeemed_points,
        ];
    }

    /**
     * Adjust customer points (admin)
     *
     * @param  string  $phone
     * @param  int  $warehouseId
     * @param  float  $adjustment
     * @param  array  $context
     * @return array
     */
    public function adjustCustomerPoints($phone, $warehouseId, $adjustment, $context = [])
    {
        $customer = $this->identifyCustomer($phone);

        if (! $customer) {
            return [
                'success' => false,
                'error' => 'Customer not found',
            ];
        }

        $result = $this->pointCalculationService->adjustPoints(
            $customer,
            $adjustment,
            array_merge($context, ['warehouse_id' => $warehouseId])
        );

        return array_merge($result, [
            'customer' => [
                'phone' => $customer->phone,
                'name' => $customer->name,
                'total_points' => $customer->total_points,
            ],
        ]);
    }

    /**
     * Get transaction history for customer
     *
     * @param  string  $phone
     * @param  int  $limit
     * @return array|null
     */
    public function getTransactionHistory($phone, $limit = 20)
    {
        $customer = $this->identifyCustomer($phone);

        if (! $customer) {
            return null;
        }

        $transactions = $this->pointCalculationService->getTransactionHistory($customer, $limit);

        return [
            'customer' => [
                'phone' => $customer->phone,
                'name' => $customer->name,
            ],
            'transactions' => $transactions->map(fn ($t) => [
                'id' => $t->id,
                'type' => $t->transaction_type,
                'amount' => $t->points_amount,
                'balance_before' => $t->points_balance_before,
                'balance_after' => $t->points_balance_after,
                'description' => $t->description,
                'created_at' => $t->created_at,
            ]),
        ];
    }

    /**
     * Get customer service helper
     *
     * @return CustomerIdentificationService
     */
    public function getCustomerService()
    {
        return $this->customerIdentificationService;
    }

    /**
     * Get point calculation service helper
     *
     * @return PointCalculationService
     */
    public function getPointCalculationService()
    {
        return $this->pointCalculationService;
    }

    /**
     * Get redemption service helper
     *
     * @return RedemptionService
     */
    public function getRedemptionService()
    {
        return $this->redemptionService;
    }

    /**
     * Get rule engine service helper
     *
     * @return RuleEngineService
     */
    public function getRuleEngineService()
    {
        return $this->ruleEngineService;
    }
}
