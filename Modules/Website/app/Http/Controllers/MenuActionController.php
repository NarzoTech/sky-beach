<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MenuFavorite;
use Modules\Website\app\Models\WebsiteCart;
use Modules\Menu\app\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MenuActionController extends Controller
{
    /**
     * Toggle favorite status for a menu item
     */
    public function toggleFavorite(Request $request, $itemId)
    {
        $request->validate([
            'item_id' => 'required|exists:menu_items,id'
        ]);

        $userId = Auth::id();
        $sessionId = Session::getId();

        // Check if already favorited
        $favorite = MenuFavorite::where('menu_item_id', $itemId)
            ->where(function($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->first();

        if ($favorite) {
            // Remove from favorites
            $favorite->delete();
            return response()->json([
                'success' => true,
                'message' => 'Removed from favorites',
                'is_favorite' => false
            ]);
        } else {
            // Add to favorites
            MenuFavorite::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'menu_item_id' => $itemId
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Added to favorites',
                'is_favorite' => true
            ]);
        }
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:menu_variants,id',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:menu_addons,id',
            'special_instructions' => 'nullable|string|max:500'
        ]);

        // Add item to cart using WebsiteCart model
        $cartItem = WebsiteCart::addItem(
            $request->menu_item_id,
            $request->quantity,
            $request->variant_id,
            $request->addons ?? [],
            $request->special_instructions
        );

        // Load the menu item relationship
        $cartItem->load('menuItem');

        // Get updated cart count and total
        $cartCount = WebsiteCart::getCartCount();
        $cartTotal = WebsiteCart::getCartTotal();

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
            'message' => 'Item added to cart',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'cart_item' => $cartItemData
        ]);
    }

    /**
     * Get user's favorites
     */
    public function getFavorites()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        $favorites = MenuFavorite::with('menuItem')
            ->where(function($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get()
            ->pluck('menu_item_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'favorites' => $favorites
        ]);
    }
}
