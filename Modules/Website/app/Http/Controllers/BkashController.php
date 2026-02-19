<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\BkashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Modules\Website\app\Models\WebsiteCart;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\ProductSale;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuAddon;
use Modules\Menu\app\Models\Combo;
use App\Models\Stock;
use Modules\Membership\app\Services\LoyaltyService;
use Modules\Website\app\Models\Coupon;
use Illuminate\Support\Str;

class BkashController extends Controller
{
    protected $bkashService;
    protected $loyaltyService;

    public function __construct(BkashService $bkashService, LoyaltyService $loyaltyService)
    {
        $this->bkashService = $bkashService;
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Initiate bKash payment
     */
    public function createPayment(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $cartItems = WebsiteCart::getCart();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => __('Your cart is empty.'),
            ], 400);
        }

        // Calculate totals
        $subtotal = $cartItems->sum('subtotal');

        // Apply coupon discount
        $couponDiscount = 0;
        $appliedCoupon = session('applied_coupon');
        $couponData = null;

        if ($appliedCoupon) {
            $coupon = Coupon::find($appliedCoupon['id']);
            if ($coupon && $coupon->isValid()) {
                $couponDiscount = $coupon->calculateDiscount($subtotal);
                $couponData = [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'discount' => $couponDiscount,
                ];
            }
        }

        $tax = $this->calculateTax($subtotal - $couponDiscount);
        $grandTotal = $subtotal - $couponDiscount + $tax;

        // Generate temporary invoice number for payment reference
        $invoiceNumber = 'WEB' . now()->format('YmdHis') . rand(100, 999);

        // Store checkout data in session for later use
        session([
            'bkash_checkout_data' => [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'grand_total' => $grandTotal,
                'coupon_discount' => $couponDiscount,
                'coupon_data' => $couponData,
                'invoice_number' => $invoiceNumber,
            ]
        ]);

        // Create bKash payment
        $callbackUrl = route('website.bkash.callback');
        $result = $this->bkashService->createPayment($grandTotal, $invoiceNumber, $callbackUrl);

        if ($result['success']) {
            // Store payment ID in session
            session(['bkash_payment_id' => $result['paymentID']]);

            return response()->json([
                'success' => true,
                'bkashURL' => $result['bkashURL'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? __('Failed to initiate payment. Please try again.'),
        ], 500);
    }

    /**
     * Handle bKash callback
     */
    public function callback(Request $request)
    {
        $paymentID = $request->query('paymentID');
        $status = $request->query('status');

        // Verify payment ID matches session
        $sessionPaymentID = session('bkash_payment_id');
        $checkoutData = session('bkash_checkout_data');

        if (!$paymentID || !$checkoutData) {
            return redirect()->route('website.checkout.index')
                ->with('error', __('Invalid payment session. Please try again.'));
        }

        if ($status === 'cancel') {
            $this->clearBkashSession();
            return redirect()->route('website.checkout.index')
                ->with('error', __('Payment was cancelled.'));
        }

        if ($status === 'failure') {
            $this->clearBkashSession();
            return redirect()->route('website.checkout.index')
                ->with('error', __('Payment failed. Please try again.'));
        }

        if ($status === 'success') {
            // Execute the payment
            $result = $this->bkashService->executePayment($paymentID);

            if ($result['success']) {
                // Create the order
                $order = $this->createOrder($checkoutData, $result);

                if ($order) {
                    // Save checkout data to cookies for returning customers
                    $this->saveCheckoutDataToCookie($checkoutData);

                    $this->clearBkashSession();
                    return redirect()->route('website.checkout.success', $order->uid)
                        ->with('success', __('Payment successful! Your order has been placed.'));
                }

                return redirect()->route('website.checkout.index')
                    ->with('error', __('Payment was successful but order creation failed. Please contact support.'));
            }

            $this->clearBkashSession();
            return redirect()->route('website.checkout.index')
                ->with('error', $result['message'] ?? __('Payment verification failed. Please try again.'));
        }

        $this->clearBkashSession();
        return redirect()->route('website.checkout.index')
            ->with('error', __('Unknown payment status. Please try again.'));
    }

    /**
     * Create order after successful payment
     */
    protected function createOrder($checkoutData, $paymentResult)
    {
        $cartItems = WebsiteCart::getCart();

        if ($cartItems->isEmpty()) {
            Log::error('bKash: Cart empty when creating order after successful payment');
            return null;
        }

        DB::beginTransaction();

        try {
            // Generate final invoice number and UID
            $invoice = $this->generateInvoiceNumber();
            $uid = $this->generateUniqueUid();

            // Get coupon data from session
            $couponDiscount = $checkoutData['coupon_discount'] ?? 0;
            $couponData = $checkoutData['coupon_data'] ?? null;

            // Create sale/order
            $sale = Sale::create([
                'uid' => $uid,
                'user_id' => 1, // System user for website orders
                'customer_id' => Auth::id(),
                'customer_phone' => $checkoutData['phone'],
                'warehouse_id' => $this->getDefaultWarehouse(),
                'quantity' => $cartItems->sum('quantity'),
                'total_price' => $checkoutData['subtotal'],
                'order_type' => 'website',
                'status' => 'pending',
                'payment_status' => 'success',
                'payment_method' => ['bkash'],
                'order_discount' => $couponDiscount,
                'coupon_code' => $couponData['code'] ?? null,
                'coupon_id' => $couponData['id'] ?? null,
                'payment_details' => json_encode([
                    'gateway' => 'bkash',
                    'payment_id' => $paymentResult['data']['paymentID'] ?? null,
                    'transaction_id' => $paymentResult['transactionId'] ?? null,
                    'payer_reference' => $paymentResult['data']['payerReference'] ?? null,
                    'customer_msisdn' => $paymentResult['data']['customerMsisdn'] ?? null,
                    'payment_execute_time' => $paymentResult['data']['paymentExecuteTime'] ?? null,
                ]),
                'total_tax' => $checkoutData['tax'],
                'shipping_cost' => 0,
                'grand_total' => $checkoutData['grand_total'],
                'order_date' => now(),
                'invoice' => $invoice,
                'delivery_address' => null,
                'delivery_phone' => $checkoutData['phone'],
                'delivery_notes' => null,
                'sale_note' => 'Website Order (bKash) - ' . $checkoutData['first_name'] . ' ' . $checkoutData['last_name'],
                'notes' => json_encode([
                    'customer_name' => $checkoutData['first_name'] . ' ' . $checkoutData['last_name'],
                    'customer_email' => $checkoutData['email'],
                    'customer_phone' => $checkoutData['phone'],
                    'source' => 'website',
                    'payment_gateway' => 'bkash',
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
                        'price' => $cartItem->unit_price - $addonsPrice,
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

            // Record coupon usage after successful order
            if ($couponData && $couponDiscount > 0) {
                $coupon = Coupon::find($couponData['id']);
                if ($coupon) {
                    $userIdentifier = Auth::check() ? 'user_' . Auth::id() : 'phone_' . preg_replace('/[\s\-]/', '', $checkoutData['phone']);
                    $coupon->recordUsage($userIdentifier, $couponDiscount, $sale->id);
                }
                session()->forget('applied_coupon');
            }

            // Handle loyalty points
            $this->handleLoyaltyPoints($sale, $checkoutData['phone'], $checkoutData['first_name'] . ' ' . $checkoutData['last_name']);

            return $sale;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('bKash Order Creation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Clear bKash session data
     */
    protected function clearBkashSession()
    {
        session()->forget(['bkash_payment_id', 'bkash_checkout_data']);
    }

    /**
     * Save checkout data to cookies for returning customers
     */
    protected function saveCheckoutDataToCookie($checkoutData)
    {
        $cookieData = [
            'name' => ($checkoutData['first_name'] ?? '') . ' ' . ($checkoutData['last_name'] ?? ''),
            'email' => $checkoutData['email'] ?? '',
            'phone' => $checkoutData['phone'] ?? '',
        ];

        // Cookie expires in 1 year (525600 minutes)
        $cookie = cookie('checkout_data', json_encode($cookieData), 525600);

        // Queue the cookie to be sent with the response
        cookie()->queue($cookie);
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
        return \App\Models\Warehouse::first()->id ?? 1;
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
            Log::error('Loyalty points error (bKash): ' . $e->getMessage());
        }
    }

    /**
     * Deduct stock for website order
     */
    private function deductStockForWebsiteOrder(Sale $sale, $cartItems)
    {
        try {
            foreach ($cartItems as $cartItem) {
                if ($cartItem->combo_id) {
                    $combo = Combo::with(['comboItems.menuItem.recipes.ingredient'])->find($cartItem->combo_id);
                    if ($combo) {
                        foreach ($combo->comboItems as $comboItem) {
                            if ($comboItem->menuItem) {
                                $totalQuantity = $comboItem->quantity * $cartItem->quantity;
                                $this->deductIngredientStockFromRecipe($comboItem->menuItem, $totalQuantity, $sale);
                            }
                        }
                    }
                } else if ($cartItem->menu_item_id) {
                    $menuItem = MenuItem::with(['recipes.ingredient'])->find($cartItem->menu_item_id);
                    if ($menuItem) {
                        $this->deductIngredientStockFromRecipe($menuItem, $cartItem->quantity, $sale);
                    }
                }

                // Deduct addon ingredient stock
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
            Log::error('Stock deduction error for bKash order ' . $sale->invoice . ': ' . $e->getMessage());
        }
    }

    /**
     * Deduct ingredient stock based on menu item recipe
     */
    private function deductIngredientStockFromRecipe(MenuItem $menuItem, $quantity, Sale $sale)
    {
        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            if (!$ingredient) continue;

            $deductQuantity = $recipe->quantity_required * $quantity;
            $conversionRate = $ingredient->conversion_rate ?? 1;
            $deductInPurchaseUnit = $deductQuantity / $conversionRate;

            $ingredient->deductStock($deductQuantity, $ingredient->consumption_unit_id);

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
                'created_by' => 1,
            ];

            if ($sale->warehouse_id && \App\Models\Warehouse::find($sale->warehouse_id)) {
                $stockData['warehouse_id'] = $sale->warehouse_id;
            }

            Stock::create($stockData);
        }
    }
}
