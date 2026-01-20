<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\BkashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Website\app\Models\WebsiteCart;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\ProductSale;

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
            'order_type' => 'required|in:delivery,take_away',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required_if:order_type,delivery|nullable|string|max:500',
            'city' => 'required_if:order_type,delivery|nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'delivery_notes' => 'nullable|string|max:500',
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
        $deliveryFee = $request->order_type === 'delivery' ? $this->calculateDeliveryFee() : 0;
        $tax = $this->calculateTax($subtotal);
        $grandTotal = $subtotal + $deliveryFee + $tax;

        // Generate temporary invoice number for payment reference
        $invoiceNumber = 'WEB' . now()->format('YmdHis') . rand(100, 999);

        // Store checkout data in session for later use
        session([
            'bkash_checkout_data' => [
                'order_type' => $request->order_type,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'delivery_notes' => $request->delivery_notes,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
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
                    $this->clearBkashSession();
                    return redirect()->route('website.checkout.success', $order->id)
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
            // Generate final invoice number
            $invoice = $this->generateInvoiceNumber();

            // Create sale/order
            $sale = Sale::create([
                'user_id' => 1, // System user for website orders
                'customer_id' => Auth::id(),
                'warehouse_id' => $this->getDefaultWarehouse(),
                'quantity' => $cartItems->sum('quantity'),
                'total_price' => $checkoutData['subtotal'],
                'order_type' => $checkoutData['order_type'],
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
                'shipping_cost' => $checkoutData['delivery_fee'],
                'grand_total' => $checkoutData['grand_total'],
                'order_date' => now(),
                'invoice' => $invoice,
                'delivery_address' => $checkoutData['order_type'] === 'delivery'
                    ? $checkoutData['address'] . ', ' . $checkoutData['city'] . ($checkoutData['postal_code'] ? ', ' . $checkoutData['postal_code'] : '')
                    : null,
                'delivery_phone' => $checkoutData['phone'],
                'delivery_notes' => $checkoutData['delivery_notes'],
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

            // Clear the cart
            WebsiteCart::clearCart();

            DB::commit();

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
     * Calculate delivery fee
     */
    private function calculateDeliveryFee()
    {
        return 0;
    }

    /**
     * Calculate tax
     */
    private function calculateTax($subtotal)
    {
        return 0;
    }

    /**
     * Get default warehouse ID
     */
    private function getDefaultWarehouse()
    {
        return \App\Models\Warehouse::first()->id ?? 1;
    }
}
