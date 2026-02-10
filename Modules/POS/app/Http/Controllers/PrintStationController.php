<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\POS\app\Models\PosPrinter;
use Modules\POS\app\Models\PrintJob;

class PrintStationController extends Controller
{
    /**
     * Display the print station page
     * This page stays open and automatically prints incoming jobs
     */
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('printer.view');
        $printers = PosPrinter::active()->browser()->get();
        $selectedPrinterId = $request->get('printer_id');

        return view('pos::print-station.index', compact('printers', 'selectedPrinterId'));
    }

    /**
     * Get pending print jobs for browser printing
     */
    public function getPendingJobs(Request $request)
    {
        checkAdminHasPermissionAndThrowException('printer.view');
        $printerId = $request->get('printer_id');

        $query = PrintJob::with(['printer', 'sale.table', 'sale.waiter'])
            ->where('status', PrintJob::STATUS_PENDING)
            ->whereHas('printer', function ($q) {
                $q->where('connection_type', 'browser')->where('is_active', true);
            });

        if ($printerId) {
            $query->where('printer_id', $printerId);
        }

        $jobs = $query->orderBy('created_at', 'asc')->limit(10)->get();

        return response()->json([
            'success' => true,
            'jobs' => $jobs->map(function ($job) {
                return [
                    'id' => $job->id,
                    'type' => $job->type,
                    'printer_id' => $job->printer_id,
                    'printer_name' => $job->printer->name ?? 'Unknown',
                    'printer_type' => $job->printer->type ?? 'unknown',
                    'paper_width' => $job->printer->paper_width ?? 80,
                    'sale_id' => $job->sale_id,
                    'invoice' => $job->sale->invoice ?? '',
                    'table_name' => $job->sale->table->name ?? '',
                    'created_at' => $job->created_at->diffForHumans(),
                ];
            }),
            'count' => $jobs->count(),
        ]);
    }

    /**
     * Get print job content for rendering
     */
    public function getJobContent($id)
    {
        checkAdminHasPermissionAndThrowException('printer.view');
        $job = PrintJob::with('printer')->findOrFail($id);

        // Mark as printing
        $job->markAsPrinting();

        return response($job->content)
            ->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Mark job as printed (called after browser print completes)
     */
    public function markPrinted($id)
    {
        checkAdminHasPermissionAndThrowException('printer.view');
        $job = PrintJob::findOrFail($id);
        $job->markAsPrinted();

        return response()->json([
            'success' => true,
            'message' => 'Print job marked as completed.',
        ]);
    }

    /**
     * Mark job as failed
     */
    public function markFailed(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('printer.view');
        $job = PrintJob::findOrFail($id);
        $job->markAsFailed($request->get('error', 'Print failed'));

        return response()->json([
            'success' => true,
            'message' => 'Print job marked as failed.',
        ]);
    }

    /**
     * Retry a failed job
     */
    public function retryJob($id)
    {
        checkAdminHasPermissionAndThrowException('printer.view');
        $job = PrintJob::findOrFail($id);

        if ($job->status === PrintJob::STATUS_FAILED) {
            $job->retry();
            return response()->json([
                'success' => true,
                'message' => 'Print job requeued.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Job is not in failed state.',
        ], 400);
    }

    /**
     * Get print statistics
     */
    public function getStats()
    {
        checkAdminHasPermissionAndThrowException('printer.view');
        $stats = [
            'pending' => PrintJob::where('status', PrintJob::STATUS_PENDING)->count(),
            'printing' => PrintJob::where('status', PrintJob::STATUS_PRINTING)->count(),
            'printed_today' => PrintJob::where('status', PrintJob::STATUS_PRINTED)
                ->whereDate('printed_at', today())
                ->count(),
            'failed' => PrintJob::where('status', PrintJob::STATUS_FAILED)->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get failed jobs for review
     */
    public function getFailedJobs()
    {
        checkAdminHasPermissionAndThrowException('printer.view');
        $jobs = PrintJob::with(['printer', 'sale'])
            ->where('status', PrintJob::STATUS_FAILED)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'jobs' => $jobs,
        ]);
    }

    /**
     * Clear all printed jobs older than specified hours
     */
    public function clearOldJobs(Request $request)
    {
        checkAdminHasPermissionAndThrowException('printer.view');
        $hours = $request->get('hours', 24);

        $deleted = PrintJob::where('status', PrintJob::STATUS_PRINTED)
            ->where('printed_at', '<', now()->subHours($hours))
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$deleted} old print jobs.",
            'deleted_count' => $deleted,
        ]);
    }
}
