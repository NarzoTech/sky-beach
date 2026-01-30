<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxLedger;
use App\Models\TaxPeriodSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Sales\app\Models\Sale;

class TaxReportController extends Controller
{
    /**
     * Display tax dashboard/summary
     */
    public function index(Request $request)
    {
        // Default to current month
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : now()->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : now()->endOfMonth();

        // Get current period summary
        $currentSummary = TaxLedger::getTaxSummaryForPeriod($startDate, $endDate);

        // Get recent tax entries
        $recentEntries = TaxLedger::with(['sale', 'createdBy'])
            ->active()
            ->orderBy('transaction_date', 'desc')
            ->limit(20)
            ->get();

        // Get period summaries for the last 12 months
        $periodSummaries = [];
        for ($i = 0; $i < 12; $i++) {
            $periodStart = now()->subMonths($i)->startOfMonth();
            $periodEnd = now()->subMonths($i)->endOfMonth();
            $periodSummaries[] = TaxLedger::getTaxSummaryForPeriod($periodStart, $periodEnd);
        }
        $periodSummaries = array_reverse($periodSummaries);

        // Get yearly totals
        $yearStart = now()->startOfYear();
        $yearEnd = now()->endOfYear();
        $yearlySummary = TaxLedger::getTaxSummaryForPeriod($yearStart, $yearEnd);

        return view('admin.tax-reports.index', compact(
            'currentSummary',
            'recentEntries',
            'periodSummaries',
            'yearlySummary',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display detailed tax ledger
     */
    public function ledger(Request $request)
    {
        $query = TaxLedger::with(['sale', 'purchase', 'tax', 'createdBy']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->active();
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->forPeriod($request->start_date, $request->end_date);
        }

        // Filter by reference number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderBy('transaction_date', 'desc')
            ->paginate(50)
            ->withQueryString();

        // Get summary for filtered results
        $filteredSummary = [
            'total_collected' => (clone $query)->collected()->sum('tax_amount'),
            'total_paid' => (clone $query)->paid()->sum('tax_amount'),
            'total_entries' => (clone $query)->count(),
        ];

        return view('admin.tax-reports.ledger', compact('entries', 'filteredSummary'));
    }

    /**
     * Display period management
     */
    public function periods(Request $request)
    {
        $periods = TaxPeriodSummary::orderBy('period_start', 'desc')
            ->paginate(12);

        return view('admin.tax-reports.periods', compact('periods'));
    }

    /**
     * Generate/update period summary
     */
    public function generatePeriod(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $period = TaxPeriodSummary::getOrCreateForPeriod(
            $request->period_start,
            $request->period_end
        );

        $period->recalculate();

        return redirect()->route('admin.tax-reports.periods')
            ->with('success', __('Tax period summary generated successfully.'));
    }

    /**
     * Close a tax period
     */
    public function closePeriod(Request $request, $id)
    {
        $period = TaxPeriodSummary::findOrFail($id);

        if (!$period->close($request->notes)) {
            return back()->with('error', __('Cannot close this period. It may already be closed.'));
        }

        return back()->with('success', __('Tax period closed successfully.'));
    }

    /**
     * Mark period as filed
     */
    public function markFiled(Request $request, $id)
    {
        $period = TaxPeriodSummary::findOrFail($id);

        if (!$period->markAsFiled($request->notes)) {
            return back()->with('error', __('Cannot mark as filed. Period must be closed first.'));
        }

        return back()->with('success', __('Tax period marked as filed.'));
    }

    /**
     * Export tax report to CSV
     */
    public function export(Request $request)
    {
        $query = TaxLedger::with(['sale', 'purchase', 'tax'])
            ->active()
            ->orderBy('transaction_date', 'asc');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->forPeriod($request->start_date, $request->end_date);
        }

        $entries = $query->get();

        $filename = 'tax_report_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($entries) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Date',
                'Reference Type',
                'Reference Number',
                'Tax Type',
                'Tax Name',
                'Tax Rate (%)',
                'Taxable Amount',
                'Tax Amount',
                'Type (Collected/Paid)',
                'Status',
                'Description',
            ]);

            foreach ($entries as $entry) {
                fputcsv($file, [
                    $entry->transaction_date->format('Y-m-d'),
                    ucfirst($entry->reference_type),
                    $entry->reference_number,
                    $entry->type === 'collected' ? 'Output Tax' : 'Input Tax',
                    $entry->tax_name,
                    $entry->tax_rate,
                    number_format($entry->taxable_amount, 2),
                    number_format($entry->tax_amount, 2),
                    ucfirst($entry->type),
                    ucfirst($entry->status),
                    $entry->description,
                ]);
            }

            // Add summary row
            fputcsv($file, []);
            fputcsv($file, ['SUMMARY']);

            $collected = $entries->where('type', 'collected')->sum('tax_amount');
            $paid = $entries->where('type', 'paid')->sum('tax_amount');

            fputcsv($file, ['Total Tax Collected (Output)', '', '', '', '', '', '', number_format($collected, 2)]);
            fputcsv($file, ['Total Tax Paid (Input)', '', '', '', '', '', '', number_format($paid, 2)]);
            fputcsv($file, ['Net Tax Payable', '', '', '', '', '', '', number_format($collected - $paid, 2)]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sync existing sales to tax ledger (one-time migration)
     */
    public function syncSales(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)
            : now()->subMonths(3)->startOfMonth();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)
            : now()->endOfMonth();

        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->where('total_tax', '>', 0)
            ->whereNotIn('status', ['cancelled', 'voided'])
            ->get();

        $synced = 0;
        $skipped = 0;

        foreach ($sales as $sale) {
            // Check if already recorded
            $exists = TaxLedger::where('sale_id', $sale->id)
                ->where('status', 'active')
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            TaxLedger::recordSaleTax($sale);
            $synced++;
        }

        return back()->with('success', __(':synced sales synced to tax ledger. :skipped already existed.', [
            'synced' => $synced,
            'skipped' => $skipped,
        ]));
    }

    /**
     * Void a tax entry (for corrections)
     */
    public function voidEntry(Request $request, $id)
    {
        $entry = TaxLedger::findOrFail($id);

        if ($entry->status !== 'active') {
            return back()->with('error', __('This entry is already voided or adjusted.'));
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $entry->update([
            'status' => 'voided',
            'voided_by' => auth()->id(),
            'voided_at' => now(),
            'void_reason' => $request->reason,
        ]);

        return back()->with('success', __('Tax entry voided successfully.'));
    }

    /**
     * Create adjustment entry
     */
    public function createAdjustment(Request $request)
    {
        $request->validate([
            'type' => 'required|in:collected,paid',
            'tax_amount' => 'required|numeric|min:0.01',
            'taxable_amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'required|string|max:500',
        ]);

        $transactionDate = Carbon::parse($request->transaction_date);

        TaxLedger::create([
            'tax_name' => 'Adjustment',
            'tax_rate' => $request->taxable_amount > 0
                ? ($request->tax_amount / $request->taxable_amount) * 100
                : 0,
            'type' => $request->type,
            'reference_type' => 'adjustment',
            'reference_number' => 'ADJ-' . now()->format('YmdHis'),
            'taxable_amount' => $request->taxable_amount,
            'tax_amount' => $request->type === 'collected'
                ? $request->tax_amount
                : -$request->tax_amount,
            'transaction_date' => $transactionDate->toDateString(),
            'period_start' => $transactionDate->startOfMonth()->toDateString(),
            'period_end' => $transactionDate->endOfMonth()->toDateString(),
            'description' => $request->description,
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', __('Tax adjustment created successfully.'));
    }
}
