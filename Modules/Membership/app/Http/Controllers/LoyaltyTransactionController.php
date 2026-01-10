<?php

namespace Modules\Membership\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Membership\app\Models\LoyaltyTransaction;

class LoyaltyTransactionController extends Controller
{
    /**
     * Display audit log of all transactions
     */
    public function index(Request $request): View
    {
        $type = $request->query('type');
        $customerId = $request->query('customer_id');
        $warehouseId = $request->query('warehouse_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $transactions = LoyaltyTransaction::query()
            ->when($type, fn ($q) => $q->where('transaction_type', $type))
            ->when($customerId, fn ($q) => $q->where('loyalty_customer_id', $customerId))
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate))
            ->with('customer', 'warehouse', 'createdBy')
            ->latest()
            ->paginate(20);

        $warehouses = \App\Models\Warehouse::all();

        return view('membership::transactions.index', [
            'transactions' => $transactions,
            'warehouses' => $warehouses,
            'filters' => [
                'type' => $type,
                'customer_id' => $customerId,
                'warehouse_id' => $warehouseId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Display transaction details
     */
    public function show(LoyaltyTransaction $transaction): View
    {
        $transaction->load('customer', 'warehouse', 'createdBy');

        return view('membership::transactions.show', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Export transactions as CSV
     */
    public function export(Request $request)
    {
        $type = $request->query('type');
        $customerId = $request->query('customer_id');
        $warehouseId = $request->query('warehouse_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $transactions = LoyaltyTransaction::query()
            ->when($type, fn ($q) => $q->where('transaction_type', $type))
            ->when($customerId, fn ($q) => $q->where('loyalty_customer_id', $customerId))
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->when($startDate, fn ($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('created_at', '<=', $endDate))
            ->with('customer', 'warehouse', 'createdBy')
            ->latest()
            ->get();

        $filename = 'loyalty_transactions_'.date('Y-m-d_H-i-s').'.csv';
        $handle = fopen('php://memory', 'w');

        // CSV Headers
        fputcsv($handle, [
            'Date',
            'Customer Phone',
            'Customer Name',
            'Transaction Type',
            'Points Amount',
            'Balance Before',
            'Balance After',
            'Source Type',
            'Source ID',
            'Redemption Method',
            'Redemption Value',
            'Description',
            'Created By',
        ]);

        // CSV Data
        foreach ($transactions as $transaction) {
            fputcsv($handle, [
                $transaction->created_at->format('Y-m-d H:i:s'),
                $transaction->customer->phone,
                $transaction->customer->name,
                $transaction->transaction_type,
                $transaction->points_amount,
                $transaction->points_balance_before,
                $transaction->points_balance_after,
                $transaction->source_type,
                $transaction->source_id,
                $transaction->redemption_method,
                $transaction->redemption_value,
                $transaction->description,
                $transaction->createdBy?->name,
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Get transaction statistics
     */
    public function statistics(Request $request): View
    {
        $warehouseId = $request->query('warehouse_id');
        $startDate = $request->query('start_date') ? \Carbon\Carbon::parse($request->query('start_date')) : now()->subMonth();
        $endDate = $request->query('end_date') ? \Carbon\Carbon::parse($request->query('end_date')) : now();

        // Total earnings
        $totalEarned = LoyaltyTransaction::query()
            ->earnings()
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('points_amount');

        // Total redeemed
        $totalRedeemed = LoyaltyTransaction::query()
            ->redemptions()
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('points_amount');

        // Earnings by transaction type
        $earningsByType = LoyaltyTransaction::query()
            ->earnings()
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('source_type, COUNT(*) as count, SUM(points_amount) as total')
            ->groupBy('source_type')
            ->get();

        // Redemptions by type
        $redemptionsByType = LoyaltyTransaction::query()
            ->redemptions()
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('redemption_method, COUNT(*) as count, SUM(ABS(points_amount)) as total')
            ->groupBy('redemption_method')
            ->get();

        // Active customers
        $activeCustomers = LoyaltyTransaction::query()
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('loyalty_customer_id')
            ->count('loyalty_customer_id');

        $warehouses = \App\Models\Warehouse::all();

        return view('membership::transactions.statistics', [
            'total_earned' => $totalEarned,
            'total_redeemed' => abs($totalRedeemed),
            'earnings_by_type' => $earningsByType,
            'redemptions_by_type' => $redemptionsByType,
            'active_customers' => $activeCustomers,
            'warehouses' => $warehouses,
            'filters' => [
                'warehouse_id' => $warehouseId,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
            ],
        ]);
    }
}
