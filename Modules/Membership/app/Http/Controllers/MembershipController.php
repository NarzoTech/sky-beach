<?php

namespace Modules\Membership\app\Http\Controllers;

use App\Http\Controllers\Controller;
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

        // Points statistics
        $totalPointsEarned = LoyaltyTransaction::earnings()->sum('points_amount');
        $totalPointsRedeemed = LoyaltyTransaction::redemptions()->sum('points_amount');
        $outstandingPoints = LoyaltyCustomer::sum('total_points');

        // Recent transactions
        $recentTransactions = LoyaltyTransaction::with('customer', 'warehouse')
            ->latest()
            ->take(10)
            ->get();

        // Top customers by points
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
}
