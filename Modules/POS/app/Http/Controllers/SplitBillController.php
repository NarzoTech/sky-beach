<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\POS\app\Models\SplitBill;
use Modules\POS\app\Models\SplitBillItem;
use Modules\POS\app\Services\PrintService;
use Modules\Sales\app\Models\Sale;

class SplitBillController extends Controller
{
    protected $printService;

    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }

    /**
     * Show split bill interface for an order
     */
    public function show($orderId)
    {
        $order = Sale::with(['details.menuItem', 'details.service', 'table', 'splitBills.items'])
            ->findOrFail($orderId);

        return view('pos::split-bill.index', compact('order'));
    }

    /**
     * Get order data for split bill
     */
    public function getOrderData($orderId)
    {
        $order = Sale::with([
            'details' => function ($query) {
                $query->where('is_voided', false);
            },
            'details.menuItem',
            'details.service',
            'splitBills.items.productSale',
        ])->findOrFail($orderId);

        return response()->json($order);
    }

    /**
     * Create split bills
     */
    public function createSplits(Request $request, $orderId)
    {
        $validated = $request->validate([
            'splits' => 'required|array|min:2',
            'splits.*.label' => 'required|string|max:50',
            'splits.*.items' => 'required|array|min:1',
            'splits.*.items.*.product_sale_id' => 'required|exists:product_sales,id',
            'splits.*.items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Sale::with('details')->findOrFail($orderId);

        // Validate total quantities don't exceed order quantities
        $itemQuantities = [];
        foreach ($validated['splits'] as $split) {
            foreach ($split['items'] as $item) {
                $psId = $item['product_sale_id'];
                $itemQuantities[$psId] = ($itemQuantities[$psId] ?? 0) + $item['quantity'];
            }
        }

        foreach ($order->details as $detail) {
            if (isset($itemQuantities[$detail->id]) && $itemQuantities[$detail->id] > $detail->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => "Split quantity exceeds available quantity for {$detail->menuItem?->name}",
                ], 400);
            }
        }

        DB::beginTransaction();
        try {
            // Remove existing splits
            SplitBill::where('sale_id', $orderId)->delete();

            $taxRate = $order->tax_rate ?? 0;

            foreach ($validated['splits'] as $index => $splitData) {
                $subtotal = 0;

                $splitBill = SplitBill::create([
                    'sale_id' => $orderId,
                    'label' => $splitData['label'],
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'total' => 0,
                ]);

                foreach ($splitData['items'] as $itemData) {
                    $productSale = $order->details->find($itemData['product_sale_id']);
                    if (!$productSale) continue;

                    $unitPrice = $productSale->sub_total / $productSale->quantity;
                    $amount = $unitPrice * $itemData['quantity'];

                    SplitBillItem::create([
                        'split_bill_id' => $splitBill->id,
                        'product_sale_id' => $itemData['product_sale_id'],
                        'quantity' => $itemData['quantity'],
                        'amount' => $amount,
                    ]);

                    $subtotal += $amount;
                }

                $taxAmount = $subtotal * ($taxRate / 100);

                $splitBill->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total' => $subtotal + $taxAmount,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bill split successfully.',
                'splits' => SplitBill::where('sale_id', $orderId)->with('items.productSale')->get(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to split bill: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Split bill equally
     */
    public function splitEqually(Request $request, $orderId)
    {
        $validated = $request->validate([
            'number_of_splits' => 'required|integer|min:2|max:20',
        ]);

        $order = Sale::with('details')->findOrFail($orderId);
        $numberOfSplits = $validated['number_of_splits'];

        DB::beginTransaction();
        try {
            // Remove existing splits
            SplitBill::where('sale_id', $orderId)->delete();

            $splitAmount = $order->total / $numberOfSplits;
            $taxSplit = ($order->tax_amount ?? 0) / $numberOfSplits;
            $subtotalSplit = $order->subtotal / $numberOfSplits;

            for ($i = 1; $i <= $numberOfSplits; $i++) {
                SplitBill::create([
                    'sale_id' => $orderId,
                    'label' => "Guest {$i}",
                    'subtotal' => $subtotalSplit,
                    'tax_amount' => $taxSplit,
                    'total' => $splitAmount,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Bill split equally into {$numberOfSplits} parts.",
                'splits' => SplitBill::where('sale_id', $orderId)->get(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to split bill: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process payment for a split
     */
    public function processPayment(Request $request, $splitId)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        $split = SplitBill::with('sale')->findOrFail($splitId);

        if ($split->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'This split is already paid.',
            ], 400);
        }

        $split->processPayment($validated['amount'], $validated['payment_method']);

        // Check if all splits are paid
        $this->checkAllSplitsPaid($split->sale_id);

        return response()->json([
            'success' => true,
            'message' => 'Payment processed.',
            'split' => $split->fresh(),
        ]);
    }

    /**
     * Print split receipt
     */
    public function printSplitReceipt($splitId)
    {
        $split = SplitBill::with(['sale.table', 'sale.waiter', 'items.productSale.menuItem'])
            ->findOrFail($splitId);

        return view('pos::print.split-receipt', [
            'split' => $split,
            'setting' => cache('setting'),
        ]);
    }

    /**
     * Check if all splits are paid and complete order
     */
    protected function checkAllSplitsPaid($saleId): void
    {
        $unpaidSplits = SplitBill::where('sale_id', $saleId)
            ->where('payment_status', '!=', 'paid')
            ->count();

        if ($unpaidSplits === 0) {
            $order = Sale::find($saleId);
            if ($order) {
                $order->update([
                    'payment_status' => 1,
                    'status' => 1, // Completed
                ]);

                // Release table
                if ($order->table) {
                    $order->table->release($order->guest_count);
                }
            }
        }
    }

    /**
     * Remove split and restore to single bill
     */
    public function removeSplits($orderId)
    {
        SplitBill::where('sale_id', $orderId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Splits removed. Order is now a single bill.',
        ]);
    }
}
