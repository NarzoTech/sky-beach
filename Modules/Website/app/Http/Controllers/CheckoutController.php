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
use Modules\Membership\app\Models\LoyaltyCustomer;
use Modules\Membership\app\Models\LoyaltyProgram;
use Modules\Membership\app\Services\LoyaltyService;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
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

        // Get settings for tax and delivery
        $setting = Cache::get('setting');
        $taxEnabled = ($setting->website_tax_enabled ?? '1') == '1';
        $taxRate = $setting->website_tax_rate ?? 15;
        $deliveryFeeEnabled = ($setting->website_delivery_fee_enabled ?? '1') == '1';
        $deliveryFee = $setting->website_delivery_fee ?? 50;
        $freeDeliveryThreshold = $setting->website_free_delivery_threshold ?? 0;

        // Calculate tax and delivery for display
        $calculatedTax = $taxEnabled ? round($cartTotal * ($taxRate / 100), 2) : 0;
        $calculatedDeliveryFee = $deliveryFeeEnabled ? $deliveryFee : 0;

        // Check free delivery threshold
        if ($freeDeliveryThreshold > 0 && $cartTotal >= $freeDeliveryThreshold) {
            $calculatedDeliveryFee = 0;
        }

        return view('website::checkout', compact(
            'cartItems',
            'cartTotal',
            'cartCount',
            'savedAddresses',
            'user',
            'savedCheckoutData',
            'taxEnabled',
            'taxRate',
            'calculatedTax',
            'deliveryFeeEnabled',
            'calculatedDeliveryFee',
            'freeDeliveryThreshold'
        ));
    }

    /**
     * Process checkout and create order
     */
    public function processOrder(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:delivery,take_away',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required_if:order_type,delivery|nullable|string|max:500',
            'city' => 'required_if:order_type,delivery|nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'delivery_notes' => 'nullable|string|max:500',
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
            $deliveryFee = $request->order_type === 'delivery' ? $this->calculateDeliveryFee($subtotal) : 0;
            $tax = $this->calculateTax($subtotal);
            $grandTotal = $subtotal + $deliveryFee + $tax;

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
                'order_type' => $request->order_type,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cash' ? 'unpaid' : 'pending',
                'payment_method' => [$request->payment_method],
                'total_tax' => $tax,
                'shipping_cost' => $deliveryFee,
                'grand_total' => $grandTotal,
                'order_date' => now(),
                'invoice' => $invoice,
                'delivery_address' => $request->order_type === 'delivery'
                    ? $request->address . ', ' . $request->city . ($request->postal_code ? ', ' . $request->postal_code : '')
                    : null,
                'delivery_phone' => $request->phone,
                'delivery_notes' => $request->delivery_notes,
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
     * Calculate delivery fee based on settings
     */
    private function calculateDeliveryFee($subtotal = 0)
    {
        $setting = Cache::get('setting');

        // Check if delivery fee is enabled
        if (($setting->website_delivery_fee_enabled ?? '1') != '1') {
            return 0;
        }

        // Check free delivery threshold
        $threshold = $setting->website_free_delivery_threshold ?? 0;
        if ($threshold > 0 && $subtotal >= $threshold) {
            return 0;
        }

        return floatval($setting->website_delivery_fee ?? 50);
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
            'address' => $request->address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
        ];

        // Cookie expires in 30 days (43200 minutes)
        $cookie = cookie('checkout_data', json_encode($checkoutData), 43200);

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

            // Find or create loyalty customer
            $loyaltyCustomer = LoyaltyCustomer::where('phone', $phone)->first();

            if (!$loyaltyCustomer) {
                $loyaltyCustomer = LoyaltyCustomer::create([
                    'phone' => $phone,
                    'name' => $customerName,
                    'status' => 'active',
                    'total_points' => 0,
                    'lifetime_points' => 0,
                    'redeemed_points' => 0,
                    'joined_at' => now(),
                ]);
            }

            if (!$loyaltyCustomer || $loyaltyCustomer->status !== 'active') {
                return;
            }

            // Get warehouse
            $warehouseId = $sale->warehouse_id ?? $this->getDefaultWarehouse();

            // Get active loyalty program for this warehouse
            $program = LoyaltyProgram::where('warehouse_id', $warehouseId)
                ->where('is_active', true)
                ->first();

            if (!$program) {
                // Try to get any active program
                $program = LoyaltyProgram::where('is_active', true)->first();
            }

            if (!$program) {
                return;
            }

            // Calculate points based on program settings
            $pointsEarned = 0;
            $amount = $sale->grand_total;

            if ($program->earning_type === 'per_amount') {
                // e.g., 1 point per $10 spent
                $pointsEarned = floor($amount / max(1, $program->earning_rate));
            } else {
                // per_transaction - fixed points per order
                $pointsEarned = $program->earning_rate;
            }

            // Check minimum transaction amount
            if ($program->min_transaction_amount && $amount < $program->min_transaction_amount) {
                $pointsEarned = 0;
            }

            if ($pointsEarned > 0) {
                // Update customer points
                $loyaltyCustomer->increment('total_points', $pointsEarned);
                $loyaltyCustomer->increment('lifetime_points', $pointsEarned);
                $loyaltyCustomer->update(['last_purchase_at' => now()]);

                // Update sale with points info
                $sale->update([
                    'loyalty_customer_id' => $loyaltyCustomer->id,
                    'points_earned' => $pointsEarned,
                ]);

                // Create loyalty transaction record
                if (class_exists(\Modules\Membership\app\Models\LoyaltyTransaction::class)) {
                    \Modules\Membership\app\Models\LoyaltyTransaction::create([
                        'loyalty_customer_id' => $loyaltyCustomer->id,
                        'warehouse_id' => $warehouseId,
                        'transaction_type' => 'earn',
                        'points_amount' => $pointsEarned,
                        'points_balance_before' => $loyaltyCustomer->total_points - $pointsEarned,
                        'points_balance_after' => $loyaltyCustomer->total_points,
                        'source_type' => 'sale',
                        'source_id' => $sale->id,
                        'description' => 'Points earned from website order #' . $sale->invoice,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the order
            Log::error('Loyalty points error: ' . $e->getMessage());
        }
    }
}
