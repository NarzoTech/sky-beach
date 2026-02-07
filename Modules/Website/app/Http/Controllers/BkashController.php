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
use Modules\Membership\app\Models\LoyaltyCustomer;
use Modules\Membership\app\Models\LoyaltyProgram;
use Illuminate\Support\Str;

class BkashController extends Controller
{
    protected $bkashService;

    public function __construct(BkashService $bkashService)
    {
        $this->bkashService = $bkashService;
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
        $tax = $this->calculateTax($subtotal);
        $grandTotal = $subtotal + $tax;

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

            // Create sale/order
            $sale = Sale::create([
                'uid' => $uid,
                'user_id' => 1, // System user for website orders
                'customer_id' => Auth::id(),
                'warehouse_id' => $this->getDefaultWarehouse(),
                'quantity' => $cartItems->sum('quantity'),
                'total_price' => $checkoutData['subtotal'],
                'order_type' => 'take_away',
                'status' => 'pending',
                'payment_status' => 'success',
                'payment_method' => ['bkash'],
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

            // Clear the cart
            WebsiteCart::clearCart();

            DB::commit();

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
            'first_name' => $checkoutData['first_name'],
            'last_name' => $checkoutData['last_name'],
            'email' => $checkoutData['email'],
            'phone' => $checkoutData['phone'],
            'address' => $checkoutData['address'],
            'city' => $checkoutData['city'],
            'postal_code' => $checkoutData['postal_code'],
        ];

        // Cookie expires in 30 days (43200 minutes)
        $cookie = cookie('checkout_data', json_encode($cookieData), 43200);

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

            // Normalize phone number
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

            // Get active loyalty program
            $program = LoyaltyProgram::where('warehouse_id', $warehouseId)
                ->where('is_active', true)
                ->first();

            if (!$program) {
                $program = LoyaltyProgram::where('is_active', true)->first();
            }

            if (!$program) {
                return;
            }

            // Calculate points
            $pointsEarned = 0;
            $amount = $sale->grand_total;

            if ($program->earning_type === 'per_amount') {
                $pointsEarned = floor($amount / max(1, $program->earning_rate));
            } else {
                $pointsEarned = $program->earning_rate;
            }

            if ($program->min_transaction_amount && $amount < $program->min_transaction_amount) {
                $pointsEarned = 0;
            }

            if ($pointsEarned > 0) {
                $loyaltyCustomer->increment('total_points', $pointsEarned);
                $loyaltyCustomer->increment('lifetime_points', $pointsEarned);
                $loyaltyCustomer->update(['last_purchase_at' => now()]);

                $sale->update([
                    'loyalty_customer_id' => $loyaltyCustomer->id,
                    'points_earned' => $pointsEarned,
                ]);

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
            Log::error('Loyalty points error (bKash): ' . $e->getMessage());
        }
    }
}
