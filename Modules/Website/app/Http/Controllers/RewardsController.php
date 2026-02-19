<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Services\LoyaltyRewardService;

class RewardsController extends Controller
{
    protected $rewardService;

    public function __construct(LoyaltyRewardService $rewardService)
    {
        $this->rewardService = $rewardService;
    }

    /**
     * Display rewards page
     */
    public function index()
    {
        return view('website::rewards');
    }

    /**
     * AJAX: Check points balance by phone
     */
    public function checkPoints(Request $request)
    {
        $request->validate(['phone' => 'required|string|min:10']);

        $result = $this->rewardService->getAvailableTiers($request->phone);

        if (!($result['success'] ?? false)) {
            return response()->json(['success' => false, 'message' => $result['error'] ?? __('Not found')], 400);
        }

        return response()->json($result);
    }

    /**
     * AJAX: Redeem points for coupon
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10',
            'tier_index' => 'required|integer|min:0',
        ]);

        $result = $this->rewardService->redeemForCoupon($request->phone, $request->tier_index);

        if (!($result['success'] ?? false)) {
            return response()->json(['success' => false, 'message' => $result['error'] ?? __('Redemption failed')], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'coupon_code' => $result['coupon_code'],
            'discount_amount' => $result['discount_amount'],
            'points_used' => $result['points_used'],
            'remaining_points' => $result['remaining_points'],
        ]);
    }
}
