<?php

namespace Modules\Membership\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Membership\app\Models\LoyaltyCustomer;
use Modules\Membership\app\Models\LoyaltyProgram;
use Modules\Membership\app\Models\LoyaltyTransaction;

class MembershipController extends Controller
{
    /**
     * Display dashboard overview
     */
    public function index(): View
    {
        checkAdminHasPermissionAndThrowException('membership.view');
        // Get statistics
        $totalPrograms = LoyaltyProgram::count();
        $activePrograms = LoyaltyProgram::active()->count();
        $totalCustomers = LoyaltyCustomer::count();
        $activeCustomers = LoyaltyCustomer::active()->count();

        // Points statistics from transactions (source of truth)
        $totalPointsEarned = LoyaltyTransaction::earnings()->sum('points_amount');
        $totalPointsRedeemed = LoyaltyTransaction::redemptions()->sum('points_amount');
        $outstandingPoints = LoyaltyCustomer::sum('total_points');

        // Recent transactions
        $recentTransactions = LoyaltyTransaction::with('customer', 'warehouse')
            ->latest()
            ->take(10)
            ->get();

        // Top customers by points (recalculated from transactions for accuracy)
        $topCustomers = LoyaltyCustomer::orderByDesc('total_points')
            ->take(10)
            ->get();

        return view('membership::dashboard', [
            'total_programs' => $totalPrograms,
            'active_programs' => $activePrograms,
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'total_points_earned' => $totalPointsEarned,
            'total_points_redeemed' => abs($totalPointsRedeemed),
            'outstanding_points' => $outstandingPoints,
            'recent_transactions' => $recentTransactions,
            'top_customers' => $topCustomers,
        ]);
    }

    /**
     * Recalculate all customer points from transaction history
     */
    public function recalculatePoints(): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('membership.edit');

        $customers = LoyaltyCustomer::all();
        $fixed = 0;

        foreach ($customers as $customer) {
            $earned = LoyaltyTransaction::where('loyalty_customer_id', $customer->id)
                ->where('transaction_type', 'earn')
                ->sum('points_amount');

            $redeemed = LoyaltyTransaction::where('loyalty_customer_id', $customer->id)
                ->where('transaction_type', 'redeem')
                ->sum('points_amount'); // negative values

            $adjusted = LoyaltyTransaction::where('loyalty_customer_id', $customer->id)
                ->where('transaction_type', 'adjust')
                ->sum('points_amount'); // can be positive or negative

            $correctTotal = $earned + $redeemed + $adjusted; // redeemed is already negative
            $correctLifetime = $earned + max(0, $adjusted); // only positive adjustments
            $correctRedeemed = abs($redeemed) + abs(min(0, $adjusted)); // redemptions + negative adjustments

            if (
                abs($customer->total_points - $correctTotal) > 0.01 ||
                abs($customer->lifetime_points - $correctLifetime) > 0.01 ||
                abs($customer->redeemed_points - $correctRedeemed) > 0.01
            ) {
                $customer->update([
                    'total_points' => max(0, $correctTotal),
                    'lifetime_points' => max(0, $correctLifetime),
                    'redeemed_points' => max(0, $correctRedeemed),
                ]);
                $fixed++;
            }
        }

        return redirect()->route('membership.index')
            ->with('success', __('Points recalculated successfully. :count customer(s) updated.', ['count' => $fixed]));
    }
}
