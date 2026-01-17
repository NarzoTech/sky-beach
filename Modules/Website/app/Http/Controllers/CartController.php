<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\WebsiteCart;
use Modules\Menu\app\Models\MenuItem;

class CartController extends Controller
{
    /**
     * Display cart page
     */
    public function index()
    {
        $cartItems = WebsiteCart::getCart();
        $cartTotal = $cartItems->sum('subtotal');
        $cartCount = $cartItems->sum('quantity');

        return view('website::cart_view', compact('cartItems', 'cartTotal', 'cartCount'));
    }

    /**
     * Get cart items as JSON (AJAX)
     */
    public function getCart()
    {
        $cartItems = WebsiteCart::getCart();

        return response()->json([
            'success' => true,
            'items' => $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'menu_item_id' => $item->menu_item_id,
                    'name' => $item->menuItem->name ?? 'Unknown Item',
                    'image' => $item->menuItem->image ?? null,
                    'variant' => $item->variant_name,
                    'addons' => $item->addon_names,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                    'special_instructions' => $item->special_instructions,
                ];
            }),
            'total' => $cartItems->sum('subtotal'),
            'count' => $cartItems->sum('quantity'),
        ]);
    }

    /**
     * Get cart count (AJAX)
     */
    public function getCartCount()
    {
        return response()->json([
            'success' => true,
            'count' => WebsiteCart::getCartCount(),
        ]);
    }

    /**
     * Add item to cart (AJAX)
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:99',
            'variant_id' => 'nullable|exists:menu_variants,id',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:menu_addons,id',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        $menuItem = MenuItem::find($request->menu_item_id);

        if (!$menuItem || $menuItem->status != 1 || !$menuItem->is_available) {
            return response()->json([
                'success' => false,
                'message' => __('This item is currently unavailable.'),
            ], 400);
        }

        try {
            $cartItem = WebsiteCart::addItem(
                $request->menu_item_id,
                $request->quantity,
                $request->variant_id,
                $request->addons ?? [],
                $request->special_instructions
            );

            return response()->json([
                'success' => true,
                'message' => __('Item added to cart successfully.'),
                'cart_count' => WebsiteCart::getCartCount(),
                'cart_total' => WebsiteCart::getCartTotal(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to add item to cart.'),
            ], 500);
        }
    }

    /**
     * Update cart item quantity (AJAX)
     */
    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        try {
            $cartItem = WebsiteCart::updateItemQuantity($id, $request->quantity);

            return response()->json([
                'success' => true,
                'message' => $request->quantity > 0 ? __('Cart updated.') : __('Item removed.'),
                'item' => $cartItem ? [
                    'id' => $cartItem->id,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->subtotal,
                ] : null,
                'cart_count' => WebsiteCart::getCartCount(),
                'cart_total' => WebsiteCart::getCartTotal(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update cart.'),
            ], 500);
        }
    }

    /**
     * Remove item from cart (AJAX)
     */
    public function removeItem($id)
    {
        try {
            WebsiteCart::removeItem($id);

            return response()->json([
                'success' => true,
                'message' => __('Item removed from cart.'),
                'cart_count' => WebsiteCart::getCartCount(),
                'cart_total' => WebsiteCart::getCartTotal(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to remove item.'),
            ], 500);
        }
    }

    /**
     * Clear entire cart (AJAX)
     */
    public function clearCart()
    {
        try {
            WebsiteCart::clearCart();

            return response()->json([
                'success' => true,
                'message' => __('Cart cleared successfully.'),
                'cart_count' => 0,
                'cart_total' => 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to clear cart.'),
            ], 500);
        }
    }

    /**
     * Apply coupon code (AJAX)
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        // TODO: Implement coupon validation in Phase 7
        // For now, return a placeholder response

        return response()->json([
            'success' => false,
            'message' => __('Coupon feature coming soon.'),
        ]);
    }
}
