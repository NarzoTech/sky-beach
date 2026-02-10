<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\POS\app\Models\OrderNotification;
use Modules\POS\app\Services\PrintService;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\ProductSale;

class KitchenDisplayController extends Controller
{
    protected $printService;

    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }

    /**
     * Display kitchen orders screen
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('kitchen.view');
        $orders = Sale::where('status', 0) // Processing orders only
            ->whereHas('details', function ($query) {
                $query->where('kitchen_status', '!=', 'served')
                      ->where('is_voided', false);
            })
            ->with(['table', 'waiter', 'details' => function ($query) {
                $query->where('is_voided', false)
                      ->with(['menuItem', 'service']);
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('pos::kitchen.index', compact('orders'));
    }

    /**
     * Get orders for AJAX refresh
     */
    public function getOrders()
    {
        checkAdminHasPermissionAndThrowException('kitchen.view');
        $orders = Sale::where('status', 0)
            ->whereHas('details', function ($query) {
                $query->where('kitchen_status', '!=', 'served')
                      ->where('is_voided', false);
            })
            ->with(['table', 'waiter', 'details' => function ($query) {
                $query->where('is_voided', false)
                      ->with(['menuItem', 'service']);
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($orders);
    }

    /**
     * Update item status
     */
    public function updateItemStatus(Request $request, $itemId)
    {
        checkAdminHasPermissionAndThrowException('kitchen.update_status');
        $item = ProductSale::findOrFail($itemId);

        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served,cancelled',
        ]);

        $item->update([
            'kitchen_status' => $validated['status'],
            'status_updated_at' => now(),
        ]);

        // Send notification to waiter when item is ready
        if ($validated['status'] === 'ready') {
            $sale = $item->sale;
            $itemName = $item->menuItem->name ?? $item->service->name ?? 'Item';
            OrderNotification::notifyItemReady($sale, $itemName);
        }

        // Check if all items are ready
        $this->checkAllItemsReady($item->sale_id);

        return response()->json([
            'success' => true,
            'message' => 'Item status updated.',
            'status' => $validated['status'],
        ]);
    }

    /**
     * Update all items in an order
     */
    public function updateOrderStatus(Request $request, $orderId)
    {
        checkAdminHasPermissionAndThrowException('kitchen.update_status');
        $order = Sale::findOrFail($orderId);

        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,ready,served',
        ]);

        $order->details()
            ->where('is_voided', false)
            ->update([
                'kitchen_status' => $validated['status'],
                'status_updated_at' => now(),
            ]);

        // Notify waiter when order is ready
        if ($validated['status'] === 'ready') {
            OrderNotification::notifyOrderReady($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated to ' . $validated['status'],
        ]);
    }

    /**
     * Mark item as preparing (quick action)
     */
    public function startPreparing($itemId)
    {
        checkAdminHasPermissionAndThrowException('kitchen.update_status');
        $item = ProductSale::findOrFail($itemId);

        $item->update([
            'kitchen_status' => 'preparing',
            'status_updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Started preparing.',
        ]);
    }

    /**
     * Mark item as ready (quick action)
     */
    public function markReady($itemId)
    {
        checkAdminHasPermissionAndThrowException('kitchen.update_status');
        $item = ProductSale::findOrFail($itemId);

        $item->update([
            'kitchen_status' => 'ready',
            'status_updated_at' => now(),
        ]);

        // Notify waiter
        $sale = $item->sale;
        $itemName = $item->menuItem->name ?? $item->service->name ?? 'Item';
        OrderNotification::notifyItemReady($sale, $itemName);

        // Check if all items are ready
        $this->checkAllItemsReady($item->sale_id);

        return response()->json([
            'success' => true,
            'message' => 'Item marked as ready.',
        ]);
    }

    /**
     * Mark item as served (quick action)
     */
    public function markServed($itemId)
    {
        checkAdminHasPermissionAndThrowException('kitchen.update_status');
        $item = ProductSale::findOrFail($itemId);

        $item->update([
            'kitchen_status' => 'served',
            'status_updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item marked as served.',
        ]);
    }

    /**
     * Bump entire order (mark all as ready)
     */
    public function bumpOrder($orderId)
    {
        checkAdminHasPermissionAndThrowException('kitchen.bump_order');
        $order = Sale::findOrFail($orderId);

        $order->details()
            ->where('is_voided', false)
            ->where('kitchen_status', '!=', 'served')
            ->update([
                'kitchen_status' => 'ready',
                'status_updated_at' => now(),
            ]);

        // Notify waiter
        OrderNotification::notifyOrderReady($order);

        return response()->json([
            'success' => true,
            'message' => 'Order bumped - all items ready.',
        ]);
    }

    /**
     * Recall order (bring back to display)
     */
    public function recallOrder($orderId)
    {
        checkAdminHasPermissionAndThrowException('kitchen.update_status');
        $order = Sale::findOrFail($orderId);

        $order->details()
            ->where('is_voided', false)
            ->where('kitchen_status', 'served')
            ->update([
                'kitchen_status' => 'ready',
                'status_updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Order recalled.',
        ]);
    }

    /**
     * Check if all items are ready and notify
     */
    protected function checkAllItemsReady($saleId): void
    {
        $sale = Sale::with('details')->find($saleId);

        if (!$sale) return;

        $pendingItems = $sale->details()
            ->where('is_voided', false)
            ->whereNotIn('kitchen_status', ['ready', 'served'])
            ->count();

        if ($pendingItems === 0) {
            // All items are ready - send full order notification
            OrderNotification::notifyOrderReady($sale);
        }
    }

    /**
     * Get order history (completed orders)
     */
    public function history(Request $request)
    {
        checkAdminHasPermissionAndThrowException('kitchen.view_history');
        $orders = Sale::where('status', 1) // Completed
            ->with(['table', 'waiter', 'details.menuItem', 'details.service'])
            ->whereDate('created_at', today())
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        return view('pos::kitchen.history', compact('orders'));
    }
}
