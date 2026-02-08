<?php
namespace Modules\Purchase\app\Services;

use App\Helpers\UnitConverter;
use App\Models\Ledger;
use App\Models\Payment;
use App\Models\Stock;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Services\IngredientService;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Models\PurchaseReturn;
use Modules\Purchase\app\Models\PurchaseReturnDetails;
use Modules\Purchase\app\Models\PurchaseReturnType;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;

class PurchaseService
{

    public function __construct(
        private Purchase $purchase,
        private PurchaseDetails $purchaseDetails,
        private IngredientService $ingredientService,
        private Supplier $supplier,
        private Warehouse $warehouse,
        private Ingredient $ingredient,
        private AccountsService $accountsService,
        private PurchaseReturn $purchaseReturn,
        private PurchaseReturnDetails $purchaseReturnDetials,
    ) {}

    public function all()
    {
        $purchase = $this->purchase->with('supplier', 'warehouse')->orderBy('purchase_date', 'desc');

        if (request()->has('keyword')) {
            $purchase = $purchase->where(function ($query) {
                $query->where('invoice_number', 'like', '%' . request()->keyword . '%')
                    ->orWhere('memo_no', 'like', '%' . request()->keyword . '%')
                    ->orWhere('reference_no', 'like', '%' . request()->keyword . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('name', 'like', '%' . request()->keyword . '%')
                            ->orWhere('company', 'like', '%' . request()->keyword . '%');
                    });
            });
        }
        if (request()->supplier_id) {
            $purchase = $purchase->where('supplier_id', request()->supplier_id);
        }

        if (request('from_date') && request('to_date')) {
            $purchase = $purchase->whereBetween('purchase_date', [now()->parse(request('from_date')), now()->parse(request('to_date'))]);
        }
        if (request()->ingredient_id || request()->product_id) {
            $ingredientId = request('ingredient_id') ?? request('product_id');
            $purchase = $purchase->whereHas('purchaseDetails', function ($q) use ($ingredientId) {
                $q->where('ingredient_id', $ingredientId);
            });
        }
        if (request('order_by')) {
            $purchase = $purchase->reorder('purchase_date', request('order_by'));
        }
        return $purchase;
    }

    public function allReturn()
    {
        return $this->purchaseReturn->with('purchase', 'returnType', 'purchaseDetails')->latest();
    }

    public function store($request)
    {
        $attachment_name = null;
        if ($request->hasFile('attachment')) {
            $attachment      = $request->file('attachment');
            $attachment_name = time() . '.' . $attachment->getClientOriginalExtension();
            $attachment->move(public_path('uploads/purchase/'), $attachment_name);
        }
        $purchase                 = new Purchase();
        $paidAmount               = $request->total_amount - $request->due_amount;
        $purchase->supplier_id    = $request->supplier_id;
        $purchase->warehouse_id   = $request->warehouse_id;
        $purchase->invoice_number = $request->invoice_number;
        $purchase->memo_no        = $request->memo_no;
        $purchase->reference_no   = $request->reference_no;
        $purchase->purchase_date  = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
        $purchase->items          = $request->items;
        $purchase->attachment     = $attachment_name;
        $purchase->total_amount   = $request->total_amount;
        $purchase->paid_amount    = $paidAmount;
        $purchase->due_amount     = $request->due_amount;
        $purchase->payment_status = $paidAmount == $request->total_amount ? 'paid' : 'due';
        $purchase->payment_type   = $request->payment_type;
        $purchase->note           = $request->note;
        $purchase->created_by     = Auth::id();
        $purchase->save();

        $this->purchaseLedger($request, $purchase->id, $paidAmount, $request->total_amount, 'purchase', 1, $request->due_amount);

        foreach ($request->ingredient_id as $index => $id) {
            $ingredient = Ingredient::find($id);
            if (!$ingredient) {
                continue;
            }

            // Get the unit used for this purchase (from form, or ingredient's default)
            $purchaseUnitId = $request->purchase_unit_id[$index] ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id;
            $quantity = (float) $request->quantity[$index];
            $unitPrice = (float) $request->unit_price[$index];

            // Get ingredient's stock unit (purchase_unit_id is where we store stock)
            $stockUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;

            // Convert quantity to stock unit if different
            $stockQuantity = $quantity;
            $pricePerStockUnit = $unitPrice;

            if ($purchaseUnitId && $stockUnitId && $purchaseUnitId != $stockUnitId) {
                // Convert quantity to stock units
                $stockQuantity = UnitConverter::safeConvert($quantity, $purchaseUnitId, $stockUnitId);

                // Convert price to per stock unit
                // If we bought 5L at ₹100/L, and stock is in ml, price per ml = ₹100/1000 = ₹0.10
                if ($stockQuantity > 0) {
                    $pricePerStockUnit = ($quantity * $unitPrice) / $stockQuantity;
                }
            }

            // Get base unit quantity for accurate tracking
            $baseUnitId = UnitConverter::getBaseUnitId($stockUnitId);
            $baseQuantity = UnitConverter::safeConvert($stockQuantity, $stockUnitId, $baseUnitId);

            // Create purchase details record
            $purchaseDetails                 = new PurchaseDetails();
            $purchaseDetails->purchase_id    = $purchase->id;
            $purchaseDetails->ingredient_id  = $id;
            $purchaseDetails->unit_id        = $purchaseUnitId;
            $purchaseDetails->quantity       = $quantity;
            $purchaseDetails->base_quantity  = $stockQuantity; // Store in stock units
            $purchaseDetails->purchase_price = $unitPrice;
            $purchaseDetails->sale_price     = 0;
            $purchaseDetails->sub_total      = $request->total[$index];
            $purchaseDetails->profit         = 0;
            $purchaseDetails->created_by     = Auth::id();
            $purchaseDetails->save();

            // Calculate weighted average cost (in stock units)
            $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
            $oldAvgCost = (float) ($ingredient->average_cost ?? $ingredient->getRawOriginal('purchase_price') ?? 0);

            if (($oldStock + $stockQuantity) > 0) {
                $newAvgCost = (($oldStock * $oldAvgCost) + ($stockQuantity * $pricePerStockUnit)) / ($oldStock + $stockQuantity);
            } else {
                $newAvgCost = $pricePerStockUnit;
            }

            // Update ingredient - stock is in purchase/stock units
            $ingredient->stock = $oldStock + $stockQuantity;
            $ingredient->average_cost = $newAvgCost;
            $ingredient->purchase_price = $pricePerStockUnit;
            $ingredient->cost = $pricePerStockUnit;

            // Update stock status
            if ($ingredient->stock <= 0) {
                $ingredient->stock_status = 'out_of_stock';
            } elseif ($ingredient->stock_alert && $ingredient->stock <= $ingredient->stock_alert) {
                $ingredient->stock_status = 'low_stock';
            } else {
                $ingredient->stock_status = 'in_stock';
            }

            $ingredient->save(); // This triggers boot() which recalculates consumption_unit_cost

            // Create stock record with full unit tracking
            Stock::create([
                'purchase_id'       => $purchase->id,
                'ingredient_id'     => $id,
                'warehouse_id'      => $request->warehouse_id,
                'unit_id'           => $purchaseUnitId,
                'date'              => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'type'              => 'Purchase',
                'invoice'           => route('admin.purchase.invoice', $purchase->id),
                'in_quantity'       => $quantity,
                'base_in_quantity'  => $baseQuantity,
                'sku'               => $ingredient->sku,
                'purchase_price'    => $unitPrice,
                'average_cost'      => $newAvgCost,
                'sale_price'        => 0,
                'rate'              => $unitPrice,
                'profit'            => 0,
                'created_by'        => auth('admin')->user()->id,
            ]);
        }

        // Create payments
        foreach ($request->payment_type as $key => $item) {
            if ($item == 'cash') {
                $account = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $account = Account::where('account_type', $item)
                    ->where('id', $request->account_id[$key])->first();
            }

            // Skip if no account found or no paid amount
            if (!$account || !$request->paid_amount[$key]) {
                continue;
            }

            $data = [
                'payment_type' => 'purchase',
                'purchase_id'  => $purchase->id,
                'is_paid'      => 1,
                'supplier_id'  => $request->supplier_id,
                'account_id'   => $account->id,
                'amount'       => $request->paid_amount[$key],
                'payment_date' => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'note'         => $request->note,
                'created_by'   => auth('admin')->user()->id,
                'account_type' => accountList()[$item],
                'invoice'      => $request->invoice_number,
            ];
            SupplierPayment::create($data);
        }

        return $purchase;
    }

    public function update($request, $id)
    {
        $purchase = $this->purchase->find($id);

        $attachment_name = null;
        if ($request->hasFile('attachment')) {
            $attachment      = $request->file('attachment');
            $attachment_name = file_upload($attachment, oldFile: $purchase->attachment);
        }

        $paidAmount               = $request->total_amount - $request->due_amount;
        $purchase->supplier_id    = $request->supplier_id;
        $purchase->warehouse_id   = $request->warehouse_id;
        $purchase->invoice_number = $request->invoice_number;
        $purchase->memo_no        = $request->memo_no;
        $purchase->reference_no   = $request->reference_no;
        $purchase->purchase_date  = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
        $purchase->items          = $request->items;
        $purchase->attachment     = $attachment_name;
        $purchase->total_amount   = $request->total_amount;
        $purchase->paid_amount    = $paidAmount;
        $purchase->due_amount     = $request->due_amount;
        $purchase->payment_status = $paidAmount == $request->total_amount ? 'paid' : 'due';
        $purchase->payment_type   = $request->payment_type;
        $purchase->note           = $request->note;
        $purchase->updated_by     = Auth::id();
        $purchase->save();

        $ledger = $this->getLedger($request, $id, 'purchase', 1);

        $this->purchaseLedger($request, $purchase->id, $paidAmount, $request->total_amount, 'purchase', 1, $request->due_amount, $ledger);

        // Restore ingredient stock using base_quantity (stock units)
        foreach ($purchase->purchaseDetails as $purchaseDetail) {
            $ingredient = Ingredient::find($purchaseDetail->ingredient_id);
            if ($ingredient) {
                $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
                // Use base_quantity which is stored in stock units
                $restoreQty = (float) ($purchaseDetail->base_quantity ?? $purchaseDetail->quantity);
                $ingredient->stock = max(0, $oldStock - $restoreQty);
                $ingredient->save();
            }
        }

        // Delete old purchase details (only delete 'purchase' type payments, preserve due_pay records)
        $purchase->purchaseDetails()->delete();
        $purchase->payments()->where('payment_type', 'purchase')->delete();
        $purchase->stock()->delete();

        // Store new purchase details with unit conversion
        foreach ($request->ingredient_id as $index => $id) {
            $ingredient = Ingredient::find($id);
            if (!$ingredient) {
                continue;
            }

            // Get the unit used for this purchase
            $purchaseUnitId = $request->purchase_unit_id[$index] ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id;
            $quantity = (float) $request->quantity[$index];
            $unitPrice = (float) $request->unit_price[$index];

            // Get ingredient's stock unit
            $stockUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;

            // Convert quantity to stock unit if different
            $stockQuantity = $quantity;
            $pricePerStockUnit = $unitPrice;

            if ($purchaseUnitId && $stockUnitId && $purchaseUnitId != $stockUnitId) {
                $stockQuantity = UnitConverter::safeConvert($quantity, $purchaseUnitId, $stockUnitId);

                if ($stockQuantity > 0) {
                    $pricePerStockUnit = ($quantity * $unitPrice) / $stockQuantity;
                }
            }

            // Get base unit quantity
            $baseUnitId = UnitConverter::getBaseUnitId($stockUnitId);
            $baseQuantity = UnitConverter::safeConvert($stockQuantity, $stockUnitId, $baseUnitId);

            $purchaseDetails                 = new PurchaseDetails();
            $purchaseDetails->purchase_id    = $purchase->id;
            $purchaseDetails->ingredient_id  = $id;
            $purchaseDetails->unit_id        = $purchaseUnitId;
            $purchaseDetails->quantity       = $quantity;
            $purchaseDetails->base_quantity  = $stockQuantity;
            $purchaseDetails->purchase_price = $unitPrice;
            $purchaseDetails->sale_price     = 0;
            $purchaseDetails->sub_total      = $request->total[$index];
            $purchaseDetails->profit         = 0;
            $purchaseDetails->created_by     = Auth::id();
            $purchaseDetails->save();

            // Calculate weighted average cost
            $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
            $oldAvgCost = (float) ($ingredient->average_cost ?? $ingredient->getRawOriginal('purchase_price') ?? 0);

            if (($oldStock + $stockQuantity) > 0) {
                $newAvgCost = (($oldStock * $oldAvgCost) + ($stockQuantity * $pricePerStockUnit)) / ($oldStock + $stockQuantity);
            } else {
                $newAvgCost = $pricePerStockUnit;
            }

            // Update ingredient
            $ingredient->stock = $oldStock + $stockQuantity;
            $ingredient->average_cost = $newAvgCost;
            $ingredient->purchase_price = $pricePerStockUnit;
            $ingredient->cost = $pricePerStockUnit;

            // Update stock status
            if ($ingredient->stock <= 0) {
                $ingredient->stock_status = 'out_of_stock';
            } elseif ($ingredient->stock_alert && $ingredient->stock <= $ingredient->stock_alert) {
                $ingredient->stock_status = 'low_stock';
            } else {
                $ingredient->stock_status = 'in_stock';
            }

            $ingredient->save();

            // Create stock record
            Stock::create([
                'purchase_id'       => $purchase->id,
                'ingredient_id'     => $id,
                'warehouse_id'      => $request->warehouse_id,
                'unit_id'           => $purchaseUnitId,
                'date'              => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'type'              => 'Purchase',
                'invoice'           => route('admin.purchase.invoice', $purchase->id),
                'in_quantity'       => $quantity,
                'base_in_quantity'  => $baseQuantity,
                'sku'               => $ingredient->sku,
                'purchase_price'    => $unitPrice,
                'average_cost'      => $newAvgCost,
                'sale_price'        => 0,
                'rate'              => $unitPrice,
                'profit'            => 0,
                'created_by'        => auth('admin')->user()->id,
            ]);
        }

        // Create payments
        foreach ($request->payment_type as $key => $item) {
            if ($item == 'cash') {
                $account = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $account = Account::where('account_type', $item)
                    ->where('id', $request->account_id[$key])->first();
            }

            // Skip if no account found or no paid amount
            if (!$account || !$request->paid_amount[$key]) {
                continue;
            }

            $data = [
                'payment_type' => 'purchase',
                'purchase_id'  => $purchase->id,
                'is_paid'      => 1,
                'supplier_id'  => $request->supplier_id,
                'account_id'   => $account->id,
                'amount'       => $request->paid_amount[$key],
                'payment_date' => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'note'         => $request->note,
                'created_by'   => auth('admin')->user()->id,
                'invoice'      => $request->invoice_number,
                'account_type' => accountList()[$item],
            ];
            SupplierPayment::create($data);
        }

        return $purchase;
    }

    public function destroy($id)
    {
        $purchase = $this->purchase->find($id);

        // Restore ingredient stock using base_quantity (stock units)
        foreach ($purchase->purchaseDetails as $purchaseDetail) {
            $ingredient = Ingredient::find($purchaseDetail->ingredient_id);
            if ($ingredient) {
                $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
                // Use base_quantity which is stored in stock units
                $restoreQty = (float) ($purchaseDetail->base_quantity ?? $purchaseDetail->quantity);
                $ingredient->stock = max(0, $oldStock - $restoreQty);

                // Update stock status
                if ($ingredient->stock <= 0) {
                    $ingredient->stock_status = 'out_of_stock';
                } elseif ($ingredient->stock_alert && $ingredient->stock <= $ingredient->stock_alert) {
                    $ingredient->stock_status = 'low_stock';
                } else {
                    $ingredient->stock_status = 'in_stock';
                }

                $ingredient->save();
            }
        }

        PurchaseDetails::where('purchase_id', $id)?->delete();
        Stock::where('purchase_id', $id)?->delete();
        SupplierPayment::where('purchase_id', $id)?->delete();

        // Delete ledger entries for this purchase
        Ledger::whereIn('invoice_type', ['purchase', 'purchase payment'])
            ->where('invoice_no', $purchase->invoice_number)
            ->delete();

        return $purchase->delete();
    }

    public function genInvoiceNumber()
    {
        $setting = cache('setting');
        $number  = $setting->invoice_suffix ? $setting->invoice_suffix : 1;
        $prefix  = $setting->invoice_prefix ? $setting->invoice_prefix : 'INV-';

        $invoice_number = $prefix . $number;

        $purchase = $this->purchase->latest()->first();

        if ($purchase) {
            $purchaseInvoice = $purchase->invoice_number;

            // Extract numeric suffix after the prefix
            if (str_starts_with($purchaseInvoice, $prefix)) {
                $numericPart = substr($purchaseInvoice, strlen($prefix));
                $invoice_number = $prefix . ((int) $numericPart + 1);
            } else {
                // Fallback: get last numeric portion
                preg_match('/(\d+)$/', $purchaseInvoice, $matches);
                if (!empty($matches[1])) {
                    $invoice_number = $prefix . ((int) $matches[1] + 1);
                }
            }
        }

        return $invoice_number;
    }

    public function getPurchase($id)
    {
        return $this->purchase->with('supplier', 'warehouse', 'purchaseDetails.ingredient.unit', 'purchaseDetails.unit', 'payments')->find($id);
    }

    public function getPurchaseDetails($id)
    {
        return PurchaseDetails::with('ingredient', 'unit')->where('purchase_id', $id)->get();
    }

    public function getPurchaseList()
    {
        return $this->purchase->with('supplier', 'warehouse')->latest()->get();
    }

    public function getSuppliers()
    {
        return Supplier::where('status', 1)->orderBy('name', 'asc')->get();
    }

    public function getWarehouses()
    {
        return Warehouse::where('status', 1)->latest()->get();
    }

    public function getIngredients(Request $request)
    {
        $ingredients = $this->ingredientService->allActiveIngredients($request);
        return $ingredients->with(['unit', 'purchaseUnit', 'purchaseUnit.children', 'consumptionUnit'])->get();
    }

    // Alias for backward compatibility
    public function getProducts(Request $request)
    {
        return $this->getIngredients($request);
    }

    public function getAccounts()
    {
        return $this->accountsService->all()->get();
    }

    public function getPurchaseById($id)
    {
        return $this->purchase->find($id);
    }

    public function getReturnTypes()
    {
        return PurchaseReturnType::all();
    }

    public function storeReturn(Request $request, $id)
    {
        // Store purchase return
        $purchaseReturn = $this->purchaseReturn->create([
            'supplier_id'     => $request->supplier_id,
            'warehouse_id'    => $request->warehouse_id,
            'created_by'      => auth()->user()->id,
            'purchase_id'     => $request->purchase_id,
            'return_type_id'  => $request->return_type_id,
            'return_date'     => Carbon::createFromFormat('d-m-Y', $request->return_date),
            'note'            => $request->note,
            'payment_method'  => $request->payment_type,
            'received_amount' => $request->received_amount,
            'return_amount'   => $request->invoice_amount,
            'shipping_cost'   => $request->shipping_cost,
            'invoice'         => $this->returnInvoice(),
        ]);

        // Store purchase return details with unit conversion
        foreach ($request->ingredient_id as $index => $val) {
            $ingredient = Ingredient::find($val);
            if (!$ingredient) {
                continue;
            }

            $returnQuantity = (float) $request->return_quantity[$index];

            // Get unit from original purchase detail or use ingredient's default
            $returnUnitId = $request->return_unit_id[$index] ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id;

            // Get ingredient's stock unit
            $stockUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;

            // Convert return quantity to stock units
            $stockQuantity = $returnQuantity;
            if ($returnUnitId && $stockUnitId && $returnUnitId != $stockUnitId) {
                $stockQuantity = UnitConverter::safeConvert($returnQuantity, $returnUnitId, $stockUnitId);
            }

            // Get base unit quantity
            $baseUnitId = UnitConverter::getBaseUnitId($stockUnitId);
            $baseQuantity = UnitConverter::safeConvert($stockQuantity, $stockUnitId, $baseUnitId);

            $purchaseReturn->purchaseDetails()->create([
                'ingredient_id'  => $val,
                'purchase_id'    => $request->purchase_id,
                'unit_id'        => $returnUnitId,
                'quantity'       => $returnQuantity,
                'base_quantity'  => $stockQuantity,
                'total'          => $request->return_subtotal[$index],
            ]);

            // Update ingredient stock (subtract returned quantity)
            $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
            $ingredient->stock = max(0, $oldStock - $stockQuantity);

            // Update stock status
            if ($ingredient->stock <= 0) {
                $ingredient->stock_status = 'out_of_stock';
            } elseif ($ingredient->stock_alert && $ingredient->stock <= $ingredient->stock_alert) {
                $ingredient->stock_status = 'low_stock';
            } else {
                $ingredient->stock_status = 'in_stock';
            }

            $ingredient->save();

            // Create stock record
            Stock::create([
                'invoice'            => $purchaseReturn->invoice,
                'purchase_return_id' => $purchaseReturn->id,
                'type'               => 'Purchase Return',
                'ingredient_id'      => $val,
                'warehouse_id'       => $request->warehouse_id,
                'unit_id'            => $returnUnitId,
                'date'               => now(),
                'out_quantity'       => $returnQuantity,
                'base_out_quantity'  => $baseQuantity,
                'sku'                => $ingredient->sku,
                'created_by'         => auth('admin')->user()->id,
            ]);
        }

        if ($request->payment_type == 'cash') {
            $account = Account::firstOrCreate(
                ['account_type' => 'cash'],
                ['bank_account_name' => 'Cash Register']
            );
        } else {
            $account = Account::where('account_type', $request->payment_type)
                ->where('id', $request->account_id)->first();
        }

        if ($request->received_amount) {
            // Create ledger
            $ledger = $this->purchaseReturnLedger($request, $purchaseReturn->id, $request->received_amount, 'purchase return', 0);
            SupplierPayment::create([
                'payment_type'       => 'purchase_receive',
                'purchase_return_id' => $purchaseReturn->id,
                'supplier_id'        => $purchaseReturn->supplier_id,
                'account_id'         => $account->id,
                'is_received'        => 1,
                'account_type'       => accountList()[$request->payment_type],
                'amount'             => $request->received_amount,
                'payment_date'       => now(),
                'created_by'         => auth()->user()->id,
                'ledger_id'          => $ledger->id,
            ]);
        }

        return $purchaseReturn;
    }

    public function updateReturn($request, $id)
    {
        $return = $this->purchaseReturn->find($id);

        $return->update([
            'supplier_id'     => $request->supplier_id,
            'warehouse_id'    => $request->warehouse_id,
            'return_type_id'  => $request->return_type_id,
            'return_date'     => Carbon::createFromFormat('d-m-Y', $request->return_date),
            'note'            => $request->note,
            'payment_method'  => $request->payment_type,
            'received_amount' => $request->received_amount,
            'return_amount'   => $request->invoice_amount,
            'shipping_cost'   => $request->shipping_cost,
        ]);

        // Restore ingredient stock using base_quantity
        foreach ($return->purchaseDetails as $purchaseDetail) {
            $ingredient = Ingredient::find($purchaseDetail->ingredient_id);
            if ($ingredient) {
                $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
                $restoreQty = (float) ($purchaseDetail->base_quantity ?? $purchaseDetail->quantity);
                $ingredient->stock = $oldStock + $restoreQty;

                // Update stock status
                if ($ingredient->stock <= 0) {
                    $ingredient->stock_status = 'out_of_stock';
                } elseif ($ingredient->stock_alert && $ingredient->stock <= $ingredient->stock_alert) {
                    $ingredient->stock_status = 'low_stock';
                } else {
                    $ingredient->stock_status = 'in_stock';
                }

                $ingredient->save();
            }
        }

        // Delete old purchase details, payment, ledger, and stock
        $return->purchaseDetails()->delete();
        $return->payment()?->delete();
        // Delete old ledger entries for this return
        Ledger::where('invoice_type', 'purchase return')
            ->where('invoice_no', $return->purchase->invoice_number ?? '')
            ->delete();
        Stock::where('purchase_return_id', $return->id)?->delete();

        // Store new return details (similar to storeReturn)
        foreach ($request->ingredient_id as $index => $val) {
            $ingredient = Ingredient::find($val);
            if (!$ingredient) {
                continue;
            }

            $returnQuantity = (float) $request->return_quantity[$index];
            $returnUnitId = $request->return_unit_id[$index] ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id;
            $stockUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;

            $stockQuantity = $returnQuantity;
            if ($returnUnitId && $stockUnitId && $returnUnitId != $stockUnitId) {
                $stockQuantity = UnitConverter::safeConvert($returnQuantity, $returnUnitId, $stockUnitId);
            }

            $baseUnitId = UnitConverter::getBaseUnitId($stockUnitId);
            $baseQuantity = UnitConverter::safeConvert($stockQuantity, $stockUnitId, $baseUnitId);

            $return->purchaseDetails()->create([
                'ingredient_id'  => $val,
                'purchase_id'    => $request->purchase_id,
                'unit_id'        => $returnUnitId,
                'quantity'       => $returnQuantity,
                'base_quantity'  => $stockQuantity,
                'total'          => $request->return_subtotal[$index],
            ]);

            // Update ingredient stock
            $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
            $ingredient->stock = max(0, $oldStock - $stockQuantity);

            if ($ingredient->stock <= 0) {
                $ingredient->stock_status = 'out_of_stock';
            } elseif ($ingredient->stock_alert && $ingredient->stock <= $ingredient->stock_alert) {
                $ingredient->stock_status = 'low_stock';
            } else {
                $ingredient->stock_status = 'in_stock';
            }

            $ingredient->save();

            Stock::create([
                'invoice'            => $return->invoice,
                'purchase_return_id' => $return->id,
                'type'               => 'Purchase Return',
                'ingredient_id'      => $val,
                'warehouse_id'       => $request->warehouse_id,
                'unit_id'            => $returnUnitId,
                'date'               => now(),
                'out_quantity'       => $returnQuantity,
                'base_out_quantity'  => $baseQuantity,
                'sku'                => $ingredient->sku,
                'created_by'         => auth('admin')->user()->id,
            ]);
        }

        // Recreate payment and ledger for the updated return
        if ($request->received_amount) {
            if ($request->payment_type == 'cash') {
                $account = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $account = Account::where('account_type', $request->payment_type)
                    ->where('id', $request->account_id)->first();
            }

            if ($account) {
                // Create ledger entry for the return
                $ledger = $this->purchaseReturnLedger($request, $return->id, $request->received_amount, 'purchase return', 0);

                SupplierPayment::create([
                    'payment_type'       => 'purchase_receive',
                    'purchase_return_id' => $return->id,
                    'supplier_id'        => $return->supplier_id,
                    'account_id'         => $account->id,
                    'is_received'        => 1,
                    'account_type'       => accountList()[$request->payment_type],
                    'amount'             => $request->received_amount,
                    'payment_date'       => now(),
                    'created_by'         => auth()->user()->id,
                    'ledger_id'          => $ledger->id,
                ]);
            }
        }

        return $return;
    }

    public function getPurchaseReturn($id)
    {
        return $this->purchaseReturn->with('supplier', 'purchaseDetails.ingredient', 'purchaseDetails.unit')->find($id);
    }

    public function purchaseLedger($request, $id, $paid, $total_amount = 0, $type = 'purchase', $isPaid = 1, $dueAmount = 0, $ledger = null)
    {
        if ($ledger == null) {
            $ledger = new Ledger();
        }

        $ledger->supplier_id  = $request->supplier_id;
        $ledger->amount       = $paid;
        $ledger->invoice_type = $type;
        $ledger->is_paid      = $isPaid;
        $ledger->invoice_url  = route('admin.purchase.invoice', $id);
        $ledger->invoice_no   = $request->invoice_number;
        $ledger->note         = $request->note;
        $ledger->due_amount   = $dueAmount;
        $ledger->total_amount = $total_amount;
        $ledger->date         = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
        $ledger->created_by   = auth('admin')->user()->id;
        $ledger->save();
    }

    public function purchaseReturnLedger($request, $id, $paid, $type = 'purchase_return', $isPaid = 0, $dueAmount = 0, $ledger = null)
    {
        if ($ledger == null) {
            $ledger = new Ledger();
        }

        $ledger->supplier_id  = $request->supplier_id;
        $ledger->amount       = $paid;
        $ledger->invoice_type = $type;
        $ledger->is_paid      = $isPaid;
        $ledger->is_received  = 1;
        $ledger->invoice_url  = route('admin.purchase.return.invoice', $id);
        $ledger->invoice_no   = $request->invoice_number;
        $ledger->note         = $request->note;
        $ledger->due_amount   = $dueAmount;
        $ledger->date         = Carbon::createFromFormat('d-m-Y', $request->return_date);
        $ledger->created_by   = auth('admin')->user()->id;
        $ledger->save();

        return $ledger;
    }

    public function getLedger($request, $id, $type, $isPaid = 1)
    {
        $purchase = $this->purchase->find($id);
        $ledger   = Ledger::where('supplier_id', $request->supplier_id)
            ->where('invoice_type', $type)
            ->where('invoice_no', $purchase->invoice_number)
            ->where('is_paid', $isPaid)
            ->first();

        return $ledger;
    }

    public function updateLedger($request, $id, $paidAmount, $type = 'purchase', $isPaid = 1)
    {
        $purchase = $this->purchase->find($id);

        // Check if ledger already exist
        $ledger = Ledger::where('supplier_id', $request->supplier_id)
            ->where('invoice_type', 'purchase')
            ->where('invoice_no', $purchase->invoice_number)
            ->where('is_paid', $isPaid)
            ->first();

        if (! $ledger) {
            $ledger = new Ledger();
        }

        $ledger->supplier_id  = $request->supplier_id;
        $ledger->amount       = $paidAmount;
        $ledger->invoice_type = $type;
        $ledger->is_paid      = 1;
        $ledger->invoice_url  = route('admin.purchase.invoice', $purchase->id);
        $ledger->invoice_no   = $request->invoice_number;
        $ledger->note         = $request->note;
        $ledger->due_amount   = $request->due_amount ?? 0;
        $ledger->date         = Carbon::createFromFormat('d-m-Y', $request->purchase_date);
        $ledger->created_by   = auth('admin')->user()->id;
        $ledger->save();
    }

    public function purchaseReturnCreateLedger($request, $id, $paidAmount, $type = 'purchase', $isPaid = 1)
    {
        $this->updateLedger($request, $id, $paidAmount, $type, $isPaid);
    }

    public function deleteReturn($id)
    {
        $return = $this->purchaseReturn->find($id);

        // Restore ingredient stock using base_quantity
        foreach ($return->purchaseDetails as $purchaseDetail) {
            $ingredient = Ingredient::find($purchaseDetail->ingredient_id);
            if ($ingredient) {
                $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
                $restoreQty = (float) ($purchaseDetail->base_quantity ?? $purchaseDetail->quantity);
                $ingredient->stock = $oldStock + $restoreQty;

                if ($ingredient->stock <= 0) {
                    $ingredient->stock_status = 'out_of_stock';
                } elseif ($ingredient->stock_alert && $ingredient->stock <= $ingredient->stock_alert) {
                    $ingredient->stock_status = 'low_stock';
                } else {
                    $ingredient->stock_status = 'in_stock';
                }

                $ingredient->save();
            }
        }

        Stock::where('purchase_return_id', $return->id)?->delete();
        $return->payment()?->delete();
        // Delete ledger entries for this return
        Ledger::where('invoice_type', 'purchase return')
            ->where('invoice_no', $return->purchase->invoice_number ?? '')
            ->delete();
        $return->purchaseDetails()->delete();
        $return->delete();
    }

    public function returnInvoice($id = 0)
    {
        $number         = 1;
        $prefix         = 'RET-';
        $invoice_number = $prefix . $number;

        $return = $this->purchaseReturn->latest()->first();
        if ($return) {
            $purchaseInvoice = $return->invoice;

            // Split the invoice number
            $split_invoice  = explode($prefix, $purchaseInvoice);
            if (count($split_invoice) > 1) {
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }
}
