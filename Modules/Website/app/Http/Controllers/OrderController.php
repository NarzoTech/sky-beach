<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Sales\app\Models\Sale;
use Modules\Website\app\Models\WebsiteCart;

class OrderController extends Controller
{
    /**
     * Display user's orders list
     */
    public function myOrders(Request $request)
    {
        $user = Auth::user();

        $query = Sale::with(['details.menuItem'])
            ->where('customer_id', $user->id)
            ->websiteOrders()
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        return view('website::orders.index', compact('orders'));
    }

    /**
     * Display order details
     */
    public function orderDetails($id)
    {
        $user = Auth::user();

        $order = Sale::with(['details.menuItem'])
            ->where('customer_id', $user->id)
            ->websiteOrders()
            ->findOrFail($id);

        return view('website::orders.details', compact('order'));
    }

    /**
     * Display order tracking page
     */
    public function trackOrder($id)
    {
        $user = Auth::user();

        $order = Sale::with(['details.menuItem'])
            ->where('customer_id', $user->id)
            ->websiteOrders()
            ->findOrFail($id);

        return view('website::orders.track', compact('order'));
    }

    /**
     * Request order cancellation
     */
    public function cancelOrder(Request $request, $id)
    {
        $user = Auth::user();

        $order = Sale::where('customer_id', $user->id)
            ->websiteOrders()
            ->findOrFail($id);

        if (!$order->canBeCancelled()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('This order cannot be cancelled at this stage.'),
                ], 400);
            }
            return back()->with('error', __('This order cannot be cancelled at this stage.'));
        }

        $order->update([
            'status' => 'cancelled',
            'notes' => json_encode(array_merge(
                json_decode($order->notes ?? '{}', true) ?: [],
                ['cancelled_at' => now()->toDateTimeString(), 'cancelled_by' => 'customer']
            )),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Order cancelled successfully.'),
            ]);
        }

        return redirect()->route('website.orders.index')
            ->with('success', __('Order cancelled successfully.'));
    }

    /**
     * Reorder - add items from previous order to cart
     */
    public function reorder(Request $request, $id)
    {
        $user = Auth::user();

        $order = Sale::with(['details.menuItem'])
            ->where('customer_id', $user->id)
            ->websiteOrders()
            ->findOrFail($id);

        $addedItems = 0;
        $unavailableItems = [];

        foreach ($order->details as $item) {
            if (!$item->menu_item_id) continue;

            $menuItem = $item->menuItem;

            // Check if item is still available
            if (!$menuItem || $menuItem->status != 1 || !$menuItem->is_available) {
                $unavailableItems[] = $item->menuItem->name ?? 'Unknown Item';
                continue;
            }

            try {
                WebsiteCart::addItem(
                    $item->menu_item_id,
                    $item->quantity,
                    $item->variant_id,
                    $item->addons ?? [],
                    $item->note
                );
                $addedItems++;
            } catch (\Exception $e) {
                $unavailableItems[] = $menuItem->name;
            }
        }

        $message = __(':count item(s) added to cart.', ['count' => $addedItems]);
        if (!empty($unavailableItems)) {
            $message .= ' ' . __('Some items were unavailable: :items', ['items' => implode(', ', $unavailableItems)]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'added_count' => $addedItems,
                'unavailable' => $unavailableItems,
                'cart_count' => WebsiteCart::getCartCount(),
                'cart_total' => WebsiteCart::getCartTotal(),
            ]);
        }

        return redirect()->route('website.cart.index')
            ->with('success', $message);
    }

    /**
     * Get order status for AJAX polling
     */
    public function getOrderStatus($id)
    {
        $user = Auth::user();

        $order = Sale::where('customer_id', $user->id)
            ->websiteOrders()
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'status_badge_class' => $order->status_badge_class,
            'updated_at' => $order->updated_at->toDateTimeString(),
        ]);
    }
}
