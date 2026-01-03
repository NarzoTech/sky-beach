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
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\MenuVariant;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Service\app\Models\Service;

class SaleService
{
    public function __construct(private Sale $sale) {}

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
        return $this->sale->with('products', 'customer', 'services', 'details', 'payment', 'saleReturns');
    }
    public function createSale(Request $request, $user, $cart): Sale
    {

        $sale = new Sale();
        $sale->user_id = $user != null ?  $user->id : null;

        $sale->customer_id = $request->order_customer_id;
        $sale->warehouse_id = 1;
        $sale->quantity = 1;
        $sale->total_price = $request->sub_total;
        $sale->order_date = Carbon::createFromFormat('d-m-Y', $request->sale_date);
        $sale->status = 1;
        $sale->payment_status = 1;

        $sale->payment_method = json_encode($request->payment_type);
        $sale->order_discount = $request->discount_amount;
        $sale->total_tax = $request->total_tax ?? 0;
        $sale->grand_total = $request->total_amount;
        $sale->invoice = $this->genInvoiceNumber();

        $sale->paid_amount = array_sum($request->paying_amount);
        $sale->receive_amount = $request->receive_amount;
        $sale->return_amount = $request->return_amount;
        $due = $request->total_amount - array_sum($request->paying_amount);
        $sale->due_amount = $due < 0 ? 0 : $due;
        $sale->due_date = $request->due_date ? $this->parseDate($request->due_date) : null;
        $sale->sale_note = $request->remark;
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
                $orderDetails->sub_total = $item['sub_total'];
                $orderDetails->attributes = $menuVariant ? $menuVariant->name : null;
                $orderDetails->save();

                // Deduct ingredient stock based on menu item recipes
                if ($menuItem && $item['source'] == 1) {
                    $this->deductIngredientStockFromRecipe($menuItem, $saleQuantity, $sale, $request);
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
                    $product->stock = $product->stock - $baseQuantity;
                    $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                    $product->save();

                    // create stock with unit tracking
                    $purchasePrice = $product->last_purchase_price ?? 0;
                    Stock::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'unit_id' => $saleUnitId,
                        'date' => Carbon::createFromFormat('d-m-Y', $request->sale_date),
                        'type' => 'Sale',
                        'invoice' => route('admin.sales.invoice', $sale->id),
                        'invoice_number' => $sale->invoice,
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
        $sale->save();


        // create payments
        foreach ($request->payment_type as $key => $item) {
            $account = Account::where('account_type', $item);
            if ($item == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
            }
            $customerId = $request->order_customer_id;
            $data = [
                'payment_type' => 'sale',
                'sale_id' => $sale->id,
                'is_received' => 1,
                'customer_id' => $request->order_customer_id,
                'account_id' => $account->id,
                'amount' => $request->paying_amount[$key],
                'payment_date' => Carbon::createFromFormat('d-m-Y', $request->sale_date),
                'created_by' => auth('admin')->user()->id,
            ];
            if ($customerId == 'walk-in-customer') {
                $data['customer_id'] = null;
                $data['is_guest'] = 1;
            }
            if ($request->paying_amount[$key]) {
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


        // if user is exists

        if ($user) {
            // $this->updateLedger($request, $sale->id, $user, 'sale');
            $this->salesLedger($request, $sale, array_sum($request->paying_amount), $request->total_amount, 'sale', 1, $due);
        }
        return $sale;
    }

    public function updateSale(Request $request, $user, $cart, $id): Sale
    {
        DB::beginTransaction();
        try {
            $sale = $this->sale->find($id);


            // update sales
            $sale->user_id = $user != null ?  $user->id : null;
            $sale->customer_id = $request->order_customer_id;
            $sale->warehouse_id = 1;
            $sale->total_price = $request->sub_total;
            $sale->order_date = $this->parseDate($request->sale_date);
            $sale->status = 1;
            $sale->payment_status = 1;

            $sale->payment_method = json_encode($request->payment_type);
            $sale->order_discount = $request->discount_amount;
            $sale->total_tax = $request->total_tax ?? 0;
            $sale->grand_total = $request->total_amount;
            $sale->paid_amount = array_sum($request->paying_amount);


            $due = $request->total_amount - array_sum($request->paying_amount);
            $sale->due_amount = $due < 0 ? 0 : $due;
            $sale->due_date = $request->due_date ? $this->parseDate($request->due_date) : null;
            $sale->sale_note = $request->remark;
            $sale->receive_amount = $request->receive_amount;
            $sale->return_amount = $request->return_amount;
            $sale->updated_by = auth('admin')->user()->id;

            // restore ingredient stock from menu items via recipes
            foreach ($sale->menuItems as $item) {
                $menuItem = MenuItem::with('recipes.ingredient')->find($item->menu_item_id);
                if ($menuItem && $item->source == 1) {
                    $this->restoreIngredientStockFromRecipe($menuItem, $item->quantity);
                }
            }

            // restore product stock using base quantity (for legacy ingredient sales)
            foreach ($sale->products as $item) {
                $product = Ingredient::where('id', $item->ingredient_id)->first();
                if ($product != null && $item->source == 1) {
                    $restoreQty = $item->base_quantity ?? $item->quantity;
                    $product->stock = $product->stock + $restoreQty;
                    $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                    $product->save();
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
                    $orderDetails->sub_total = $item['sub_total'];
                    $orderDetails->attributes = $menuVariant ? $menuVariant->name : null;
                    $orderDetails->save();

                    // Deduct ingredient stock based on menu item recipes
                    if ($menuItem && ($item['source'] ?? 1) == 1) {
                        $this->deductIngredientStockFromRecipe($menuItem, $saleQuantity, $sale, $request);
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
                        $product->stock = $product->stock - $baseQuantity;
                        $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                        $product->save();

                        // create stock with unit tracking
                        $purchasePrice = $product->last_purchase_price ?? 0;
                        Stock::create([
                            'sale_id' => $sale->id,
                            'product_id' => $product->id,
                            'unit_id' => $saleUnitId,
                            'date' => $this->parseDate($request->sale_date),
                            'type' => 'Sale',
                            'invoice' => route('admin.sales.invoice', $sale->id),
                            'invoice_number' => $sale->invoice,
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
            $sale->save();

            $ledger = $this->getLedger($request, $id, 1, 'sale');

            $this->salesLedger($request, $sale, array_sum($request->paying_amount), $request->total_amount, 'sale', 1, $due, $ledger);

            // create payments
            foreach ($request->payment_type as $key => $item) {
                $account = Account::where('account_type', $item);
                if ($item == 'cash') {
                    $account = $account->first();
                } else {
                    $account = $account->where('id', $request->account_id[$key])->first();
                }
                $customerId = $request->order_customer_id;
                $data = [
                    'payment_type' => 'sale',
                    'sale_id' => $sale->id,
                    'is_received' => 1,
                    'customer_id' => $request->order_customer_id,
                    'account_id' => $account->id,
                    'amount' => $request->paying_amount[$key],
                    'payment_date' => $this->parseDate($request->sale_date),
                    'created_by' => auth('admin')->user()->id,
                ];
                if ($customerId == 'walk-in-customer') {
                    $data['customer_id'] = null;
                    $data['is_guest'] = 1;
                }
                if ($request->paying_amount[$key]) {
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
                $this->restoreIngredientStockFromRecipe($menuItem, $item->quantity);
            }
        }

        // restore product stock (legacy ingredient sales)
        foreach ($sale->products as $item) {
            $product = Ingredient::where('id', $item->ingredient_id)->first();
            if ($product != null && $item->source == 1) {
                $restoreQty = $item->base_quantity ?? $item->quantity;
                $product->stock = $product->stock + $restoreQty;
                $product->stock_status = $product->stock <= 0 ? 'out_of_stock' : 'in_stock';
                $product->save();
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
            $invoice_number = (int) $split_invoice[1] + 1;
            $invoice_number = $prefix . $invoice_number;
        }

        return $invoice_number;
    }
    public function editSale($id)
    {
        $sale = $this->getSales()->find($id);

        foreach ($sale->details as $key => $detail) {
            $data = array();
            $data["rowid"] = uniqid();

            // Handle menu item
            if ($detail->menu_item_id) {
                $menuItem = MenuItem::find($detail->menu_item_id);
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
                if ($detail->variant_id) {
                    $data['variant']['attribute'] = $detail->attributes;
                    $data['variant']['options'] = [];
                }
            }
            // Handle legacy ingredient (product)
            elseif ($detail->ingredient_id) {
                $product = Ingredient::find($detail->ingredient_id);
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

            $cart_contents = session()->get('UPDATE_CART');
            $cart_contents = $cart_contents ? $cart_contents : [];
            session()->put('UPDATE_CART', [...$cart_contents, $data["rowid"] => $data]);
        }
        $cart_contents = session()->get('UPDATE_CART');
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
        $ledger->customer_id = $request->order_customer_id;
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

            // Update ingredient stock
            $ingredient->stock = $ingredient->stock - $deductInPurchaseUnit;
            $ingredient->stock_status = $ingredient->stock <= 0 ? 'out_of_stock' : 'in_stock';
            $ingredient->save();

            // Create stock record for tracking
            Stock::create([
                'sale_id' => $sale->id,
                'product_id' => $ingredient->id,
                'unit_id' => $ingredient->consumption_unit_id,
                'date' => $this->parseDate($request->sale_date),
                'type' => 'Sale',
                'invoice' => route('admin.sales.invoice', $sale->id),
                'invoice_number' => $sale->invoice,
                'out_quantity' => $deductQuantity,
                'base_out_quantity' => $deductInPurchaseUnit,
                'sku' => $ingredient->sku,
                'purchase_price' => $ingredient->purchase_price ?? 0,
                'sale_price' => 0,
                'rate' => $ingredient->consumption_unit_cost ?? 0,
                'profit' => 0,
                'note' => 'Menu Item: ' . $menuItem->name,
                'created_by' => auth('admin')->user()->id,
            ]);
        }
    }

    /**
     * Restore ingredient stock based on menu item recipe (for sale cancellation/return)
     */
    private function restoreIngredientStockFromRecipe(MenuItem $menuItem, $quantity)
    {
        foreach ($menuItem->recipes as $recipe) {
            $ingredient = $recipe->ingredient;
            if (!$ingredient) continue;

            // Calculate quantity to restore
            $restoreQuantity = $recipe->quantity_required * $quantity;

            // Convert to purchase unit
            $conversionRate = $ingredient->conversion_rate ?? 1;
            $restoreInPurchaseUnit = $restoreQuantity / $conversionRate;

            // Update ingredient stock
            $ingredient->stock = $ingredient->stock + $restoreInPurchaseUnit;
            $ingredient->stock_status = $ingredient->stock <= 0 ? 'out_of_stock' : 'in_stock';
            $ingredient->save();
        }
    }
}
