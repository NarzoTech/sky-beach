<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\POS\app\Models\PosPrinter;
use Modules\POS\app\Services\EscPosFormatService;

class PrinterController extends Controller
{
    /**
     * Display a listing of printers
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('printer.view');

        $printers = PosPrinter::latest()->get();

        return view('pos::printers.index', compact('printers'));
    }

    /**
     * Show the form for creating a new printer
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('printer.create');

        return view('pos::printers.create');
    }

    /**
     * Store a newly created printer
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('printer.create');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash_counter,kitchen',
            'connection_type' => 'required|in:network,windows,linux,usb,bluetooth,browser',
            'capability_profile' => 'required|in:default,simple,SP2000,TEP-200M,P822D',
            'char_per_line' => 'required|integer|min:1|max:200',
            'ip_address' => 'nullable|string|max:255',
            'port' => 'nullable|integer',
            'path' => 'nullable|string|max:255',
            'paper_width' => 'required|integer|in:58,80',
            'is_active' => 'boolean',
            'print_categories' => 'nullable|array',
            'location_name' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        PosPrinter::create($validated);

        return redirect()->route('admin.pos.printers.index')
            ->with('success', 'Printer created successfully.');
    }

    /**
     * Show the form for editing the specified printer
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('printer.edit');

        $printer = PosPrinter::findOrFail($id);

        return view('pos::printers.edit', compact('printer'));
    }

    /**
     * Update the specified printer
     */
    public function update(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('printer.edit');

        $printer = PosPrinter::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash_counter,kitchen',
            'connection_type' => 'required|in:network,windows,linux,usb,bluetooth,browser',
            'capability_profile' => 'required|in:default,simple,SP2000,TEP-200M,P822D',
            'char_per_line' => 'required|integer|min:1|max:200',
            'ip_address' => 'nullable|string|max:255',
            'port' => 'nullable|integer',
            'path' => 'nullable|string|max:255',
            'paper_width' => 'required|integer|in:58,80',
            'is_active' => 'boolean',
            'print_categories' => 'nullable|array',
            'location_name' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $printer->update($validated);

        return redirect()->route('admin.pos.printers.index')
            ->with('success', 'Printer updated successfully.');
    }

    /**
     * Remove the specified printer
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('printer.delete');

        $printer = PosPrinter::findOrFail($id);
        $printer->delete();

        return redirect()->route('admin.pos.printers.index')
            ->with('success', 'Printer deleted successfully.');
    }

    /**
     * Toggle printer status
     */
    public function toggleStatus($id)
    {
        checkAdminHasPermissionAndThrowException('printer.edit');

        $printer = PosPrinter::findOrFail($id);
        $printer->update(['is_active' => !$printer->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $printer->is_active,
            'message' => 'Printer status updated.',
        ]);
    }

    /**
     * Test printer connection
     */
    public function test($id)
    {
        checkAdminHasPermissionAndThrowException('printer.test');

        $printer = PosPrinter::findOrFail($id);

        // For browser printing, return test content
        if ($printer->usesBrowserPrinting()) {
            return view('pos::printers.test-print', compact('printer'));
        }

        // For network printers, attempt actual connection test
        if ($printer->isNetworkPrinter()) {
            try {
                $escPosService = new EscPosFormatService();
                $escPosService->printTestPage($printer);

                return redirect()->route('admin.pos.printers.index')
                    ->with('success', __('Test print sent successfully to') . ' ' . $printer->name);
            } catch (\Exception $e) {
                return redirect()->route('admin.pos.printers.index')
                    ->with('error', __('Failed to connect to printer') . ': ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.pos.printers.index')
            ->with('error', __('Printer connection type not supported for testing'));
    }

    /**
     * Get printers for AJAX requests
     */
    public function getPrinters(Request $request)
    {
        $type = $request->get('type');

        $query = PosPrinter::active();

        if ($type) {
            $query->where('type', $type);
        }

        return response()->json($query->get());
    }

    /**
     * Get pending print jobs
     */
    public function getPendingJobs()
    {
        $jobs = \Modules\POS\app\Models\PrintJob::pending()
            ->with(['printer', 'sale'])
            ->orderBy('created_at')
            ->get();

        return response()->json($jobs);
    }

    /**
     * Get print job content
     */
    public function getJobContent($id)
    {
        $job = \Modules\POS\app\Models\PrintJob::findOrFail($id);

        return response($job->content)->header('Content-Type', 'text/html');
    }

    /**
     * Mark print job as printed
     */
    public function markAsPrinted($id)
    {
        $job = \Modules\POS\app\Models\PrintJob::findOrFail($id);
        $job->markAsPrinted();

        return response()->json(['success' => true, 'message' => 'Job marked as printed.']);
    }

    /**
     * Mark print job as failed
     */
    public function markAsFailed(Request $request, $id)
    {
        $job = \Modules\POS\app\Models\PrintJob::findOrFail($id);
        $job->markAsFailed($request->get('error_message'));

        return response()->json(['success' => true, 'message' => 'Job marked as failed.']);
    }

    /**
     * Retry failed print job
     */
    public function retryJob($id)
    {
        $job = \Modules\POS\app\Models\PrintJob::with('printer')->findOrFail($id);

        if ($job->status === \Modules\POS\app\Models\PrintJob::STATUS_FAILED) {
            $job->retry();

            // Re-dispatch for network printers
            if ($job->printer && $job->printer->isNetworkPrinter()) {
                \Modules\POS\app\Jobs\ProcessNetworkPrintJob::dispatch($job->id);
            }

            return response()->json(['success' => true, 'message' => 'Job requeued for printing.']);
        }

        return response()->json(['success' => false, 'message' => 'Job is not in failed state.'], 400);
    }
}
