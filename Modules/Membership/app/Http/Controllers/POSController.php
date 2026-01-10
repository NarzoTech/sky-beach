<?php

namespace Modules\Membership\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Membership\app\Services\LoyaltyService;

class POSController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Identify customer by phone number
     * POST /api/v1/membership/identify
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function identifyCustomer(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|string|min:10',
            ]);

            $customer = $this->loyaltyService->identifyCustomer($validated['phone']);

            if (! $customer) {
                return response()->json([
                    'success' => false,
                    'error' => 'Failed to identify customer',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer identified',
                'customer' => [
                    'id' => $customer->id,
                    'phone' => $customer->phone,
                    'name' => $customer->name,
                    'total_points' => $customer->total_points,
                    'status' => $customer->status,
                    'joined_at' => $customer->joined_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Earn points on sale
     * POST /api/v1/membership/earn-points
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function earnPoints(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|string|min:10',
                'warehouse_id' => 'required|integer|exists:warehouses,id',
                'amount' => 'required|numeric|min:0',
                'sale_id' => 'nullable|integer|exists:sales,id',
                'description' => 'nullable|string',
            ]);

            $result = $this->loyaltyService->handleSaleCompletion(
                $validated['phone'],
                $validated['warehouse_id'],
                [
                    'amount' => $validated['amount'],
                    'sale_id' => $validated['sale_id'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'created_by' => auth()->guard('sanctum')->id(),
                ]
            );

            if (! $result['success']) {
                return response()->json($result, 400);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check points and redemption eligibility
     * POST /api/v1/membership/check-redemption
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function checkRedemption(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|string|min:10',
                'warehouse_id' => 'required|integer|exists:warehouses,id',
                'points' => 'nullable|numeric|min:0',
            ]);

            $profile = $this->loyaltyService->getCustomerProfile($validated['phone']);

            if (! $profile) {
                return response()->json([
                    'success' => false,
                    'error' => 'Customer not found',
                ], 404);
            }

            $loyaltyProgram = \Modules\Membership\app\Models\LoyaltyProgram::byWarehouse($validated['warehouse_id'])
                ->active()
                ->first();

            if (! $loyaltyProgram) {
                return response()->json([
                    'success' => false,
                    'error' => 'No active loyalty program',
                ], 400);
            }

            $customer = $this->loyaltyService->getCustomerService()->identifyByPhone($validated['phone']);
            $eligibility = $this->loyaltyService->getRedemptionService()
                ->getRedemptionEligibility($customer, $loyaltyProgram);

            // If points specified, calculate value
            $redemptionValue = null;
            if ($validated['points'] ?? false) {
                $redemptionValue = $this->loyaltyService->getRedemptionService()
                    ->getRedemptionValue($loyaltyProgram, $validated['points']);
            }

            return response()->json([
                'success' => true,
                'customer' => $profile,
                'eligibility' => $eligibility,
                'redemption_value' => $redemptionValue,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Redeem points
     * POST /api/v1/membership/redeem-points
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function redeemPoints(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'phone' => 'required|string|min:10',
                'warehouse_id' => 'required|integer|exists:warehouses,id',
                'points_to_redeem' => 'required|numeric|min:1',
                'redemption_type' => 'required|in:discount,free_item,cashback',
                'menu_item_id' => 'nullable|integer',
                'ingredient_id' => 'nullable|integer',
                'quantity' => 'nullable|integer|min:1',
                'sale_id' => 'nullable|integer|exists:sales,id',
            ]);

            $result = $this->loyaltyService->handleRedemption(
                $validated['phone'],
                $validated['warehouse_id'],
                [
                    'points_to_redeem' => $validated['points_to_redeem'],
                    'redemption_type' => $validated['redemption_type'],
                    'menu_item_id' => $validated['menu_item_id'] ?? null,
                    'ingredient_id' => $validated['ingredient_id'] ?? null,
                    'quantity' => $validated['quantity'] ?? 1,
                    'sale_id' => $validated['sale_id'] ?? null,
                    'created_by' => auth()->guard('sanctum')->id(),
                ]
            );

            if (! $result['success']) {
                return response()->json($result, 400);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer balance
     * GET /api/v1/membership/balance/:phone
     *
     * @param  Request  $request
     * @param  string  $phone
     * @return JsonResponse
     */
    public function getBalance(Request $request, $phone): JsonResponse
    {
        try {
            $balance = $this->loyaltyService->getCustomerBalance($phone);

            if (! $balance) {
                return response()->json([
                    'success' => false,
                    'error' => 'Customer not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'balance' => $balance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get customer profile
     * GET /api/v1/membership/customer/:phone
     *
     * @param  Request  $request
     * @param  string  $phone
     * @return JsonResponse
     */
    public function getCustomerProfile(Request $request, $phone): JsonResponse
    {
        try {
            $profile = $this->loyaltyService->getCustomerProfile($phone);

            if (! $profile) {
                return response()->json([
                    'success' => false,
                    'error' => 'Customer not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'customer' => $profile,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get transaction history
     * GET /api/v1/membership/transactions/:phone
     *
     * @param  Request  $request
     * @param  string  $phone
     * @return JsonResponse
     */
    public function getTransactionHistory(Request $request, $phone): JsonResponse
    {
        try {
            $limit = $request->query('limit', 20);
            $history = $this->loyaltyService->getTransactionHistory($phone, $limit);

            if (! $history) {
                return response()->json([
                    'success' => false,
                    'error' => 'Customer not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
