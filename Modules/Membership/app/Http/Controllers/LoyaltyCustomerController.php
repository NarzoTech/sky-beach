<?php

namespace Modules\Membership\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Membership\app\Models\LoyaltyCustomer;
use Modules\Membership\app\Services\PointCalculationService;

class LoyaltyCustomerController extends Controller
{
    protected $pointCalculationService;

    public function __construct(PointCalculationService $pointCalculationService)
    {
        $this->pointCalculationService = $pointCalculationService;
    }

    /**
     * Display a listing of loyalty customers
     */
    public function index(Request $request): View
    {
        checkAdminHasPermissionAndThrowException('membership.view');
        $search = $request->query('search');
        $status = $request->query('status');

        $customers = LoyaltyCustomer::query()
            ->when($search, fn ($q) => $q->where('phone', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return view('membership::customers.index', [
            'customers' => $customers,
            'search' => $search,
            'status' => $status,
        ]);
    }

    /**
     * Display customer details
     */
    public function show(LoyaltyCustomer $customer): View
    {
        checkAdminHasPermissionAndThrowException('membership.view');
        $customer->load('transactions', 'redemptions');
        $summary = $this->pointCalculationService->getPointsSummary($customer);
        $transactionHistory = $this->pointCalculationService->getTransactionHistory($customer, 50);

        return view('membership::customers.show', [
            'customer' => $customer,
            'summary' => $summary,
            'transactions' => $transactionHistory,
        ]);
    }

    /**
     * Adjust customer points
     */
    public function adjustPoints(Request $request, LoyaltyCustomer $customer): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('membership.manage_points');
        $validated = $request->validate([
            'adjustment_amount' => 'required|numeric',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $pointCalculationService = app(PointCalculationService::class);

        $result = $pointCalculationService->adjustPoints(
            $customer,
            $validated['adjustment_amount'],
            [
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]
        );

        if ($result['success']) {
            return redirect()->back()
                ->with('success', 'Points adjusted successfully');
        }

        return redirect()->back()
            ->withErrors(['error' => $result['error']]);
    }

    /**
     * Block customer
     */
    public function block(LoyaltyCustomer $customer): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('membership.edit');
        $customer->update(['status' => 'blocked']);

        return redirect()->back()
            ->with('success', 'Customer blocked successfully');
    }

    /**
     * Unblock customer
     */
    public function unblock(LoyaltyCustomer $customer): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('membership.edit');
        $customer->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Customer unblocked successfully');
    }

    /**
     * Suspend customer
     */
    public function suspend(LoyaltyCustomer $customer): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('membership.edit');
        $customer->update(['status' => 'suspended']);

        return redirect()->back()
            ->with('success', 'Customer suspended successfully');
    }

    /**
     * Resume customer
     */
    public function resume(LoyaltyCustomer $customer): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('membership.edit');
        $customer->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', 'Customer resumed successfully');
    }

    /**
     * Export customers
     */
    public function export(Request $request)
    {
        checkAdminHasPermissionAndThrowException('membership.view');
        $customers = LoyaltyCustomer::query()
            ->when($request->query('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->get();

        $filename = 'loyalty_customers_'.date('Y-m-d_H-i-s').'.csv';
        $handle = fopen('php://memory', 'w');

        // CSV Headers
        fputcsv($handle, ['Phone', 'Name', 'Email', 'Total Points', 'Lifetime Points', 'Status', 'Joined At']);

        // CSV Data
        foreach ($customers as $customer) {
            fputcsv($handle, [
                $customer->phone,
                $customer->name,
                $customer->email,
                $customer->total_points,
                $customer->lifetime_points,
                $customer->status,
                $customer->joined_at,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }
}
