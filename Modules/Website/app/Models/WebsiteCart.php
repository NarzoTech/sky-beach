<?php

namespace Modules\Website\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuVariant;
use Modules\Menu\app\Models\MenuAddon;

class WebsiteCart extends Model
{
    use HasFactory;

    protected $table = 'website_carts';

    protected $fillable = [
        'user_id',
        'session_id',
        'menu_item_id',
        'variant_id',
        'quantity',
        'unit_price',
        'addons',
        'special_instructions',
    ];

    protected $casts = [
        'addons' => 'array',
        'unit_price' => 'decimal:2',
    ];

    protected $appends = ['subtotal', 'addon_names', 'variant_name'];

    /**
     * Get the menu item
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    /**
     * Get the variant
     */
    public function variant()
    {
        return $this->belongsTo(MenuVariant::class, 'variant_id');
    }

    /**
     * Calculate subtotal for this cart item
     */
    public function getSubtotalAttribute()
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Get addon names as array
     */
    public function getAddonNamesAttribute()
    {
        if (empty($this->addons)) {
            return [];
        }

        $addonIds = collect($this->addons)->pluck('id')->toArray();
        return MenuAddon::whereIn('id', $addonIds)->pluck('name')->toArray();
    }

    /**
     * Get variant name
     */
    public function getVariantNameAttribute()
    {
        return $this->variant ? $this->variant->name : null;
    }

    /**
     * Scope for current user or session
     */
    public function scopeForCurrentUser($query)
    {
        if (Auth::check()) {
            return $query->where('user_id', Auth::id());
        }
        return $query->where('session_id', Session::getId());
    }

    /**
     * Get cart for current user/session
     */
    public static function getCart()
    {
        return self::forCurrentUser()
            ->with(['menuItem', 'variant'])
            ->get();
    }

    /**
     * Get cart count
     */
    public static function getCartCount()
    {
        return self::forCurrentUser()->sum('quantity');
    }

    /**
     * Get cart total
     */
    public static function getCartTotal()
    {
        $cart = self::getCart();
        return $cart->sum('subtotal');
    }

    /**
     * Clear cart for current user/session
     */
    public static function clearCart()
    {
        return self::forCurrentUser()->delete();
    }

    /**
     * Add item to cart
     */
    public static function addItem($menuItemId, $quantity = 1, $variantId = null, $addons = [], $specialInstructions = null)
    {
        $menuItem = MenuItem::with(['variants', 'addons'])->findOrFail($menuItemId);

        // Calculate unit price
        $unitPrice = $menuItem->base_price;

        // Add variant price adjustment
        if ($variantId) {
            $variant = $menuItem->variants->find($variantId);
            if ($variant) {
                $unitPrice += $variant->price_adjustment;
            }
        }

        // Add addons prices
        $addonData = [];
        if (!empty($addons)) {
            $selectedAddons = MenuAddon::whereIn('id', $addons)->get();
            foreach ($selectedAddons as $addon) {
                $unitPrice += $addon->price;
                $addonData[] = [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => $addon->price,
                ];
            }
        }

        // Check if same item with same variant and addons exists
        $existingItem = self::forCurrentUser()
            ->where('menu_item_id', $menuItemId)
            ->where('variant_id', $variantId)
            ->first();

        // Compare addons
        if ($existingItem) {
            $existingAddonIds = collect($existingItem->addons)->pluck('id')->sort()->values()->toArray();
            $newAddonIds = collect($addonData)->pluck('id')->sort()->values()->toArray();

            if ($existingAddonIds == $newAddonIds) {
                // Same item, update quantity
                $existingItem->quantity += $quantity;
                $existingItem->special_instructions = $specialInstructions ?: $existingItem->special_instructions;
                $existingItem->save();
                return $existingItem;
            }
        }

        // Create new cart item
        return self::create([
            'user_id' => Auth::id(),
            'session_id' => Auth::check() ? null : Session::getId(),
            'menu_item_id' => $menuItemId,
            'variant_id' => $variantId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'addons' => $addonData,
            'special_instructions' => $specialInstructions,
        ]);
    }

    /**
     * Update cart item quantity
     */
    public static function updateItemQuantity($cartItemId, $quantity)
    {
        $item = self::forCurrentUser()->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $item->delete();
            return null;
        }

        $item->quantity = $quantity;
        $item->save();
        return $item;
    }

    /**
     * Remove item from cart
     */
    public static function removeItem($cartItemId)
    {
        return self::forCurrentUser()->where('id', $cartItemId)->delete();
    }

    /**
     * Transfer guest cart to user after login
     */
    public static function transferGuestCart($userId)
    {
        $sessionId = Session::getId();

        // Get guest cart items
        $guestItems = self::where('session_id', $sessionId)->get();

        foreach ($guestItems as $item) {
            // Check if user already has this item
            $existingItem = self::where('user_id', $userId)
                ->where('menu_item_id', $item->menu_item_id)
                ->where('variant_id', $item->variant_id)
                ->first();

            if ($existingItem) {
                $existingAddonIds = collect($existingItem->addons)->pluck('id')->sort()->values()->toArray();
                $itemAddonIds = collect($item->addons)->pluck('id')->sort()->values()->toArray();

                if ($existingAddonIds == $itemAddonIds) {
                    // Merge quantities
                    $existingItem->quantity += $item->quantity;
                    $existingItem->save();
                    $item->delete();
                    continue;
                }
            }

            // Transfer to user
            $item->user_id = $userId;
            $item->session_id = null;
            $item->save();
        }
    }
}
