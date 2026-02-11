<?php

namespace Modules\POS\app\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Modules\POS\app\Jobs\ProcessNetworkPrintJob;
use Modules\POS\app\Models\PosPrinter;
use Modules\POS\app\Models\PrintJob;
use Modules\Sales\app\Models\Sale;

class PrintService
{
    /**
     * Print new order to all configured printers
     */
    public function printNewOrder(Sale $sale): void
    {
        $sale->load(['table', 'waiter', 'details.menuItem', 'details.service', 'customer']);

        // Print to kitchen printers
        $kitchenPrinters = PosPrinter::active()->kitchen()->get();
        foreach ($kitchenPrinters as $printer) {
            $this->createPrintJob($printer, $sale, PrintJob::TYPE_NEW_ORDER, 'kitchen');
        }

        // Print to cash counter printers
        $cashPrinters = PosPrinter::active()->cashCounter()->get();
        foreach ($cashPrinters as $printer) {
            $this->createPrintJob($printer, $sale, PrintJob::TYPE_NEW_ORDER, 'cash');
        }
    }

    /**
     * Print order update (new items only) to kitchen printers
     */
    public function printOrderUpdate(Sale $sale, array $newItems): void
    {
        $sale->load(['table', 'waiter']);

        $kitchenPrinters = PosPrinter::active()->kitchen()->get();
        foreach ($kitchenPrinters as $printer) {
            $content = $this->renderKitchenUpdateTicket($sale, $newItems, $printer);

            $job = PrintJob::create([
                'printer_id' => $printer->id,
                'sale_id' => $sale->id,
                'type' => PrintJob::TYPE_UPDATE_ORDER,
                'content' => $content,
                'meta' => ['new_items' => $newItems],
                'status' => PrintJob::STATUS_PENDING,
            ]);

            $this->dispatchIfNetwork($printer, $job);
        }
    }

    /**
     * Print receipt to cash counter
     */
    public function printReceipt(Sale $sale): void
    {
        $sale->load(['table', 'waiter', 'details.menuItem', 'details.service', 'customer', 'payments']);

        $cashPrinters = PosPrinter::active()->cashCounter()->get();
        foreach ($cashPrinters as $printer) {
            $this->createPrintJob($printer, $sale, PrintJob::TYPE_RECEIPT, 'receipt');
        }
    }

    /**
     * Print void ticket
     */
    public function printVoid(Sale $sale): void
    {
        $sale->load(['table', 'waiter', 'details.menuItem']);

        $kitchenPrinters = PosPrinter::active()->kitchen()->get();
        foreach ($kitchenPrinters as $printer) {
            $content = $this->renderVoidTicket($sale, $printer);

            $job = PrintJob::create([
                'printer_id' => $printer->id,
                'sale_id' => $sale->id,
                'type' => PrintJob::TYPE_VOID,
                'content' => $content,
                'meta' => ['void_type' => 'full'],
                'status' => PrintJob::STATUS_PENDING,
            ]);

            $this->dispatchIfNetwork($printer, $job);
        }
    }

    /**
     * Reprint order to specific printer type
     */
    public function reprint(Sale $sale, string $printerType = 'all'): void
    {
        $sale->load(['table', 'waiter', 'details.menuItem', 'details.service', 'customer', 'payments']);

        if ($printerType === 'all' || $printerType === 'kitchen') {
            $kitchenPrinters = PosPrinter::active()->kitchen()->get();
            foreach ($kitchenPrinters as $printer) {
                $this->createPrintJob($printer, $sale, PrintJob::TYPE_NEW_ORDER, 'kitchen');
            }
        }

        if ($printerType === 'all' || $printerType === 'cash') {
            $cashPrinters = PosPrinter::active()->cashCounter()->get();
            foreach ($cashPrinters as $printer) {
                $this->createPrintJob($printer, $sale, PrintJob::TYPE_RECEIPT, 'receipt');
            }
        }
    }

    /**
     * Create a print job and dispatch to network printer if applicable
     */
    protected function createPrintJob(PosPrinter $printer, Sale $sale, string $type, string $template): void
    {
        $content = $this->renderTemplate($template, $sale, $printer);

        $job = PrintJob::create([
            'printer_id' => $printer->id,
            'sale_id' => $sale->id,
            'type' => $type,
            'content' => $content,
            'status' => PrintJob::STATUS_PENDING,
        ]);

        $this->dispatchIfNetwork($printer, $job);
    }

    /**
     * Dispatch network print job if the printer is a network printer
     */
    protected function dispatchIfNetwork(PosPrinter $printer, PrintJob $job): void
    {
        if ($printer->isNetworkPrinter()) {
            ProcessNetworkPrintJob::dispatch($job->id);
        }
    }

    /**
     * Render print template
     */
    protected function renderTemplate(string $template, Sale $sale, PosPrinter $printer): string
    {
        $viewName = match ($template) {
            'kitchen' => 'pos::print.kitchen-ticket',
            'cash' => 'pos::print.cash-slip',
            'receipt' => 'pos::print.receipt',
            default => 'pos::print.kitchen-ticket',
        };

        return View::make($viewName, [
            'sale' => $sale,
            'printer' => $printer,
            'setting' => cache('setting'),
        ])->render();
    }

    /**
     * Render kitchen update ticket for new items only
     */
    protected function renderKitchenUpdateTicket(Sale $sale, array $newItems, PosPrinter $printer): string
    {
        return View::make('pos::print.kitchen-update', [
            'sale' => $sale,
            'newItems' => $newItems,
            'printer' => $printer,
            'setting' => cache('setting'),
        ])->render();
    }

    /**
     * Render void ticket
     */
    protected function renderVoidTicket(Sale $sale, PosPrinter $printer): string
    {
        return View::make('pos::print.void-ticket', [
            'sale' => $sale,
            'printer' => $printer,
            'setting' => cache('setting'),
        ])->render();
    }

    /**
     * Print void ticket for a single item
     */
    public function printVoidItem(Sale $sale, $item): void
    {
        $kitchenPrinters = PosPrinter::active()->kitchen()->get();
        foreach ($kitchenPrinters as $printer) {
            $content = View::make('pos::print.void-item-ticket', [
                'sale' => $sale,
                'item' => $item,
                'printer' => $printer,
                'setting' => cache('setting'),
            ])->render();

            $job = PrintJob::create([
                'printer_id' => $printer->id,
                'sale_id' => $sale->id,
                'type' => PrintJob::TYPE_VOID,
                'content' => $content,
                'meta' => ['void_type' => 'single', 'item_id' => $item->id],
                'status' => PrintJob::STATUS_PENDING,
            ]);

            $this->dispatchIfNetwork($printer, $job);
        }
    }

    /**
     * Print void ticket for multiple items
     */
    public function printVoidItems(Sale $sale, $items): void
    {
        $kitchenPrinters = PosPrinter::active()->kitchen()->get();
        foreach ($kitchenPrinters as $printer) {
            $content = View::make('pos::print.void-items-ticket', [
                'sale' => $sale,
                'items' => $items,
                'printer' => $printer,
                'setting' => cache('setting'),
            ])->render();

            $job = PrintJob::create([
                'printer_id' => $printer->id,
                'sale_id' => $sale->id,
                'type' => PrintJob::TYPE_VOID,
                'content' => $content,
                'meta' => ['void_type' => 'multiple', 'item_ids' => $items->pluck('id')->toArray()],
                'status' => PrintJob::STATUS_PENDING,
            ]);

            $this->dispatchIfNetwork($printer, $job);
        }
    }

    /**
     * Get pending print jobs
     */
    public function getPendingJobs(): \Illuminate\Database\Eloquent\Collection
    {
        return PrintJob::pending()
            ->with(['printer', 'sale'])
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get print jobs for a specific sale
     */
    public function getJobsForSale(int $saleId): \Illuminate\Database\Eloquent\Collection
    {
        return PrintJob::where('sale_id', $saleId)
            ->with('printer')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Retry failed print job
     */
    public function retryJob(int $jobId): bool
    {
        $job = PrintJob::with('printer')->findOrFail($jobId);

        if ($job->status === PrintJob::STATUS_FAILED) {
            $job->retry();

            // Re-dispatch for network printers
            if ($job->printer && $job->printer->isNetworkPrinter()) {
                ProcessNetworkPrintJob::dispatch($job->id);
            }

            return true;
        }

        return false;
    }

    /**
     * Mark job as printed (called from browser after successful print)
     */
    public function markAsPrinted(int $jobId): void
    {
        $job = PrintJob::findOrFail($jobId);
        $job->markAsPrinted();
    }

    /**
     * Mark job as failed
     */
    public function markAsFailed(int $jobId, string $errorMessage = null): void
    {
        $job = PrintJob::findOrFail($jobId);
        $job->markAsFailed($errorMessage);
    }
}
