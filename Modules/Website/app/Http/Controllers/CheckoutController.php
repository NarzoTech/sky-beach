<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Website\app\Models\WebsiteCart;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\ProductSale;
use Modules\Membership\app\Services\LoyaltyService;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuAddon;
use Modules\Menu\app\Models\Combo;
use App\Models\Stock;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Display checkout page
     */
    public function index()
    {
        $cartItems = WebsiteCart::getCart();

        if ($cartItems->isEmpty()) {
            return redirect()->route('website.cart.index')
                ->with('error', __('Your cart is empty.'));
        }

        $cartTotal = $cartItems->sum('subtotal');
        $cartCount = $cartItems->sum('quantity');

        // Get user's saved addresses if logged in
        $savedAddresses = [];
        $user = Auth::user();

        // Get saved checkout data from cookies
        $savedCheckoutData = $this->getSavedCheckoutData();

        // Get settings for tax
        $setting = Cache::get('setting');
        $taxEnabled = ($setting->website_tax_enabled ?? '1') == '1';
        $taxRate = $setting->website_tax_rate ?? 15;

        // Calculate tax for display
        $calculatedTax = $taxEnabled ? round($cartTotal * ($taxRate / 100), 2) : 0;

        return view('website::checkout', compact(
            'cartItems',
            'cartTotal',
            'cartCount',
            'savedAddresses',
            'user',
            'savedCheckoutData',
            'taxEnabled',
            'taxRate',
            'calculatedTax'
        ));
    }

    /**
     * Process checkout and create order
     */
    public function processOrder(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cash,card',
        ]);

        $cartItems = WebsiteCart::getCart();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('Your cart is empty.'),
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = $cartItems->sum('subtotal');
            $tax = $this->calculateTax($subtotal);
            $grandTotal = $subtotal + $tax;

            // Generate invoice number and UID
            $invoice = $this->generateInvoiceNumber();
            $uid = $this->generateUniqueUid();

            // Create sale/order
            $sale = Sale::create([
                'uid' => $uid,
                'user_id' => 1, // System user for website orders
                'customer_id' => Auth::id(),
                'warehouse_id' => $this->getDefaultWarehouse(),
                'quantity' => $cartItems->sum('quantity'),
                'total_price' => $subtotal,
                'order_type' => 'take_away',
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cash' ? 'unpaid' : 'pending',
                'payment_method' => [$request->payment_method],
                'total_tax' => $tax,
                'shipping_cost' => 0,
                'grand_total' => $grandTotal,
                'order_date' => now(),
                'invoice' => $invoice,
                'delivery_address' => null,
                'delivery_phone' => $request->phone,
                'delivery_notes' => null,
                'sale_note' => 'Website Order - ' . $request->first_name . ' ' . $request->last_name,
                'notes' => json_encode([
                    'customer_name' => $request->first_name . ' ' . $request->last_name,
                    'customer_email' => $request->email,
                    'customer_phone' => $request->phone,
                    'source' => 'website',
                ]),
            ]);

            // Create order line items
            foreach ($cartItems as $cartItem) {
                $addonsPrice = 0;
                if (!empty($cartItem->addons)) {
                    $addonsPrice = collect($cartItem->addons)->sum('price');
                }

                // Check if this is a combo item
                if ($cartItem->combo_id) {
                    // Create line item for combo
                    ProductSale::create([
                        'sale_id' => $sale->id,
                        'combo_id' => $cartItem->combo_id,
                        'combo_name' => $cartItem->combo->name ?? 'Combo',
                        'menu_item_id' => null,
                        'variant_id' => null,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->unit_price,
                        'addons' => [],
                        'addons_price' => 0,
                        'sub_total' => $cartItem->subtotal,
                        'note' => $cartItem->special_instructions,
                        'source' => 'website',
                    ]);
                } else {
                    // Regular menu item
                    ProductSale::create([
                        'sale_id' => $sale->id,
                        'menu_item_id' => $cartItem->menu_item_id,
                        'variant_id' => $cartItem->variant_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->unit_price - $addonsPrice, // Base price without addons
                        'addons' => $cartItem->addons,
                        'addons_price' => $addonsPrice,
                        'sub_total' => $cartItem->subtotal,
                        'note' => $cartItem->special_instructions,
                        'source' => 'website',
                    ]);
                }
            }

            // Deduct stock for all items in the order
            $this->deductStockForWebsiteOrder($sale, $cartItems);


            // Clear the cart
            WebsiteCart::clearCart();

            DB::commit();

            // Save checkout data to cookies for returning customers
            $this->saveCheckoutDataToCookie($request);

            // Handle loyalty points (after successful order)
            $this->handleLoyaltyPoints($sale, $request->phone, $request->first_name . ' ' . $request->last_name);

            // Return success response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Order placed successfully!'),
                    'order_id' => $sale->id,
                    'invoice' => $sale->invoice,
                    'redirect_url' => route('website.checkout.success', $sale->uid),
                ]);
            }

            return redirect()->route('website.checkout.success', $sale->uid)
                ->with('success', __('Order placed successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to place order. Please try again.'),
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            return back()->with('error', __('Failed to place order. Please try again.'));
        }
    }

    /**
     * Display order success page
     */
    public function orderSuccess($uid)
    {
        $order = Sale::with(['details.menuItem'])->where('uid', $uid)->firstOrFail();

        // Verify this is the customer's order or allow guest to view their recent order
        if (Auth::check() && $order->customer_id && $order->customer_id !== Auth::id()) {
            abort(403);
        }

        return view('website::checkout_success', compact('order'));
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'WEB';
        $date = now()->format('Ymd');
        $lastOrder = Sale::whereDate('created_at', now())
            ->where('invoice', 'like', $prefix . $date . '%')
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastOrder && preg_match('/(\d{4})$/', $lastOrder->invoice, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique UID for order
     */
    private function generateUniqueUid()
    {
        do {
            $uid = Str::uuid()->toString();
        } while (Sale::where('uid', $uid)->exists());

        return $uid;
    }

    /**
     * Calculate tax based on settings
     */
    private function calculateTax($subtotal)
    {
        $setting = Cache::get('setting');

        // Check if tax is enabled
        if (($setting->website_tax_enabled ?? '1') != '1') {
            return 0;
        }

        // Get tax rate (default 15%)
        $taxRate = floatval($setting->website_tax_rate ?? 15);

        return round($subtotal * ($taxRate / 100), 2);
    }

    /**
     * Get default warehouse ID
     */
    private function getDefaultWarehouse()
    {
        // Return the first warehouse or default to 1
        return \App\Models\Warehouse::first()->id ?? 1;
    }

    /**
     * Get saved checkout data from cookies
     */
    private function getSavedCheckoutData()
    {
        $cookieData = request()->cookie('checkout_data');
        if ($cookieData) {
            try {
                return json_decode($cookieData, true) ?? [];
            } catch (\Exception $e) {
                return [];
            }
        }
        return [];
    }

    /**
     * Save checkout data to cookies
     */
    private function saveCheckoutDataToCookie($request)
    {
        $checkoutData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        // Cookie expires in 1 year (525600 minutes)
        $cookie = cookie('checkout_data', json_encode($checkoutData), 525600);

        // Queue the cookie to be sent with the response
        cookie()->queue($cookie);
    }

    /**
     * Handle loyalty points for the order
     */
    private function handleLoyaltyPoints($sale, $phone, $customerName)
    {
        try {
            $setting = Cache::get('setting');

            // Check if loyalty is enabled
            if (($setting->website_loyalty_enabled ?? '1') != '1') {
                return;
            }

            // Normalize phone number (remove dashes and spaces)
            $phone = preg_replace('/[\s\-]/', '', $phone);

            if (!$phone) {
                return;
            }

            // Calculate points on subtotal after discount, before tax
            $loyaltyAmount = ($sale->total_price ?? 0) - ($sale->order_discount ?? 0);
            $saleContext = [
                'amount' => max(0, $loyaltyAmount),
                'sale_id' => $sale->id,
                'items' => [],
            ];

            $loyaltyResult = $this->loyaltyService->handleSaleCompletion($phone, $sale->warehouse_id ?? 1, $saleContext);

            if ($loyaltyResult['success'] ?? false) {
                $pointsEarned = $loyaltyResult['points_earned'] ?? 0;
                if ($pointsEarned > 0) {
                    $sale->update(['points_earned' => $pointsEarned]);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the order
            Log::error('Loyalty points error: ' . $e->getMessage());
        }
    }

    /**
     * Deduct stock for website order
     * This method deducts ingredient stock based on menu item recipes
     */
    private function deductStockForWebsiteOrder(Sale $sale, $cartItems)
    {
        try {
            foreach ($cartItems as $cartItem) {
                if ($cartItem->combo_id) {
                    // Handle combo - deduct stock for each menu item in the combo
                    $combo = Combo::with(['comboItems.menuItem.recipes.ingredient'])->find($cartItem->combo_id);
                    if ($combo) {
                        foreach ($combo->comboItems as $comboItem) {
                            if ($comboItem->menuItem) {
                                // Multiply combo item quantity by cart quantity
                                $totalQuantity = $comboItem->quantity * $cartItem->quantity;
                                $this->deductIngredientStockFromRecipe($comboItem->menuItem, $totalQuantity, $sale);
                            }
                        }
                    }
                } else if ($cartItem->menu_item_id) {
                    // Handle regular menu item
                    $menuItem = MenuItem::with(['recipes.ingredient'])->find($cartItem->menu_item_id);
                    if ($menuItem) {
                        $this->deductIngredientStockFromRecipe($menuItem, $cartItem->quantity, $sale);
                    }
                }

                // Deduct addon ingredient stock for all item types
                $addons = $cartItem->addons;
                if (!empty($addons)) {
                    if (is_string($addons)) {
                        $addons = json_decode($addons, true);
                    }
                    if (is_array($addons)) {
                        foreach ($addons as $addon) {
                            $addonId = $addon['id'] ?? null;
                            $addonQty = $addon['qty'] ?? 1;
                            if (!$addonId) continue;

                            $addonModel = MenuAddon::with('recipes.ingredient')->find($addonId);
                            if (!$addonModel || $addonModel->recipes->isEmpty()) continue;

                            $totalAddonQty = $addonQty * $cartItem->quantity;
                            foreach ($addonModel->recipes as $recipe) {
                                $ingredient = $recipe->ingredient;
                                if (!$ingredient) continue;

                                $deductQuantity = $recipe->quantity_required * $totalAddonQty;
                                $conversionRate = $ingredient->conversion_rate ?? 1;
                                $deductInPurchaseUnit = $deductQuantity / $conversionRate;

                                $ingredient->deductStock($deductQuantity, $ingredient->consumption_unit_id);

                                $stockData = [
                                    'sale_id' => $sale->id,
                                    'ingredient_id' => $ingredient->id,
                                    'unit_id' => $ingredient->consumption_unit_id,
                                    'date' => now(),
                                    'type' => 'Addon Sale',
                                    'invoice' => $sale->invoice,
                                    'out_quantity' => $deductQuantity,
                                    'base_out_quantity' => $deductInPurchaseUnit,
                                    'sku' => $ingredient->sku,
                                    'purchase_price' => $ingredient->purchase_price ?? 0,
                                    'average_cost' => $ingredient->average_cost ?? 0,
                                    'sale_price' => 0,
                                    'rate' => $ingredient->consumption_unit_cost ?? 0,
                                    'profit' => 0,
                                    'created_by' => 1,
                                ];
                                if ($sale->warehouse_id && \App\Models\Warehouse::find($sale->warehouse_id)) {
                                    $stockData['warehouse_id'] = $sale->warehouse_id;
                                }
                                Stock::create($stockData);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the order - stock can be adjusted manually
            Log::error('Stock deduction error for order ' . $sale->invoice . ': ' . $e->getMessage());
        }
    }

    /**
     * Deduct ingredient stock based on menu item recipe
     * When a menu item is sold, deduct stock from all ingredients in its recipe
     */
    private function deductIngredientStockFromRecipe(MenuItem $menuItem, $quantity, Sale $sale)
    {
        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            if (!$ingredient) continue;

            // Calculate quantity to deduct (recipe quantity * sale quantity)
            // Recipe quantity is in consumption unit
            $deductQuantity = $recipe->quantity_required * $quantity;

            // Convert to purchase unit for stock tracking
            $conversionRate = $ingredient->conversion_rate ?? 1;
            $deductInPurchaseUnit = $deductQuantity / $conversionRate;

            // Update ingredient stock using safe method (handles number_format, negative stock, low_stock)
            $ingredient->deductStock($deductQuantity, $ingredient->consumption_unit_id);

            // Create stock record for tracking (only if warehouse exists)
            $stockData = [
                'sale_id' => $sale->id,
                'ingredient_id' => $ingredient->id,
                'unit_id' => $ingredient->consumption_unit_id,
                'date' => now(),
                'type' => 'Website Sale',
                'invoice' => $sale->invoice,
                'out_quantity' => $deductQuantity,
                'base_out_quantity' => $deductInPurchaseUnit,
                'sku' => $ingredient->sku,
                'purchase_price' => $ingredient->purchase_price ?? 0,
                'average_cost' => $ingredient->average_cost ?? 0,
                'sale_price' => 0,
                'rate' => $ingredient->consumption_unit_cost ?? 0,
                'profit' => 0,
                'created_by' => 1, // System user for website orders
            ];

            // Only add warehouse_id if it exists
            if ($sale->warehouse_id && \App\Models\Warehouse::find($sale->warehouse_id)) {
                $stockData['warehouse_id'] = $sale->warehouse_id;
            }

            Stock::create($stockData);
        }
    }
}
