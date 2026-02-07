<?php

namespace Modules\Website\app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Sales\app\Models\Sale;
use Modules\Menu\app\Services\MenuStockService;
use App\Models\Stock;
use App\Models\TaxLedger;

class WebsiteOrderController extends Controller
{
    protected $menuStockService;

    public function __construct(MenuStockService $menuStockService)
    {
        $this->menuStockService = $menuStockService;
    }
    /**
     * Display list of website orders
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'details.menuItem'])
            ->websiteOrders()
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by order type (delivery/take_away)
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->order_type);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by date range
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date . ' 23:59:59']);
        }

        // Search by invoice or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        // Get statistics
        $stats = [
            'total' => Sale::websiteOrders()->count(),
            'pending' => Sale::websiteOrders()->where('status', 'pending')->count(),
            'confirmed' => Sale::websiteOrders()->where('status', 'confirmed')->count(),
            'preparing' => Sale::websiteOrders()->where('status', 'preparing')->count(),
            'completed' => Sale::websiteOrders()->whereIn('status', ['delivered', 'completed'])->count(),
        ];

        return view('website::admin.website-orders.index', compact('orders', 'stats'));
    }

    /**
     * Display order details
     */
    public function show($id)
    {
        $order = Sale::with(['customer', 'details.menuItem', 'details.combo.comboItems.menuItem', 'createdBy'])
            ->websiteOrders()
            ->findOrFail($id);

        return view('website::admin.website-orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled',
            'staff_note' => 'nullable|string|max:500',
        ]);

        $order = Sale::with('details.menuItem')->websiteOrders()->findOrFail($id);
        $previousStatus = $order->status;

        $updateData = [
            'status' => $request->status,
        ];

        if ($request->filled('staff_note')) {
            $updateData['staff_note'] = $request->staff_note;
        }

        // Update payment status if order is delivered/completed
        if (in_array($request->status, ['delivered', 'completed']) && $order->payment_status !== 'paid') {
            $updateData['payment_status'] = 'paid';
        }

        $order->update($updateData);

        // Deduct stock when status changes to "preparing" (only if not already preparing)
        if ($request->status === 'preparing' && $previousStatus !== 'preparing') {
            $this->deductStockForOrder($order);
        }

        // Reverse stock deduction if order is cancelled (and was in preparing or later stage)
        if ($request->status === 'cancelled' && in_array($previousStatus, ['preparing', 'ready'])) {
            $this->reverseStockForOrder($order);
        }

        // Void tax ledger entry if order is cancelled
        if ($request->status === 'cancelled' && $previousStatus !== 'cancelled' && $order->total_tax > 0) {
            try {
                TaxLedger::voidSaleTax($order, 'Order cancelled by admin');
            } catch (\Exception $e) {
                Log::error('Failed to void tax entry for cancelled order ' . $order->invoice . ': ' . $e->getMessage());
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Order status updated successfully.'),
                'status' => $order->status,
                'status_label' => $order->status_label,
                'status_badge_class' => $order->status_badge_class,
            ]);
        }

        return redirect()->back()->with('success', __('Order status updated successfully.'));
    }

    /**
     * Deduct stock for all menu items in the order
     */
    protected function deductStockForOrder(Sale $order)
    {
        // Check if stock was already deducted for this order (e.g., on order placement)
        $existingStockDeduction = Stock::where('sale_id', $order->id)
            ->whereIn('type', ['Website Sale', 'Sale'])
            ->where('out_quantity', '>', 0)
            ->exists();

        if ($existingStockDeduction) {
            Log::info('Stock already deducted for order, skipping', [
                'order_id' => $order->id,
                'invoice' => $order->invoice,
            ]);
            return;
        }

        $warehouseId = $order->warehouse_id ?? 1;

        foreach ($order->details as $detail) {
            if ($detail->menu_item_id) {
                try {
                    $this->menuStockService->deductStockForSale(
                        $detail->menu_item_id,
                        $detail->quantity,
                        $warehouseId,
                        $order->invoice
                    );
                    Log::info('Stock deducted for order', [
                        'order_id' => $order->id,
                        'invoice' => $order->invoice,
                        'menu_item_id' => $detail->menu_item_id,
                        'quantity' => $detail->quantity,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to deduct stock for order', [
                        'order_id' => $order->id,
                        'menu_item_id' => $detail->menu_item_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse stock deduction for cancelled order
     */
    protected function reverseStockForOrder(Sale $order)
    {
        $warehouseId = $order->warehouse_id ?? 1;

        foreach ($order->details as $detail) {
            if ($detail->menu_item_id) {
                try {
                    $this->menuStockService->reverseStockDeduction(
                        $detail->menu_item_id,
                        $detail->quantity,
                        $warehouseId,
                        $order->invoice . '-CANCELLED'
                    );
                    Log::info('Stock reversed for cancelled order', [
                        'order_id' => $order->id,
                        'invoice' => $order->invoice,
                        'menu_item_id' => $detail->menu_item_id,
                        'quantity' => $detail->quantity,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to reverse stock for order', [
                        'order_id' => $order->id,
                        'menu_item_id' => $detail->menu_item_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    /**
     * Print order (printer-friendly view)
     */
    public function printOrder($id)
    {
        $order = Sale::with(['customer', 'details.menuItem'])
            ->websiteOrders()
            ->findOrFail($id);

        return view('website::admin.website-orders.print', compact('order'));
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid,refunded',
        ]);

        $order = Sale::websiteOrders()->findOrFail($id);

        $order->update([
            'payment_status' => $request->payment_status,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Payment status updated successfully.'),
                'payment_status' => $order->payment_status,
            ]);
        }

        return redirect()->back()->with('success', __('Payment status updated successfully.'));
    }

    /**
     * Bulk update order statuses
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:sales,id',
            'status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled',
        ]);

        $newStatus = $request->status;
        $orders = Sale::with('details.menuItem')
            ->websiteOrders()
            ->whereIn('id', $request->order_ids)
            ->get();

        $count = 0;
        foreach ($orders as $order) {
            $previousStatus = $order->status;
            $order->update(['status' => $newStatus]);
            $count++;

            // Deduct stock when status changes to "preparing"
            if ($newStatus === 'preparing' && $previousStatus !== 'preparing') {
                $this->deductStockForOrder($order);
            }

            // Reverse stock deduction if order is cancelled
            if ($newStatus === 'cancelled' && in_array($previousStatus, ['preparing', 'ready'])) {
                $this->reverseStockForOrder($order);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __(':count order(s) updated successfully.', ['count' => $count]),
            ]);
        }

        return redirect()->back()->with('success', __(':count order(s) updated successfully.', ['count' => $count]));
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = Sale::with(['customer', 'details.menuItem'])
            ->websiteOrders()
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('created_at', [$request->from_date, $request->to_date . ' 23:59:59']);
        }

        $orders = $query->get();

        $filename = 'website_orders_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'Order #',
                'Date',
                'Customer',
                'Email',
                'Phone',
                'Order Type',
                'Status',
                'Items',
                'Subtotal',
                'Delivery Fee',
                'Tax',
                'Grand Total',
                'Payment Method',
                'Payment Status',
                'Delivery Address',
            ]);

            foreach ($orders as $order) {
                $notes = json_decode($order->notes ?? '{}', true);
                $items = $order->details->map(fn($d) => ($d->menuItem->name ?? 'Item') . ' x' . $d->quantity)->implode(', ');

                fputcsv($file, [
                    $order->invoice,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $notes['customer_name'] ?? ($order->customer->name ?? 'Guest'),
                    $notes['customer_email'] ?? ($order->customer->email ?? ''),
                    $notes['customer_phone'] ?? $order->delivery_phone,
                    ucfirst(str_replace('_', ' ', $order->order_type)),
                    ucfirst($order->status),
                    $items,
                    $order->total_price,
                    $order->shipping_cost,
                    $order->total_tax,
                    $order->grand_total,
                    is_array($order->payment_method) ? implode(', ', $order->payment_method) : $order->payment_method,
                    ucfirst($order->payment_status),
                    $order->delivery_address,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
