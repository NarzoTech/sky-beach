<?php

namespace Modules\Sales\app\Services;

use App\Models\Ledger;
use App\Models\Payment;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Models\Account;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Models\Variant;
use Modules\Menu\app\Models\Combo;
use Modules\Menu\app\Models\MenuAddon;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuVariant;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Service\app\Models\Service;
use Modules\Membership\app\Services\LoyaltyService;
use Modules\Membership\app\Models\LoyaltyCustomer;
use Modules\TableManagement\app\Models\RestaurantTable;

class SaleService
{
    protected $loyaltyService;

    public function __construct(private Sale $sale)
    {
        // Try to resolve LoyaltyService if available
        try {
            $this->loyaltyService = app(LoyaltyService::class);
        } catch (\Exception $e) {
            $this->loyaltyService = null;
        }
    }

    private function parseDate($date)
    {
        if (!$date) {
            return null;
        }

        // Try d-m-Y format first (expected from form)
        try {
            return Carbon::createFromFormat('d-m-Y', $date);
        } catch (Exception $e) {
            // Try Y-m-d format (database format)
            try {
                return Carbon::createFromFormat('Y-m-d', $date);
            } catch (Exception $e) {
                // Try parsing as general date string
                return Carbon::parse($date);
            }
        }
    }

    public function getSales()
    {
        return $this->sale->with('products', 'customer', 'services', 'details', 'payment');
    }
    public function createSale(Request $request, $user, $cart): Sale
    {
        $sale = new Sale();
        $sale->user_id = $user != null ?  $user->id : null;

        $isGuest = !$request->order_customer_id || $request->order_customer_id == 'walk-in-customer';
        $sale->customer_id = $isGuest ? null : $request->order_customer_id;
        $sale->customer_phone = $request->customer_phone;
        $sale->warehouse_id = 1;
        $sale->quantity = 1;
        $sale->total_price = $request->sub_total;
        $sale->order_date = Carbon::createFromFormat('d-m-Y', $request->sale_date);
        $sale->order_type = $request->order_type ?? Sale::ORDER_TYPE_DINE_IN;
        $sale->table_id = $request->table_id;
        $sale->guest_count = $request->guest_count ?? 1;
        $sale->waiter_id = $request->waiter_id;
        // Only defer payment when explicitly requested (e.g., "Start Order" button)
        // Do NOT auto-defer for dine-in when user goes through "Complete Payment" checkout
        $deferPayment = $request->defer_payment == 1;

        // Status uses string values: 'pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'
        if ($deferPayment) {
            $sale->status = 'pending'; // Pending order (waiting for payment)
            $sale->payment_status = 0; // Unpaid
            $sale->payment_method = null;
            $sale->paid_amount = 0;
            $sale->receive_amount = 0;
            $sale->return_amount = 0;
            $sale->due_amount = $request->total_amount;
        } else {
            $sale->status = 'completed'; // Completed and paid
            $sale->payment_status = 1; // Paid

            // Ensure payment_type is an array
            $paymentTypes = $request->payment_type;
            if (!is_array($paymentTypes)) {
                $paymentTypes = $paymentTypes ? [$paymentTypes] : ['cash'];
            }

            // Ensure paying_amount is an array
            $payingAmounts = $request->paying_amount;
            if (!is_array($payingAmounts)) {
                $payingAmounts = $payingAmounts ? [$payingAmounts] : [$request->receive_amount ?? 0];
            }

            $sale->payment_method = json_encode($paymentTypes);
            $sale->paid_amount = array_sum($payingAmounts);
            $sale->receive_amount = $request->receive_amount ?? array_sum($payingAmounts);
            $sale->return_amount = $request->return_amount ?? 0;
            $due = $request->total_amount - array_sum($payingAmounts);
            $sale->due_amount = $due < 0 ? 0 : $due;
        }

        $sale->order_discount = $request->discount_amount;
        $sale->total_tax = $request->total_tax ?? $request->tax_amount ?? 0;
        $sale->tax_rate = $request->tax_rate ?? 0;

        // Calculate grand_total - use provided value or calculate from sub_total, discount, and tax
        $grandTotal = $request->total_amount;
        if (empty($grandTotal) || $grandTotal == 0) {
            $subTotal = $request->sub_total ?? 0;
            $discount = $sale->order_discount ?? 0;
            $tax = $sale->total_tax ?? 0;
            $grandTotal = $subTotal - $discount + $tax;
        }
        $sale->grand_total = $grandTotal;
        $sale->invoice = $this->genInvoiceNumber();
        $sale->due_date = $request->due_date ? $this->parseDate($request->due_date) : null;
        $sale->sale_note = $request->remark;

        // Loyalty points redemption
        $pointsToRedeem = $request->points_to_redeem ?? $request->points_redeemed ?? 0;
        $sale->points_redeemed = $pointsToRedeem;
        $sale->points_discount = $request->points_discount ?? 0;
        $sale->loyalty_customer_id = $request->loyalty_customer_id ?: null;

        $sale->created_by = auth('admin')->id();
        $sale->save();

        // Mark table as occupied for dine-in orders
        if ($sale->table_id && $sale->order_type === Sale::ORDER_TYPE_DINE_IN) {
            try {
                Log::info('Occupying table for dine-in order', [
                    'sale_id' => $sale->id,
                    'table_id' => $sale->table_id,
                    'guest_count' => $sale->guest_count,
                    'order_type' => $sale->order_type
                ]);
                $table = RestaurantTable::find($sale->table_id);
                if ($table) {
                    $table->occupy($sale);
                    Log::info('Table occupied', [
                        'table_id' => $table->id,
                        'occupied_seats' => $table->occupied_seats,
                        'status' => $table->status
                    ]);
                } else {
                    Log::warning('Table not found', ['table_id' => $sale->table_id]);
                }
            } catch (\Exception $e) {
                Log::error('Error occupying table: ' . $e->getMessage());
            }
        }

        // Process loyalty points redemption if applicable
        if ($pointsToRedeem > 0 && $sale->loyalty_customer_id && $this->loyaltyService) {
            try {
                $loyaltyCustomer = LoyaltyCustomer::find($sale->loyalty_customer_id);
                if ($loyaltyCustomer) {
                    $this->loyaltyService->handleRedemption($loyaltyCustomer->phone, 1, [
                        'points' => $pointsToRedeem,
                        'redemption_type' => 'discount',
                        'value' => $sale->points_discount,
                        'sale_id' => $sale->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error redeeming loyalty points for sale #' . $sale->id . ': ' . $e->getMessage());
            }
        }


        $totalQty = 0;

        // Ensure cart is an array
        $cart = $cart ?? [];

        foreach ($cart as $item) {
            $totalQty += $item['qty'];

            $saleQuantity = $item['qty'];
            $itemType = $item['type'] ?? 'menu_item';

            // Handle menu item type
            if ($itemType == 'menu_item') {
                $menuItem = MenuItem::with('recipes.ingredient')->find($item['id']);
                $menuVariant = isset($item['variant_id']) ? MenuVariant::find($item['variant_id']) : null;

                // Calculate COGS for this menu item
                $cogsAmount = $this->calculateMenuItemCOGS($menuItem, $saleQuantity);
                $subTotal = $item['sub_total'];
                $profitAmount = $subTotal - $cogsAmount;

                $orderDetails = new ProductSale();
                $orderDetails->sale_id = $sale->id;
                $orderDetails->menu_item_id = $menuItem->id;
                $orderDetails->service_id = null;
                $orderDetails->product_sku = $item['sku'] ?? $menuItem->sku;
                $orderDetails->variant_id = $menuVariant ? $menuVariant->id : null;
                $orderDetails->price = $item['price'];
                $orderDetails->source = $item['source'] ?? 1;
                $orderDetails->purchase_price = $menuItem->cost_price ?? 0;
                $orderDetails->selling_price = $item['selling_price'] ?? $item['price'];
                $orderDetails->quantity = $saleQuantity;
                $orderDetails->base_quantity = $saleQuantity;
                $orderDetails->sub_total = $subTotal;
                $orderDetails->cogs_amount = $cogsAmount;
                $orderDetails->profit_amount = $profitAmount;
                $orderDetails->attributes = $menuVariant ? $menuVariant->name : null;
                $orderDetails->addons = $item['addons'] ?? null;
                $orderDetails->addons_price = $item['addons_price'] ?? 0;
                $orderDetails->save();

                // Deduct ingredient stock based on menu item recipes
                if ($menuItem && $item['source'] == 1) {
                    $this->deductIngredientStockFromRecipe($menuItem, $saleQuantity, $sale, $request);

                    // Deduct addon ingredient stock
                    $this->deductAddonStock(
                        $item['addons'] ?? null,
                        $saleQuantity,
                        $sale,
                        $this->parseDate($request->sale_date)
                    );
                }
            }
            // Handle legacy product type (direct ingredient sale)
            elseif ($itemType == 'product') {
                $variant = isset($item['variant']) ?  Variant::where('sku', $item['sku'])->first() : null;

                // Get ingredient and unit information for conversion
                $product = Ingredient::where('id', $item['id'])->first();
                $saleUnitId = $item['unit_id'] ?? ($product ? $product->unit_id : null);

                // Convert quantity to product's base unit for stock tracking
                $baseQuantity = $saleQuantity;
                if ($product && $saleUnitId && $saleUnitId != $product->unit_id) {
                    try {
                        $baseQuantity = \App\Helpers\UnitConverter::convert(
                            $saleQuantity,
                            $saleUnitId,
                            $product->unit_id
                        );
                    } catch (\Exception $e) {
                        // If conversion fails, use original quantity
                        $baseQuantity = $saleQuantity;
                    }
                }

                $orderDetails = new ProductSale();
                $orderDetails->sale_id = $sale->id;
                $orderDetails->ingredient_id = $product ? $product->id : null;
                $orderDetails->service_id = null;
                $orderDetails->product_sku = $item['sku'];
                $orderDetails->variant_id = $variant != null ? $variant->id : null;
                $orderDetails->unit_id = $saleUnitId;
                $orderDetails->price = $item['price'];
                $orderDetails->source = $item['source'];
                $orderDetails->purchase_price = $item['purchase_price'];
                $orderDetails->selling_price = $item['selling_price'];
                $orderDetails->quantity = $saleQuantity;
                $orderDetails->base_quantity = $baseQuantity;
                $orderDetails->sub_total = $item['sub_total'];
                $orderDetails->attributes = $variant != null ? $item['variant']['attribute'] : null;
                $orderDetails->save();

                // update stock using base quantity
                if ($product != null && $item['source'] == 1) {
                    $product->deductStock($baseQuantity);

                    // create stock with unit tracking
                    $purchasePrice = $product->last_purchase_price ?? 0;
                    Stock::create([
                        'sale_id' => $sale->id,
                        'ingredient_id' => $product->id,
                        'unit_id' => $saleUnitId,
                        'date' => Carbon::createFromFormat('d-m-Y', $request->sale_date),
                        'type' => 'Sale',
                        'invoice' => route('admin.sales.invoice', $sale->id),
                        'out_quantity' => $saleQuantity,
                        'base_out_quantity' => $baseQuantity,
                        'sku' => $product->sku,
                        'purchase_price' => $purchasePrice,
                        'sale_price' => $item['price'],
                        'rate' => $item['price'],
                        'profit' => ($item['price'] - $purchasePrice) * $saleQuantity,
                        'created_by' => auth('admin')->user()->id,
                    ]);
                }
            }
            // Handle service type
            elseif ($itemType == 'service') {
                $orderDetails = new ProductSale();
                $orderDetails->sale_id = $sale->id;
                $orderDetails->service_id = $item['id'];
                $orderDetails->product_sku = $item['sku'] ?? '';
                $orderDetails->price = $item['price'];
                $orderDetails->source = $item['source'] ?? 1;
                $orderDetails->purchase_price = $item['purchase_price'] ?? 0;
                $orderDetails->selling_price = $item['selling_price'] ?? $item['price'];
                $orderDetails->quantity = $saleQuantity;
                $orderDetails->base_quantity = $saleQuantity;
                $orderDetails->sub_total = $item['sub_total'];
                $orderDetails->save();
            }
            // Handle combo type - expand combo into individual menu items
            elseif ($itemType == 'combo') {
                $combo = Combo::with(['items.menuItem.recipes.ingredient', 'items.variant'])->find($item['id']);
                if ($combo) {
                    // Store each combo item as individual menu items
                    foreach ($combo->items as $comboItem) {
                        $menuItem = $comboItem->menuItem;
                        if (!$menuItem) continue;

                        $comboItemQty = $comboItem->quantity * $saleQuantity;

                        // Calculate proportional price from combo
                        $menuItemOriginalPrice = $menuItem->price;
                        if ($comboItem->variant) {
                            $menuItemOriginalPrice += $comboItem->variant->price_adjustment ?? 0;
                        }

                        // Calculate proportional share of combo price
                        $proportionalShare = ($combo->original_price > 0)
                            ? ($menuItemOriginalPrice * $comboItem->quantity) / $combo->original_price
                            : 1 / $combo->items->count();
                        $proportionalPrice = ($combo->combo_price * $proportionalShare) / $comboItem->quantity;
                        $itemSubTotal = $proportionalPrice * $comboItemQty;

                        // Calculate COGS
                        $cogsAmount = $this->calculateMenuItemCOGS($menuItem, $comboItemQty);
                        $profitAmount = $itemSubTotal - $cogsAmount;

                        $orderDetails = new ProductSale();
                        $orderDetails->sale_id = $sale->id;
                        $orderDetails->menu_item_id = $menuItem->id;
                        $orderDetails->service_id = null;
                        $orderDetails->product_sku = $menuItem->sku ?? '';
                        $orderDetails->variant_id = $comboItem->variant_id;
                        $orderDetails->price = $proportionalPrice;
                        $orderDetails->source = 1;
                        $orderDetails->purchase_price = $menuItem->cost_price ?? 0;
                        $orderDetails->selling_price = $proportionalPrice;
                        $orderDetails->quantity = $comboItemQty;
                        $orderDetails->base_quantity = $comboItemQty;
                        $orderDetails->sub_total = $itemSubTotal;
                        $orderDetails->cogs_amount = $cogsAmount;
                        $orderDetails->profit_amount = $profitAmount;
                        $orderDetails->attributes = $comboItem->variant ? $comboItem->variant->name : null;
                        $orderDetails->combo_id = $combo->id;
                        $orderDetails->combo_name = $combo->name;
                        $orderDetails->note = $item['note'] ?? null;
                        $orderDetails->save();

                        // Deduct ingredient stock
                        if ($menuItem) {
                            $this->deductIngredientStockFromRecipe($menuItem, $comboItemQty, $sale, $request);
                        }
                    }
                }
            }
        }

        $sale->quantity = $totalQty;

        // Calculate estimated prep time from menu items (use max prep time)
        $maxPrepTime = 0;
        foreach ($cart as $item) {
            $itemType = $item['type'] ?? 'menu_item';
            if ($itemType === 'menu_item') {
                $menuItem = MenuItem::find($item['id']);
                if ($menuItem && $menuItem->preparation_time) {
                    $maxPrepTime = max($maxPrepTime, $menuItem->preparation_time);
                }
            }
        }
        if ($maxPrepTime > 0) {
            $sale->estimated_prep_minutes = $maxPrepTime;
        }

        $sale->save();

        // Update COGS totals on the sale
        $this->updateSaleCOGSTotals($sale);

        // create payments (skip for deferred payment orders)
        $paymentTypes = $request->payment_type ?? [];
        if (!is_array($paymentTypes)) {
            $paymentTypes = $paymentTypes ? [$paymentTypes] : [];
        }

        $payingAmounts = $request->paying_amount ?? [];
        if (!is_array($payingAmounts)) {
            $payingAmounts = $payingAmounts ? [$payingAmounts] : [];
        }

        $accountIds = $request->account_id ?? [];
        if (!is_array($accountIds)) {
            $accountIds = $accountIds ? [$accountIds] : [];
        }

        if (!empty($paymentTypes)) {
            foreach ($paymentTypes as $key => $item) {
                if (empty($item)) continue;

                $account = null;
                if ($item == 'cash') {
                    $account = Account::firstOrCreate(
                        ['account_type' => 'cash'],
                        ['bank_account_name' => 'Cash Register']
                    );
                } else {
                    $accountId = $accountIds[$key] ?? null;
                    if ($accountId) {
                        $account = Account::find($accountId);
                    }
                    if (!$account) {
                        $account = Account::where('account_type', $item)->first();
                    }
                }

                if (!$account) continue;

                $payingAmount = $payingAmounts[$key] ?? 0;
                $isGuestPayment = !$sale->customer_id;
                $data = [
                    'payment_type' => 'sale',
                    'sale_id' => $sale->id,
                    'is_received' => 1,
                    'customer_id' => $isGuestPayment ? null : $sale->customer_id,
                    'is_guest' => $isGuestPayment ? 1 : 0,
                    'account_id' => $account->id,
                    'amount' => $payingAmount,
                    'payment_date' => Carbon::createFromFormat('d-m-Y', $request->sale_date),
                    'created_by' => auth('admin')->user()->id,
                ];
                if (!empty($payingAmount)) {
                    CustomerPayment::create($data);
                }
            }
        }


        // create due
        if ($request->total_due && $user) {
            CustomerDue::create([
                'invoice' => $sale->invoice,
                'due_amount' => $request->total_due,
                'due_date' => $request->due_date,
                'status' => 1,
                'customer_id' => $user->id
            ]);
        }


        // if user is exists

        if ($user) {
            // $this->updateLedger($request, $sale->id, $user, 'sale');
            $payingAmountsForLedger = $request->paying_amount;
            if (!is_array($payingAmountsForLedger)) {
                $payingAmountsForLedger = $payingAmountsForLedger ? [$payingAmountsForLedger] : [0];
            }
            $this->salesLedger($request, $sale, array_sum($payingAmountsForLedger), $request->total_amount, 'sale', 1, $sale->due_amount);
        }

        // If dine-in order with a table, occupy the table
        if ($sale->order_type === Sale::ORDER_TYPE_DINE_IN && $sale->table_id) {
            $table = \Modules\TableManagement\app\Models\RestaurantTable::find($sale->table_id);
            if ($table) {
                $table->occupy($sale);
            }
        }

        return $sale;
    }

    /**
     * Create a sale from waiter order (accepts array data instead of Request)
     *
     * @param array $saleData Sale information
     * @param array $cart Cart items
     * @param mixed $customer Customer info (optional)
     * @param array $payments Payment info (optional)
     * @return Sale
     */
    public function createWaiterOrder(array $saleData, array $cart, $customer = null, array $payments = []): Sale
    {
        $sale = new Sale();
        $sale->user_id = $customer?->id ?? null;
        $sale->customer_id = $saleData['customer_id'] ?? null;
        $sale->warehouse_id = 1;
        $sale->quantity = 1;
        $sale->total_price = $saleData['subtotal'] ?? 0;
        $sale->order_date = Carbon::now();
        $sale->order_type = $saleData['order_type'] ?? Sale::ORDER_TYPE_DINE_IN;
        $sale->table_id = $saleData['table_id'] ?? null;
        $sale->guest_count = $saleData['guest_count'] ?? 1;
        $sale->waiter_id = $saleData['waiter_id'] ?? null;
        $sale->special_instructions = $saleData['special_instructions'] ?? null;

        // Waiter orders are always deferred payment (unpaid)
        $sale->status = 'pending'; // Processing/Pending
        $sale->payment_status = 0; // Unpaid
        $sale->payment_method = null;
        $sale->paid_amount = 0;
        $sale->receive_amount = 0;
        $sale->return_amount = 0;
        $sale->due_amount = $saleData['total'] ?? $saleData['subtotal'] ?? 0;

        $sale->order_discount = $saleData['discount'] ?? 0;
        $sale->total_tax = $saleData['tax'] ?? 0;
        $sale->tax_rate = $saleData['tax_rate'] ?? 0;

        // Calculate grand_total - use provided value or calculate from subtotal, discount, and tax
        $grandTotal = $saleData['total'] ?? 0;
        if (empty($grandTotal) || $grandTotal == 0) {
            $subTotal = $saleData['subtotal'] ?? 0;
            $discount = $sale->order_discount ?? 0;
            $tax = $sale->total_tax ?? 0;
            $grandTotal = $subTotal - $discount + $tax;
        }
        $sale->grand_total = $grandTotal;
        $sale->invoice = $this->genInvoiceNumber();
        $sale->sale_note = $saleData['note'] ?? null;
        $sale->created_by = auth('admin')->id();
        $sale->save();

        $totalQty = 0;

        foreach ($cart as $item) {
            $totalQty += $item['qty'];
            $saleQuantity = $item['qty'];
            $itemType = $item['type'] ?? 'menu_item';

            // Handle menu item type
            if ($itemType == 'menu_item') {
                $menuItem = MenuItem::with('recipes.ingredient')->find($item['id']);
                $menuVariant = isset($item['variant_id']) ? MenuVariant::find($item['variant_id']) : null;

                // Calculate COGS for this menu item
                $cogsAmount = $this->calculateMenuItemCOGS($menuItem, $saleQuantity);
                $subTotal = $item['sub_total'];
                $profitAmount = $subTotal - $cogsAmount;

                $orderDetails = new ProductSale();
                $orderDetails->sale_id = $sale->id;
                $orderDetails->menu_item_id = $menuItem->id;
                $orderDetails->service_id = null;
                $orderDetails->product_sku = $item['sku'] ?? $menuItem->sku ?? '';
                $orderDetails->variant_id = $menuVariant ? $menuVariant->id : null;
                $orderDetails->price = $item['price'];
                $orderDetails->source = $item['source'] ?? 1;
                $orderDetails->purchase_price = $menuItem->cost_price ?? 0;
                $orderDetails->selling_price = $item['selling_price'] ?? $item['price'];
                $orderDetails->quantity = $saleQuantity;
                $orderDetails->base_quantity = $saleQuantity;
                $orderDetails->sub_total = $subTotal;
                $orderDetails->cogs_amount = $cogsAmount;
                $orderDetails->profit_amount = $profitAmount;
                $orderDetails->attributes = $menuVariant ? $menuVariant->name : null;
                $orderDetails->addons = !empty($item['addons']) ? $item['addons'] : null;
                $orderDetails->addons_price = $item['addons_price'] ?? 0;
                $orderDetails->note = $item['note'] ?? null;
                $orderDetails->save();

                // Deduct ingredient stock based on menu item recipes
                if ($menuItem && ($item['source'] ?? 1) == 1) {
                    $this->deductIngredientStockForWaiterOrder($menuItem, $saleQuantity, $sale);

                    // Deduct addon ingredient stock
                    $addons = $item['addons'] ?? null;
                    if (is_string($addons)) {
                        $addons = json_decode($addons, true);
                    }
                    $this->deductAddonStock($addons, $saleQuantity, $sale);
                }
            }
            // Handle combo type
            elseif ($itemType == 'combo') {
                $combo = Combo::with(['items.menuItem.recipes.ingredient', 'items.variant'])->find($item['id']);
                if ($combo) {
                    foreach ($combo->items as $comboItem) {
                        $menuItem = $comboItem->menuItem;
                        if (!$menuItem) continue;

                        $comboItemQty = $comboItem->quantity * $saleQuantity;

                        // Calculate proportional price
                        $menuItemOriginalPrice = $menuItem->price;
                        if ($comboItem->variant) {
                            $menuItemOriginalPrice += $comboItem->variant->price_adjustment ?? 0;
                        }

                        $proportionalShare = ($combo->original_price > 0)
                            ? ($menuItemOriginalPrice * $comboItem->quantity) / $combo->original_price
                            : 1 / $combo->items->count();
                        $proportionalPrice = ($combo->combo_price * $proportionalShare) / $comboItem->quantity;
                        $itemSubTotal = $proportionalPrice * $comboItemQty;

                        $cogsAmount = $this->calculateMenuItemCOGS($menuItem, $comboItemQty);
                        $profitAmount = $itemSubTotal - $cogsAmount;

                        $orderDetails = new ProductSale();
                        $orderDetails->sale_id = $sale->id;
                        $orderDetails->menu_item_id = $menuItem->id;
                        $orderDetails->product_sku = $menuItem->sku ?? '';
                        $orderDetails->variant_id = $comboItem->variant_id;
                        $orderDetails->price = $proportionalPrice;
                        $orderDetails->source = 1;
                        $orderDetails->purchase_price = $menuItem->cost_price ?? 0;
                        $orderDetails->selling_price = $proportionalPrice;
                        $orderDetails->quantity = $comboItemQty;
                        $orderDetails->base_quantity = $comboItemQty;
                        $orderDetails->sub_total = $itemSubTotal;
                        $orderDetails->cogs_amount = $cogsAmount;
                        $orderDetails->profit_amount = $profitAmount;
                        $orderDetails->attributes = $comboItem->variant ? $comboItem->variant->name : null;
                        $orderDetails->combo_id = $combo->id;
                        $orderDetails->combo_name = $combo->name;
                        $orderDetails->note = $item['note'] ?? null;
                        $orderDetails->save();

                        // Deduct ingredient stock
                        $this->deductIngredientStockForWaiterOrder($menuItem, $comboItemQty, $sale);
                    }
                }
            }
        }

        $sale->quantity = $totalQty;

        // Calculate estimated prep time
        $maxPrepTime = 0;
        foreach ($cart as $item) {
            $itemType = $item['type'] ?? 'menu_item';
            if ($itemType === 'menu_item') {
                $menuItem = MenuItem::find($item['id']);
                if ($menuItem && $menuItem->preparation_time) {
                    $maxPrepTime = max($maxPrepTime, $menuItem->preparation_time);
                }
            }
        }
        if ($maxPrepTime > 0) {
            $sale->estimated_prep_minutes = $maxPrepTime;
        }

        $sale->save();

        // Update COGS totals
        $this->updateSaleCOGSTotals($sale);

        return $sale;
    }

    /**
     * Deduct ingredient stock for waiter orders (doesn't require Request object)
     */
    private function deductIngredientStockForWaiterOrder(MenuItem $menuItem, $quantity, Sale $sale)
    {
        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            if (!$ingredient) continue;

            $deductQuantity = $recipe->quantity_required * $quantity;
            $conversionRate = $ingredient->conversion_rate ?? 1;
            $deductInPurchaseUnit = $deductQuantity / $conversionRate;

            // Update ingredient stock using safe method (handles number_format, negative stock, low_stock)
            $ingredient->deductStock($deductQuantity, $ingredient->consumption_unit_id);

            Stock::create([
                'sale_id' => $sale->id,
                'ingredient_id' => $ingredient->id,
                'unit_id' => $ingredient->consumption_unit_id,
                'date' => Carbon::now(),
                'type' => 'Sale',
                'invoice' => route('admin.sales.invoice', $sale->id),
                'out_quantity' => $deductQuantity,
                'base_out_quantity' => $deductInPurchaseUnit,
                'sku' => $ingredient->sku,
                'purchase_price' => $ingredient->purchase_price ?? 0,
                'sale_price' => 0,
                'rate' => $ingredient->consumption_unit_cost ?? 0,
                'profit' => 0,
                'created_by' => auth('admin')->id(),
            ]);
        }
    }

    public function updateSale(Request $request, $user, $cart, $id): Sale
    {
        DB::beginTransaction();
        try {
            $sale = $this->sale->find($id);


            // update sales
            $sale->user_id = $user != null ?  $user->id : null;
            $isGuestUpdate = !$request->order_customer_id || $request->order_customer_id == 'walk-in-customer';
            $sale->customer_id = $isGuestUpdate ? null : $request->order_customer_id;
            $sale->warehouse_id = 1;
            $sale->total_price = $request->sub_total;
            $sale->order_date = $this->parseDate($request->sale_date);
            $sale->status = 'completed';
            $sale->payment_status = 1;

            // Ensure payment arrays are properly formatted
            $paymentTypes = $request->payment_type;
            if (!is_array($paymentTypes)) {
                $paymentTypes = $paymentTypes ? [$paymentTypes] : ['cash'];
            }
            $payingAmounts = $request->paying_amount;
            if (!is_array($payingAmounts)) {
                $payingAmounts = $payingAmounts ? [$payingAmounts] : [$request->receive_amount ?? 0];
            }

            $sale->payment_method = json_encode($paymentTypes);
            $sale->order_discount = $request->discount_amount;
            $sale->total_tax = $request->total_tax ?? 0;

            // Calculate grand_total - use provided value or calculate from sub_total, discount, and tax
            $grandTotal = $request->total_amount;
            if (empty($grandTotal) || $grandTotal == 0) {
                $subTotal = $request->sub_total ?? 0;
                $discount = $sale->order_discount ?? 0;
                $tax = $sale->total_tax ?? 0;
                $grandTotal = $subTotal - $discount + $tax;
            }
            $sale->grand_total = $grandTotal;
            $sale->paid_amount = array_sum($payingAmounts);


            $due = $request->total_amount - array_sum($payingAmounts);
            $sale->due_amount = $due < 0 ? 0 : $due;
            $sale->due_date = $request->due_date ? $this->parseDate($request->due_date) : null;
            $sale->sale_note = $request->remark;
            $sale->receive_amount = $request->receive_amount ?? array_sum($payingAmounts);
            $sale->return_amount = $request->return_amount ?? 0;
            $sale->updated_by = auth('admin')->user()->id;

            // restore ingredient stock from menu items via recipes
            foreach ($sale->menuItems as $item) {
                $menuItem = MenuItem::with('recipes.ingredient')->find($item->menu_item_id);
                if ($menuItem && $item->source == 1) {
                    $this->restoreIngredientStockFromRecipe($menuItem, $item->quantity, $sale);

                    // Restore addon ingredient stock
                    $addons = $item->addons;
                    if (is_string($addons)) {
                        $addons = json_decode($addons, true);
                    }
                    $this->restoreAddonStock($addons, $item->quantity, $sale);
                }
            }

            // restore product stock using base quantity (for legacy ingredient sales)
            foreach ($sale->products as $item) {
                $product = Ingredient::where('id', $item->ingredient_id)->first();
                if ($product != null && $item->source == 1) {
                    $restoreQty = $item->base_quantity ?? $item->quantity;
                    $product->addStock($restoreQty);
                }
            }



            // delete old details
            $sale->details()->delete();
            $sale->payment()->delete();
            $sale->customer_due()->delete();
            $sale->stock()->delete();

            $totalQty = 0;
            foreach ($cart as $item) {
                $totalQty += $item['qty'];

                $saleQuantity = $item['qty'];
                $itemType = $item['type'] ?? 'menu_item';

                // Handle menu item type
                if ($itemType == 'menu_item') {
                    $menuItem = MenuItem::with('recipes.ingredient')->find($item['id']);
                    $menuVariant = isset($item['variant_id']) ? MenuVariant::find($item['variant_id']) : null;

                    // Calculate COGS for this menu item
                    $cogsAmount = $this->calculateMenuItemCOGS($menuItem, $saleQuantity);
                    $subTotal = $item['sub_total'];
                    $profitAmount = $subTotal - $cogsAmount;

                    $orderDetails = new ProductSale();
                    $orderDetails->sale_id = $sale->id;
                    $orderDetails->menu_item_id = $menuItem->id;
                    $orderDetails->service_id = null;
                    $orderDetails->product_sku = $item['sku'] ?? $menuItem->sku;
                    $orderDetails->variant_id = $menuVariant ? $menuVariant->id : null;
                    $orderDetails->price = $item['price'];
                    $orderDetails->source = $item['source'] ?? 1;
                    $orderDetails->purchase_price = $menuItem->cost_price ?? 0;
                    $orderDetails->selling_price = $item['selling_price'] ?? $item['price'];
                    $orderDetails->quantity = $saleQuantity;
                    $orderDetails->base_quantity = $saleQuantity;
                    $orderDetails->sub_total = $subTotal;
                    $orderDetails->cogs_amount = $cogsAmount;
                    $orderDetails->profit_amount = $profitAmount;
                    $orderDetails->attributes = $menuVariant ? $menuVariant->name : null;
                    $orderDetails->addons = $item['addons'] ?? null;
                    $orderDetails->addons_price = $item['addons_price'] ?? 0;
                    $orderDetails->save();

                    // Deduct ingredient stock based on menu item recipes
                    if ($menuItem && ($item['source'] ?? 1) == 1) {
                        $this->deductIngredientStockFromRecipe($menuItem, $saleQuantity, $sale, $request);

                        // Deduct addon ingredient stock
                        $this->deductAddonStock(
                            $item['addons'] ?? null,
                            $saleQuantity,
                            $sale,
                            $this->parseDate($request->sale_date)
                        );
                    }
                }
                // Handle combo type - expand combo into individual menu items
                elseif ($itemType == 'combo') {
                    $combo = Combo::with(['items.menuItem.recipes.ingredient', 'items.variant'])->find($item['id']);
                    if ($combo) {
                        foreach ($combo->items as $comboItem) {
                            $menuItem = $comboItem->menuItem;
                            if (!$menuItem) continue;

                            $comboItemQty = $comboItem->quantity * $saleQuantity;

                            // Calculate proportional price from combo
                            $menuItemOriginalPrice = $menuItem->price;
                            if ($comboItem->variant) {
                                $menuItemOriginalPrice += $comboItem->variant->price_adjustment ?? 0;
                            }

                            $proportionalShare = ($combo->original_price > 0)
                                ? ($menuItemOriginalPrice * $comboItem->quantity) / $combo->original_price
                                : 1 / $combo->items->count();
                            $proportionalPrice = ($combo->combo_price * $proportionalShare) / $comboItem->quantity;
                            $itemSubTotal = $proportionalPrice * $comboItemQty;

                            // Calculate COGS
                            $cogsAmount = $this->calculateMenuItemCOGS($menuItem, $comboItemQty);
                            $profitAmount = $itemSubTotal - $cogsAmount;

                            $orderDetails = new ProductSale();
                            $orderDetails->sale_id = $sale->id;
                            $orderDetails->menu_item_id = $menuItem->id;
                            $orderDetails->service_id = null;
                            $orderDetails->product_sku = $menuItem->sku ?? '';
                            $orderDetails->variant_id = $comboItem->variant_id;
                            $orderDetails->price = $proportionalPrice;
                            $orderDetails->source = 1;
                            $orderDetails->purchase_price = $menuItem->cost_price ?? 0;
                            $orderDetails->selling_price = $proportionalPrice;
                            $orderDetails->quantity = $comboItemQty;
                            $orderDetails->base_quantity = $comboItemQty;
                            $orderDetails->sub_total = $itemSubTotal;
                            $orderDetails->cogs_amount = $cogsAmount;
                            $orderDetails->profit_amount = $profitAmount;
                            $orderDetails->attributes = $comboItem->variant ? $comboItem->variant->name : null;
                            $orderDetails->combo_id = $combo->id;
                            $orderDetails->combo_name = $combo->name;
                            $orderDetails->note = $item['note'] ?? null;
                            $orderDetails->save();

                            // Deduct ingredient stock
                            if ($menuItem) {
                                $this->deductIngredientStockFromRecipe($menuItem, $comboItemQty, $sale, $request);
                            }
                        }
                    }
                }
                // Handle legacy product type (direct ingredient sale)
                elseif ($itemType == 'product') {
                    $variant = isset($item['variant']) ?  Variant::where('sku', $item['sku'])->first() : null;

                    // Get ingredient and unit information for conversion
                    $product = Ingredient::where('id', $item['id'])->first();
                    $saleUnitId = $item['unit_id'] ?? ($product ? $product->unit_id : null);

                    // Convert quantity to product's base unit for stock tracking
                    $baseQuantity = $saleQuantity;
                    if ($product && $saleUnitId && $saleUnitId != $product->unit_id) {
                        try {
                            $baseQuantity = \App\Helpers\UnitConverter::convert(
                                $saleQuantity,
                                $saleUnitId,
                                $product->unit_id
                            );
                        } catch (\Exception $e) {
                            // If conversion fails, use original quantity
                            $baseQuantity = $saleQuantity;
                        }
                    }

                    $orderDetails = new ProductSale();
                    $orderDetails->sale_id = $sale->id;
                    $orderDetails->ingredient_id = $product ? $product->id : null;
                    $orderDetails->service_id = null;
                    $orderDetails->product_sku = $item['sku'];
                    $orderDetails->variant_id = $variant != null ? $variant->id : null;
                    $orderDetails->unit_id = $saleUnitId;
                    $orderDetails->price = $item['price'];
                    $orderDetails->source = $item['source'];
                    $orderDetails->purchase_price = $item['purchase_price'];
                    $orderDetails->selling_price = $item['selling_price'];
                    $orderDetails->quantity = $saleQuantity;
                    $orderDetails->base_quantity = $baseQuantity;
                    $orderDetails->sub_total = $item['sub_total'];
                    $orderDetails->attributes = $variant != null ? $item['variant']['attribute'] : null;
                    $orderDetails->save();

                    // update stock using base quantity
                    if ($product != null && $item['source'] == 1) {
                        $product->deductStock($baseQuantity);

                        // create stock with unit tracking
                        $purchasePrice = $product->last_purchase_price ?? 0;
                        Stock::create([
                            'sale_id' => $sale->id,
                            'ingredient_id' => $product->id,
                            'unit_id' => $saleUnitId,
                            'date' => $this->parseDate($request->sale_date),
                            'type' => 'Sale',
                            'invoice' => route('admin.sales.invoice', $sale->id),
                            'out_quantity' => $saleQuantity,
                            'base_out_quantity' => $baseQuantity,
                            'sku' => $product->sku,
                            'purchase_price' => $purchasePrice,
                            'sale_price' => $item['price'],
                            'rate' => $item['price'],
                            'profit' => ($item['price'] - $purchasePrice) * $saleQuantity,
                            'created_by' => auth('admin')->user()->id,
                        ]);
                    }
                }
                // Handle service type
                elseif ($itemType == 'service') {
                    $orderDetails = new ProductSale();
                    $orderDetails->sale_id = $sale->id;
                    $orderDetails->service_id = $item['id'];
                    $orderDetails->product_sku = $item['sku'] ?? '';
                    $orderDetails->price = $item['price'];
                    $orderDetails->source = $item['source'] ?? 1;
                    $orderDetails->purchase_price = $item['purchase_price'] ?? 0;
                    $orderDetails->selling_price = $item['selling_price'] ?? $item['price'];
                    $orderDetails->quantity = $saleQuantity;
                    $orderDetails->base_quantity = $saleQuantity;
                    $orderDetails->sub_total = $item['sub_total'];
                    $orderDetails->save();
                }
            }

            $sale->quantity = $totalQty;

            // Calculate estimated prep time from menu items (use max prep time)
            $maxPrepTime = 0;
            foreach ($cart as $item) {
                $itemType = $item['type'] ?? 'menu_item';
                if ($itemType === 'menu_item') {
                    $menuItem = MenuItem::find($item['id']);
                    if ($menuItem && $menuItem->preparation_time) {
                        $maxPrepTime = max($maxPrepTime, $menuItem->preparation_time);
                    }
                }
            }
            if ($maxPrepTime > 0) {
                $sale->estimated_prep_minutes = $maxPrepTime;
            }

            $sale->save();

            // Update COGS totals on the sale
            $this->updateSaleCOGSTotals($sale);

            $ledger = $this->getLedger($request, $id, 1, 'sale');

            // Ensure payment arrays are properly formatted
            $paymentTypes = $request->payment_type;
            if (!is_array($paymentTypes)) {
                $paymentTypes = $paymentTypes ? [$paymentTypes] : ['cash'];
            }

            $payingAmounts = $request->paying_amount;
            if (!is_array($payingAmounts)) {
                $payingAmounts = $payingAmounts ? [$payingAmounts] : [$request->receive_amount ?? 0];
            }

            $accountIds = $request->account_id;
            if (!is_array($accountIds)) {
                $accountIds = $accountIds ? [$accountIds] : [null];
            }

            $this->salesLedger($request, $sale, array_sum($payingAmounts), $request->total_amount, 'sale', 1, $due, $ledger);

            // create payments
            foreach ($paymentTypes as $key => $item) {
                if ($item == 'cash') {
                    $account = Account::firstOrCreate(
                        ['account_type' => 'cash'],
                        ['bank_account_name' => 'Cash Register']
                    );
                } else {
                    $account = Account::where('account_type', $item)
                        ->where('id', $accountIds[$key] ?? null)->first();
                }
                $isGuestPayment = !$sale->customer_id;
                $payingAmount = $payingAmounts[$key] ?? 0;
                $data = [
                    'payment_type' => 'sale',
                    'sale_id' => $sale->id,
                    'is_received' => 1,
                    'customer_id' => $isGuestPayment ? null : $sale->customer_id,
                    'is_guest' => $isGuestPayment ? 1 : 0,
                    'account_id' => $account->id ?? null,
                    'amount' => $payingAmount,
                    'payment_date' => $this->parseDate($request->sale_date),
                    'created_by' => auth('admin')->user()->id,
                ];
                if ($payingAmount) {
                    CustomerPayment::create($data);
                }
            }


            // create due
            if ($request->total_due && $user) {
                CustomerDue::create([
                    'invoice' => $sale->invoice,
                    'due_amount' => $request->total_due,
                    'due_date' => $request->due_date,
                    'status' => 1,
                    'customer_id' => $user->id
                ]);
            }

            DB::commit();
            return $sale;
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollback();
            throw $ex;
        }
    }

    public function deleteSale($id): void
    {
        $sale = $this->sale->find($id);

        // delete sales related all info

        // restore ingredient stock from menu items via recipes
        foreach ($sale->menuItems as $item) {
            $menuItem = MenuItem::with('recipes.ingredient')->find($item->menu_item_id);
            if ($menuItem && $item->source == 1) {
                $this->restoreIngredientStockFromRecipe($menuItem, $item->quantity, $sale);

                // Restore addon ingredient stock
                $addons = $item->addons;
                if (is_string($addons)) {
                    $addons = json_decode($addons, true);
                }
                $this->restoreAddonStock($addons, $item->quantity, $sale);
            }
        }

        // restore product stock (legacy ingredient sales)
        foreach ($sale->products as $item) {
            $product = Ingredient::where('id', $item->ingredient_id)->first();
            if ($product != null && $item->source == 1) {
                $restoreQty = $item->base_quantity ?? $item->quantity;
                $product->addStock($restoreQty);
            }
        }

        // delete payments
        $sale->payment()->delete();

        // delete due
        $sale->customer_due()->delete();

        // delete sale details
        $sale->details()->delete();

        // delete product stock
        $sale->stock()->delete();

        // delete sale
        $sale->delete();
    }

    public function genInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $sale = $this->sale->latest()->first();
        if ($sale) {
            $saleInvoice = $sale->invoice;

            // split the invoice number
            $split_invoice = explode('-', $saleInvoice);
            // Safely get the numeric part (handle cases where format is different)
            if (isset($split_invoice[1])) {
                $invoice_number = (int) $split_invoice[1] + 1;
            } else {
                // If no dash in invoice, try to extract number from end
                preg_match('/(\d+)$/', $saleInvoice, $matches);
                $invoice_number = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
            }
            $invoice_number = $prefix . $invoice_number;
        }

        return $invoice_number;
    }
    public function editSale($id)
    {
        $sale = $this->getSales()->find($id);

        // Initialize cart contents as empty array
        $cart_contents = [];

        // Track processed combo IDs to avoid duplicates
        $processedCombos = [];

        foreach ($sale->details as $key => $detail) {
            $data = array();
            $data["rowid"] = uniqid();

            // Handle combo items first (they have combo_id set)
            // Group all items from same combo into one cart entry
            if ($detail->combo_id) {
                // Skip if we already processed this combo
                if (in_array($detail->combo_id, $processedCombos)) {
                    continue;
                }

                $combo = Combo::find($detail->combo_id);
                if (!$combo) continue;

                // Calculate total qty and sub_total for this combo from all its items
                $comboDetails = $sale->details->where('combo_id', $detail->combo_id);
                $comboQty = $comboDetails->first()->quantity ?? 1;
                $comboSubTotal = $comboDetails->sum('sub_total');
                $comboPrice = $comboSubTotal / max(1, $comboQty);

                $data['id'] = $combo->id;
                $data['name'] = $detail->combo_name ?? $combo->name;
                $data['type'] = 'combo';
                $data['image'] = $combo->image ?? '';
                $data['qty'] = $comboQty;
                $data['price'] = $comboPrice;
                $data['sub_total'] = $comboSubTotal;
                $data['sku'] = '';
                $data['source'] = 1;
                $data['purchase_price'] = 0;
                $data['selling_price'] = $comboPrice;

                $processedCombos[] = $detail->combo_id;
            }
            // Handle menu item (without combo_id)
            elseif ($detail->menu_item_id) {
                $menuItem = MenuItem::find($detail->menu_item_id);
                if (!$menuItem) continue;
                $data['id'] = $menuItem->id;
                $data['name'] = $menuItem->name;
                $data['type'] = 'menu_item';
                $data['image'] = $menuItem->image_url;
                $data['qty'] = $detail->quantity;
                $data['price'] = $detail->price;
                $data['sub_total'] = $detail->sub_total;
                $data['sku'] = $detail->product_sku ?? $menuItem->sku;
                $data['source'] = $detail->source;
                $data['purchase_price'] = $detail->purchase_price ?? $menuItem->cost_price ?? 0;
                $data['selling_price'] = $detail->selling_price ?? $detail->price;
                $data['variant_id'] = $detail->variant_id;
                $data['addons'] = $detail->addons ?? [];
                $data['addons_price'] = $detail->addons_price ?? 0;
                if ($detail->variant_id) {
                    $data['variant']['attribute'] = $detail->attributes;
                    $data['variant']['options'] = [];
                }
            }
            // Handle legacy ingredient (product)
            elseif ($detail->ingredient_id) {
                $product = Ingredient::find($detail->ingredient_id);
                if (!$product) continue;
                $data['id'] = $product->id;
                $data['name'] = $product->name;
                $data['type'] = 'product';
                $data['image'] = $product->image_url;
                $data['qty'] = $detail->quantity;
                $data['price'] = $detail->price;
                $data['sub_total'] = $detail->sub_total;
                $data['sku'] = $detail->product_sku;
                $data['source'] = $detail->source;
                $data['purchase_price'] = $detail->purchase_price;
                $data['selling_price'] = $detail->selling_price;
                if ($detail->variant_id) {
                    $data['variant']['attribute'] = $detail->attributes;
                    $data['variant']['options'] = $detail->options ?? [];
                }
            }
            // Handle service
            elseif ($detail->service_id) {
                $service = Service::find($detail->service_id);
                if (!$service) continue;
                $data['id'] = $service->id;
                $data['name'] = $service->name;
                $data['type'] = 'service';
                $data['image'] = $service->singleImage ?? '';
                $data['qty'] = $detail->quantity;
                $data['price'] = $detail->price;
                $data['sub_total'] = $detail->sub_total;
                $data['sku'] = $detail->product_sku ?? '';
                $data['source'] = $detail->source;
                $data['purchase_price'] = $detail->purchase_price ?? 0;
                $data['selling_price'] = $detail->selling_price ?? $detail->price;
            }
            else {
                continue; // Skip invalid entries
            }

            $cart_contents[$data["rowid"]] = $data;
        }

        // Store cart in session
        session()->put('UPDATE_CART', $cart_contents);

        return [$cart_contents, $sale];
    }

    public function getLedger($request, $id, $isPaid = 1, $type)
    {
        $sale = $this->sale->find($id);
        $ledger = Ledger::where('customer_id', $request->order_customer_id)
            ->where('invoice_type', $type)
            ->where('invoice_no', $sale->invoice)
            ->where('is_received', $isPaid)
            ->first();

        return $ledger;
    }

    public function salesLedger($request, $sale, $paid, $total_amount = 0, $type = 'sale', $isPaid = 1, $dueAmount = 0, $ledger = null)
    {
        if ($ledger == null) $ledger = new Ledger();
        $isGuestLedger = !$request->order_customer_id || $request->order_customer_id == 'walk-in-customer';
        $ledger->customer_id = $isGuestLedger ? null : $request->order_customer_id;
        $ledger->amount = $paid;
        $ledger->invoice_type = $type;
        $ledger->is_received = $isPaid;
        $ledger->invoice_url = route('admin.sales.invoice', $sale->id);
        $ledger->invoice_no = $sale->invoice;
        $ledger->note = $request->note;
        $ledger->due_amount = $dueAmount;
        $ledger->total_amount = $total_amount;
        $ledger->date = $this->parseDate($request->sale_date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();
    }

    /**
     * Deduct ingredient stock based on menu item recipe
     * When a menu item is sold, deduct stock from all ingredients in its recipe
     */
    private function deductIngredientStockFromRecipe(MenuItem $menuItem, $quantity, Sale $sale, Request $request)
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

            // Create stock record for tracking
            Stock::create([
                'sale_id' => $sale->id,
                'ingredient_id' => $ingredient->id,
                'unit_id' => $ingredient->consumption_unit_id,
                'date' => $this->parseDate($request->sale_date),
                'type' => 'Sale',
                'invoice' => route('admin.sales.invoice', $sale->id),
                'out_quantity' => $deductQuantity,
                'base_out_quantity' => $deductInPurchaseUnit,
                'sku' => $ingredient->sku,
                'purchase_price' => $ingredient->purchase_price ?? 0,
                'sale_price' => 0,
                'rate' => $ingredient->consumption_unit_cost ?? 0,
                'profit' => 0,
                'created_by' => auth('admin')->user()->id,
            ]);
        }
    }

    /**
     * Restore ingredient stock based on menu item recipe (for sale cancellation/return)
     */
    private function restoreIngredientStockFromRecipe(MenuItem $menuItem, $quantity, ?Sale $sale = null)
    {
        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            if (!$ingredient) continue;

            // Calculate quantity to restore
            $restoreQuantity = $recipe->quantity_required * $quantity;

            // Convert to purchase unit
            $conversionRate = $ingredient->conversion_rate ?? 1;
            $restoreInPurchaseUnit = $restoreQuantity / $conversionRate;

            // Update ingredient stock using safe method
            $ingredient->addStock($restoreQuantity, $ingredient->consumption_unit_id);

            // Create stock record for audit trail
            Stock::create([
                'sale_id' => $sale->id ?? null,
                'ingredient_id' => $ingredient->id,
                'unit_id' => $ingredient->consumption_unit_id,
                'date' => now(),
                'type' => 'Sale Reversal',
                'invoice' => $sale->invoice ?? null,
                'in_quantity' => $restoreQuantity,
                'base_in_quantity' => $restoreInPurchaseUnit,
                'out_quantity' => 0,
                'base_out_quantity' => 0,
                'sku' => $ingredient->sku,
                'purchase_price' => $ingredient->purchase_price ?? 0,
                'average_cost' => $ingredient->average_cost ?? 0,
                'created_by' => auth('admin')->id(),
            ]);
        }
    }

    /**
     * Deduct ingredient stock for addons in a sale
     * Addons are stored as JSON array: [{id, name, price, qty}, ...]
     *
     * @param array|null $addons The addons array from cart item
     * @param int $itemQuantity The menu item quantity (addon stock = addon_qty * item_qty)
     * @param Sale $sale The sale record
     * @param mixed $date The sale date
     */
    private function deductAddonStock(?array $addons, int $itemQuantity, Sale $sale, $date = null)
    {
        if (empty($addons)) return;

        foreach ($addons as $addon) {
            $addonId = $addon['id'] ?? null;
            $addonQty = $addon['qty'] ?? 1;
            if (!$addonId) continue;

            $addonModel = MenuAddon::with('recipes.ingredient')->find($addonId);
            if (!$addonModel || $addonModel->recipes->isEmpty()) continue;

            $totalAddonQty = $addonQty * $itemQuantity;

            foreach ($addonModel->recipes as $recipe) {
                $ingredient = $recipe->ingredient;
                if (!$ingredient) continue;

                $deductQuantity = $recipe->quantity_required * $totalAddonQty;
                $conversionRate = $ingredient->conversion_rate ?? 1;
                $deductInPurchaseUnit = $deductQuantity / $conversionRate;

                $ingredient->deductStock($deductQuantity, $ingredient->consumption_unit_id);

                Stock::create([
                    'sale_id' => $sale->id,
                    'ingredient_id' => $ingredient->id,
                    'unit_id' => $ingredient->consumption_unit_id,
                    'date' => $date ?? now(),
                    'type' => 'Addon Sale',
                    'invoice' => route('admin.sales.invoice', $sale->id),
                    'out_quantity' => $deductQuantity,
                    'base_out_quantity' => $deductInPurchaseUnit,
                    'sku' => $ingredient->sku,
                    'purchase_price' => $ingredient->purchase_price ?? 0,
                    'sale_price' => 0,
                    'rate' => $ingredient->consumption_unit_cost ?? 0,
                    'profit' => 0,
                    'created_by' => auth('admin')->id(),
                ]);
            }
        }
    }

    /**
     * Restore ingredient stock for addons (for sale cancellation/return)
     */
    private function restoreAddonStock(?array $addons, int $itemQuantity, ?Sale $sale = null)
    {
        if (empty($addons)) return;

        foreach ($addons as $addon) {
            $addonId = $addon['id'] ?? null;
            $addonQty = $addon['qty'] ?? 1;
            if (!$addonId) continue;

            $addonModel = MenuAddon::with('recipes.ingredient')->find($addonId);
            if (!$addonModel || $addonModel->recipes->isEmpty()) continue;

            $totalAddonQty = $addonQty * $itemQuantity;

            foreach ($addonModel->recipes as $recipe) {
                $ingredient = $recipe->ingredient;
                if (!$ingredient) continue;

                $restoreQuantity = $recipe->quantity_required * $totalAddonQty;
                $conversionRate = $ingredient->conversion_rate ?? 1;
                $restoreInPurchaseUnit = $restoreQuantity / $conversionRate;

                $ingredient->addStock($restoreQuantity, $ingredient->consumption_unit_id);

                Stock::create([
                    'sale_id' => $sale->id ?? null,
                    'ingredient_id' => $ingredient->id,
                    'unit_id' => $ingredient->consumption_unit_id,
                    'date' => now(),
                    'type' => 'Addon Sale Reversal',
                    'invoice' => $sale->invoice ?? null,
                    'in_quantity' => $restoreQuantity,
                    'base_in_quantity' => $restoreInPurchaseUnit,
                    'out_quantity' => 0,
                    'base_out_quantity' => 0,
                    'purchase_price' => $ingredient->purchase_price ?? 0,
                    'average_cost' => $ingredient->average_cost ?? 0,
                    'created_by' => auth('admin')->id(),
                ]);
            }
        }
    }

    /**
     * Calculate COGS (Cost of Goods Sold) for a menu item based on its recipe
     * Uses weighted average cost (average_cost) from ingredients
     *
     * @param MenuItem $menuItem
     * @param float $quantity Number of menu items sold
     * @return float Total COGS for this sale line item
     */
    private function calculateMenuItemCOGS(MenuItem $menuItem, float $quantity): float
    {
        $totalCOGS = 0;

        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            if (!$ingredient) continue;

            // Use consumption_unit_cost (derived from average_cost)
            // consumption_unit_cost = average_cost / conversion_rate
            $costPerUnit = $ingredient->consumption_unit_cost ?? 0;

            // COGS for this ingredient = quantity_required × sale_quantity × cost_per_unit
            $ingredientCOGS = $recipe->quantity_required * $quantity * $costPerUnit;
            $totalCOGS += $ingredientCOGS;
        }

        return $totalCOGS;
    }

    /**
     * Update COGS totals on a sale record
     * Called after all line items are processed
     *
     * @param Sale $sale
     */
    private function updateSaleCOGSTotals(Sale $sale): void
    {
        $totalCOGS = 0;
        $totalRevenue = 0;

        foreach ($sale->details as $detail) {
            $totalCOGS += $detail->cogs_amount ?? 0;
            $totalRevenue += $detail->sub_total ?? 0;
        }

        $grossProfit = $totalRevenue - $totalCOGS;
        $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        $sale->total_cogs = $totalCOGS;
        $sale->gross_profit = $grossProfit;
        $sale->profit_margin = $profitMargin;
        $sale->save();
    }
}
