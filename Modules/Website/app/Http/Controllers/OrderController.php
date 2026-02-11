<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Sales\app\Models\Sale;
use Modules\Website\app\Models\WebsiteCart;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\Combo;
use App\Models\Stock;

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

        // Restore stock for cancelled order
        $this->restoreStockForCancelledOrder($order);


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

    /**
     * Restore stock for cancelled order
     */
    private function restoreStockForCancelledOrder(Sale $order)
    {
        try {
            $order->load(['details.menuItem.recipes.ingredient', 'details.combo.comboItems.menuItem.recipes.ingredient']);

            foreach ($order->details as $detail) {
                if ($detail->combo_id && $detail->combo) {
                    // Handle combo - restore stock for each menu item in the combo
                    foreach ($detail->combo->comboItems as $comboItem) {
                        if ($comboItem->menuItem) {
                            $totalQuantity = $comboItem->quantity * $detail->quantity;
                            $this->restoreIngredientStockFromRecipe($comboItem->menuItem, $totalQuantity, $order);
                        }
                    }
                } else if ($detail->menu_item_id && $detail->menuItem) {
                    // Handle regular menu item
                    $this->restoreIngredientStockFromRecipe($detail->menuItem, $detail->quantity, $order);
                }
            }
        } catch (\Exception $e) {
            Log::error('Stock restoration error for cancelled order ' . $order->invoice . ': ' . $e->getMessage());
        }
    }

    /**
     * Restore ingredient stock based on menu item recipe (for order cancellation)
     */
    private function restoreIngredientStockFromRecipe(MenuItem $menuItem, $quantity, Sale $order)
    {
        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            if (!$ingredient) continue;

            // Calculate quantity to restore
            $restoreQuantity = $recipe->quantity_required * $quantity;

            // Convert to purchase unit
            $conversionRate = $ingredient->conversion_rate ?? 1;
            $restoreInPurchaseUnit = $restoreQuantity / $conversionRate;

            // Update ingredient stock using safe method (handles number_format, negative stock, low_stock)
            $ingredient->addStock($restoreQuantity, $ingredient->consumption_unit_id);

            // Create stock record for tracking (reversal)
            $stockData = [
                'sale_id' => $order->id,
                'ingredient_id' => $ingredient->id,
                'unit_id' => $ingredient->consumption_unit_id,
                'date' => now(),
                'type' => 'Website Sale Reversal',
                'invoice' => $order->invoice,
                'in_quantity' => $restoreQuantity,
                'base_in_quantity' => $restoreInPurchaseUnit,
                'out_quantity' => 0,
                'base_out_quantity' => 0,
                'sku' => $ingredient->sku,
                'purchase_price' => $ingredient->purchase_price ?? 0,
                'average_cost' => $ingredient->average_cost ?? 0,
                'sale_price' => 0,
                'rate' => $ingredient->consumption_unit_cost ?? 0,
                'profit' => 0,
                'created_by' => 1, // System user
            ];

            // Only add warehouse_id if it exists
            if ($order->warehouse_id && \App\Models\Warehouse::find($order->warehouse_id)) {
                $stockData['warehouse_id'] = $order->warehouse_id;
            }

            Stock::create($stockData);
        }
    }
}
