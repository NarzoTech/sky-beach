<?php

namespace Modules\POS\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\POS\app\Models\PrintJob;
use Modules\POS\app\Services\EscPosFormatService;
use Modules\Sales\app\Models\ProductSale;

class ProcessNetworkPrintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [5, 15, 30];

    public function __construct(
        protected int $printJobId
    ) {}

    public function handle(): void
    {
        $printJob = PrintJob::with(['printer'])->find($this->printJobId);

        if (!$printJob || !$printJob->printer) {
            Log::warning("ProcessNetworkPrintJob: PrintJob #{$this->printJobId} or printer not found.");
            return;
        }

        if ($printJob->status === PrintJob::STATUS_PRINTED) {
            return;
        }

        $printer = $printJob->printer;

        if (!$printer->isNetworkPrinter()) {
            return;
        }

        $printJob->markAsPrinting();

        try {
            $escPosService = new EscPosFormatService();
            $sale = $this->loadSale($printJob);

            if (!$sale) {
                $printJob->markAsFailed('Sale not found');
                return;
            }

            $this->sendToPrinter($escPosService, $printJob, $sale);

            $printJob->markAsPrinted();
        } catch (\Exception $e) {
            Log::error("ProcessNetworkPrintJob failed: " . $e->getMessage(), [
                'print_job_id' => $this->printJobId,
                'printer' => $printer->name,
                'ip' => $printer->ip_address,
            ]);

            $printJob->markAsFailed($e->getMessage());

            throw $e; // Re-throw so Laravel can retry
        }
    }

    protected function loadSale(PrintJob $printJob): ?\Modules\Sales\app\Models\Sale
    {
        $relations = ['table', 'waiter', 'details.menuItem', 'details.service'];

        if ($printJob->type === PrintJob::TYPE_RECEIPT) {
            $relations[] = 'customer';
            $relations[] = 'payments.account';
        }

        return \Modules\Sales\app\Models\Sale::with($relations)->find($printJob->sale_id);
    }

    protected function sendToPrinter(EscPosFormatService $service, PrintJob $printJob, $sale): void
    {
        $printer = $printJob->printer;
        $meta = $printJob->meta ?? [];

        switch ($printJob->type) {
            case PrintJob::TYPE_NEW_ORDER:
                if ($printer->isKitchen()) {
                    $service->printKitchenTicket($sale, $printer);
                } else {
                    $service->printCashSlip($sale, $printer);
                }
                break;

            case PrintJob::TYPE_UPDATE_ORDER:
                $newItems = $meta['new_items'] ?? [];
                $service->printKitchenUpdate($sale, $newItems, $printer);
                break;

            case PrintJob::TYPE_RECEIPT:
                $service->printReceipt($sale, $printer);
                break;

            case PrintJob::TYPE_VOID:
                $voidType = $meta['void_type'] ?? 'full';

                if ($voidType === 'single' && isset($meta['item_id'])) {
                    $item = ProductSale::with(['menuItem', 'service'])->find($meta['item_id']);
                    if ($item) {
                        $service->printVoidItemTicket($sale, $item, $printer);
                    }
                } elseif ($voidType === 'multiple' && isset($meta['item_ids'])) {
                    $items = ProductSale::with(['menuItem', 'service'])
                        ->whereIn('id', $meta['item_ids'])
                        ->get();
                    if ($items->isNotEmpty()) {
                        $service->printVoidItemsTicket($sale, $items, $printer);
                    }
                } else {
                    $service->printVoidTicket($sale, $printer);
                }
                break;
        }
    }

    public function failed(\Throwable $exception): void
    {
        $printJob = PrintJob::find($this->printJobId);
        if ($printJob) {
            $printJob->markAsFailed('Final failure: ' . $exception->getMessage());
        }

        Log::error("ProcessNetworkPrintJob permanently failed", [
            'print_job_id' => $this->printJobId,
            'error' => $exception->getMessage(),
        ]);
    }
}
