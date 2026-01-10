<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MenuFavorite;
use App\Models\Cart;
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
            'addons.*' => 'exists:menu_addons,id'
        ]);

        $menuItem = MenuItem::findOrFail($request->menu_item_id);
        
        // Calculate price
        $price = $menuItem->base_price;
        
        // Add variant price if selected
        if ($request->variant_id) {
            $variant = $menuItem->variants()->find($request->variant_id);
            if ($variant) {
                $price += $variant->price_adjustment;
            }
        }
        
        // Add addons price if selected
        if ($request->addons) {
            $addons = $menuItem->addons()->whereIn('menu_addons.id', $request->addons)->get();
            foreach ($addons as $addon) {
                $price += $addon->price;
            }
        }

        // Check if item already in cart
        $cartItem = Cart::where('product_id', $request->menu_item_id)
            ->where('user_id', Auth::id())
            ->where('session_id', Session::getId())
            ->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->total_price = $cartItem->quantity * $price;
            $cartItem->save();
        } else {
            // Create new cart item
            Cart::create([
                'user_id' => Auth::id(),
                'session_id' => Session::getId(),
                'product_id' => $request->menu_item_id,
                'quantity' => $request->quantity,
                'unit_price' => $price,
                'total_price' => $price * $request->quantity,
                'variant_id' => $request->variant_id,
                'addons' => $request->addons ? json_encode($request->addons) : null,
            ]);
        }

        // Get updated cart count
        $cartCount = Cart::where(function($query) {
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            } else {
                $query->where('session_id', Session::getId());
            }
        })->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'cart_count' => $cartCount
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
