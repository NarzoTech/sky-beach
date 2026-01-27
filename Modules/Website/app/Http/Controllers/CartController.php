<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Website\app\Models\WebsiteCart;
use Modules\Website\app\Models\Coupon;
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

            // Load the menu item relationship for cart item data
            $cartItem->load('menuItem');

            // Prepare cart item data for mini cart update
            $cartItemData = [
                'id' => $cartItem->id,
                'name' => $cartItem->menuItem->name ?? 'Item',
                'image' => $cartItem->menuItem && $cartItem->menuItem->image
                    ? asset('storage/' . $cartItem->menuItem->image)
                    : null,
                'unit_price' => $cartItem->unit_price,
                'quantity' => $cartItem->quantity,
                'variant_name' => $cartItem->variant_name,
            ];

            return response()->json([
                'success' => true,
                'message' => __('Item added to cart'),
                'cart_count' => WebsiteCart::getCartCount(),
                'cart_total' => WebsiteCart::getCartTotal(),
                'cart_item' => $cartItemData,
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

        $code = strtoupper(trim($request->code));

        // Find the coupon
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid coupon code.'),
            ], 400);
        }

        // Get cart total
        $cartTotal = WebsiteCart::getCartTotal();

        if ($cartTotal <= 0) {
            return response()->json([
                'success' => false,
                'message' => __('Your cart is empty.'),
            ], 400);
        }

        // Get user identifier (user_id, session_id, or phone from session)
        $userIdentifier = $this->getUserIdentifier();

        // Validate the coupon
        $validationError = $coupon->getValidationError($userIdentifier, $cartTotal);

        if ($validationError) {
            return response()->json([
                'success' => false,
                'message' => $validationError,
            ], 400);
        }

        // Calculate discount
        $discountAmount = $coupon->calculateDiscount($cartTotal);

        // Store coupon in session
        session([
            'applied_coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'discount_amount' => $discountAmount,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Coupon applied successfully!'),
            'coupon' => [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount_display' => $coupon->discount_display,
                'discount_amount' => $discountAmount,
            ],
            'cart_total' => $cartTotal,
            'discount' => $discountAmount,
            'final_total' => $cartTotal - $discountAmount,
        ]);
    }

    /**
     * Remove applied coupon (AJAX)
     */
    public function removeCoupon()
    {
        session()->forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => __('Coupon removed.'),
            'cart_total' => WebsiteCart::getCartTotal(),
        ]);
    }

    /**
     * Get applied coupon info (AJAX)
     */
    public function getAppliedCoupon()
    {
        $appliedCoupon = session('applied_coupon');
        $cartTotal = WebsiteCart::getCartTotal();

        if (!$appliedCoupon) {
            return response()->json([
                'success' => true,
                'coupon' => null,
                'cart_total' => $cartTotal,
            ]);
        }

        // Re-validate the coupon (in case cart total changed)
        $coupon = Coupon::find($appliedCoupon['id']);

        if (!$coupon) {
            session()->forget('applied_coupon');
            return response()->json([
                'success' => true,
                'coupon' => null,
                'cart_total' => $cartTotal,
            ]);
        }

        $userIdentifier = $this->getUserIdentifier();
        $validationError = $coupon->getValidationError($userIdentifier, $cartTotal);

        if ($validationError) {
            session()->forget('applied_coupon');
            return response()->json([
                'success' => true,
                'coupon' => null,
                'cart_total' => $cartTotal,
                'message' => $validationError,
            ]);
        }

        // Recalculate discount
        $discountAmount = $coupon->calculateDiscount($cartTotal);

        // Update session with new discount amount
        $appliedCoupon['discount_amount'] = $discountAmount;
        session(['applied_coupon' => $appliedCoupon]);

        return response()->json([
            'success' => true,
            'coupon' => [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount_display' => $coupon->discount_display,
                'discount_amount' => $discountAmount,
            ],
            'cart_total' => $cartTotal,
            'discount' => $discountAmount,
            'final_total' => $cartTotal - $discountAmount,
        ]);
    }

    /**
     * Get user identifier for coupon usage tracking
     */
    private function getUserIdentifier(): string
    {
        if (auth()->check()) {
            return 'user_' . auth()->id();
        }

        // Check if phone is stored in session (from checkout)
        if (session()->has('checkout_phone')) {
            return 'phone_' . session('checkout_phone');
        }

        return 'session_' . session()->getId();
    }
}
