<?php

namespace Modules\Website\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Website\app\Models\WebsiteCart;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\ProductSale;

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

        return view('website::checkout', compact(
            'cartItems',
            'cartTotal',
            'cartCount',
            'savedAddresses',
            'user'
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
            $deliveryFee = $request->order_type === 'delivery' ? $this->calculateDeliveryFee() : 0;
            $tax = $this->calculateTax($subtotal);
            $grandTotal = $subtotal + $deliveryFee + $tax;

            // Generate invoice number
            $invoice = $this->generateInvoiceNumber();

            // Create sale/order
            $sale = Sale::create([
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

            // Clear the cart
            WebsiteCart::clearCart();

            DB::commit();

            // Return success response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Order placed successfully!'),
                    'order_id' => $sale->id,
                    'invoice' => $sale->invoice,
                    'redirect_url' => route('website.checkout.success', $sale->id),
                ]);
            }

            return redirect()->route('website.checkout.success', $sale->id)
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
    public function orderSuccess($id)
    {
        $order = Sale::with(['details.menuItem'])->findOrFail($id);

        // Verify this is the customer's order or allow guest to view their recent order
        if (Auth::check() && $order->customer_id !== Auth::id()) {
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
     * Calculate delivery fee
     */
    private function calculateDeliveryFee()
    {
        // TODO: Implement dynamic delivery fee calculation
        // For now, return a fixed fee
        return 0;
    }

    /**
     * Calculate tax
     */
    private function calculateTax($subtotal)
    {
        // TODO: Implement tax calculation based on settings
        // For now, return 0 (no tax)
        return 0;
    }

    /**
     * Get default warehouse ID
     */
    private function getDefaultWarehouse()
    {
        // Return the first warehouse or default to 1
        return \App\Models\Warehouse::first()->id ?? 1;
    }
}
