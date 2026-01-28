<?php

namespace Modules\Sales\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\Payment;
use App\Models\Stock;
use App\Traits\RedirectHelperTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;
use Modules\Sales\app\Models\SalesReturnDetails;

class SalesReturnController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private AccountsService $service) {}
    /**
     * Display a listing of the resource.
     */
    public function returnList()
    {
        checkAdminHasPermissionAndThrowException('sales.return.list');
        $lists = SalesReturn::query();

        if (request()->keyword) {
            $returns = $lists->where(function ($q) {
                $q->where('invoice', 'like', '%' . request()->keyword . '%')
                    ->orWhereHas('customer', function ($q) {
                        $q->where('name', 'like', '%' . request()->keyword . '%');
                    })
                ;
            });
        }

        $lists = $lists->orderBy('id', request()->order_by ? request()->order_by : 'desc');


        if (request()->from_date && request()->to_date) {

            $lists = $lists->whereBetween('return_date', [now()->parse(request()->from_date), now()->parse(request()->to_date)]);
        }

        if (request()->customer) {

            $lists = $lists->where('customer_id', request()->customer);
        }

        $data = [];

        $data['totalAmount'] = 0;
        $data['paidAmount'] = 0;
        $data['totalDue'] = 0;
        foreach ($lists->get() as $list) {
            $data['totalAmount'] += $list->return_amount;
            $data['paidAmount'] += $list->return_amount - $list->return_due;
            $data['totalDue'] += $list->return_due;
        }


        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $lists = $lists->get();
            } else {
                $lists = $lists->paginate(request('par-page'));
                $lists->appends(request()->query());
            }
        } else {
            $lists = $lists->paginate(20);
            $lists->appends(request()->query());
        }

        if (checkAdminHasPermission('sales.return.pdf.download')) {
            if (request('export_pdf')) {
                return view('sales::pdf.return', [
                    'lists' => $lists,
                ]);
            }
        }

        return view('sales::return.index', compact('lists', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        checkAdminHasPermissionAndThrowException('sales.return');
        $sale = Sale::find($id);
        $accounts = $this->service->all()->get();
        return view('sales::return.create', compact('sale', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('sales.return');

        $request->validate([
            'sale_id' => 'required',
            'order_date' => 'required',
            'return_date' => 'required',
            'return_amount' => 'required',
            'paying_amount' => 'required',
            'payment_type' => 'required',
            'ingredient_id' => 'required|array',
            'return_subtotal' => 'required|array',
            'return_quantity' => 'required|array',
            'price' => 'required|array',
            'price.*' => 'required',
        ]);

        DB::beginTransaction();
        // create a new sale return
        try {

            $due = $request->return_amount - $request->paying_amount;
            $return = SalesReturn::create([
                'sale_id' => $request->sale_id,
                'customer_id' => $request->customer_id,
                'order_date' => Carbon::createFromFormat('d-m-Y', $request->order_date),
                'return_date' => Carbon::createFromFormat('d-m-Y', $request->return_date),
                'return_amount' => $request->return_amount,
                'return_due' => $due,
                'invoice' => $this->returnInvoice(),
                'note'  => $request->note,
                'status' => 1,
            ]);

            // create a return details

            foreach ($request->ingredient_id as $key => $ingredientId) {
                if (!isset($request->return_quantity[$key]) || $request->return_quantity[$key] == 0) continue;

                $ingredient = \Modules\Ingredient\app\Models\Ingredient::find($ingredientId);
                if (!$ingredient) continue;

                $returnQuantity = (float) $request->return_quantity[$key];
                $returnUnitId = $request->return_unit_id[$key] ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id;

                // Get ingredient's stock unit
                $stockUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;

                // Convert return quantity to stock units
                $stockQuantity = $returnQuantity;
                if ($returnUnitId && $stockUnitId && $returnUnitId != $stockUnitId) {
                    $stockQuantity = \App\Helpers\UnitConverter::safeConvert($returnQuantity, $returnUnitId, $stockUnitId);
                }

                // Get base unit quantity
                $baseUnitId = \App\Helpers\UnitConverter::getBaseUnitId($stockUnitId);
                $baseQuantity = \App\Helpers\UnitConverter::safeConvert($stockQuantity, $stockUnitId, $baseUnitId);

                $details = SalesReturnDetails::create([
                    'sale_return_id' => $return->id,
                    'ingredient_id' => $ingredientId,
                    'unit_id' => $returnUnitId,
                    'quantity' => $returnQuantity,
                    'base_quantity' => $stockQuantity,
                    'price' => $request->price[$key],
                    'sub_total' => $request->return_subtotal[$key],
                ]);

                // update ingredient stock (add back returned quantity in stock units)
                $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
                $ingredient->stock = $oldStock + $stockQuantity;

                // Update stock status
                if ($ingredient->stock <= 0) {
                    $ingredient->stock_status = 'out_of_stock';
                } elseif ($ingredient->stock_alert && $ingredient->stock <= $ingredient->stock_alert) {
                    $ingredient->stock_status = 'low_stock';
                } else {
                    $ingredient->stock_status = 'in_stock';
                }
                $ingredient->save();

                // create stock record
                Stock::create([
                    'sale_return_id' => $return->id,
                    'ingredient_id' => $ingredientId,
                    'unit_id' => $returnUnitId,
                    'date' => Carbon::createFromFormat('d-m-Y', $request->order_date),
                    'type' => 'Sale Return',
                    'invoice' => $return->invoice,
                    'in_quantity' => $returnQuantity,
                    'base_in_quantity' => $baseQuantity,
                    'sku' => $ingredient->sku,
                    'rate' => $request->price[$key],
                    'created_by' => auth('admin')->user()->id,
                ]);
            }


            if ($request->paying_amount) {
                // create a payment
                $account = Account::where('account_type', $request->payment_type);
                if ($request->payment_type == 'cash') {
                    $account = $account->first();
                } else {
                    $account = $account->where('id', $request->account_id)->first();
                }
                $data = [
                    'customer_id' => $request->customer_id,
                    'payment_type' => 'sale return',
                    'sale_return_id' => $return->id,
                    'is_paid' => 1,
                    'is_received' => 0,
                    'account_id' => $account->id,
                    'amount' => $request->paying_amount,
                    'payment_date' => now(),
                    'created_by' => auth('admin')->user()->id,
                ];
                CustomerPayment::create($data);
            }


            // create ledger
            $ledger = new Ledger();
            $ledger->customer_id = $request->customer_id;
            $ledger->sale_return_id = $return->id;
            $ledger->amount = $request->paying_amount;
            $ledger->invoice_type = 'Sale Return';
            $ledger->is_paid = 1;
            $ledger->invoice_no = $this->genLedgerInvoiceNumber('Sale Return');
            $ledger->note = $request->note;
            $ledger->due_amount += $due;
            $ledger->date = Carbon::createFromFormat('d-m-Y', $request->payment_date);
            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();


            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.sales.index', [], ['messege' => 'Sales return created successfully', 'alert-type' => 'success']);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => $ex->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('sales.return.edit');
        $return = SalesReturn::with(['details.ingredient', 'details.unit', 'sale.products.product'])->findOrFail($id);
        $sale = $return->sale;
        $accounts = $this->service->all()->get();
        $payment = $return->payments()->first();
        return view('sales::return.edit', compact('return', 'sale', 'accounts', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('sales.return.edit');

        $request->validate([
            'return_date' => 'required',
            'return_amount' => 'required',
            'paying_amount' => 'required',
            'payment_type' => 'required',
            'ingredient_id' => 'required|array',
            'return_subtotal' => 'required|array',
            'return_quantity' => 'required|array',
            'price' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            $return = SalesReturn::findOrFail($id);

            // Restore old stock quantities before updating
            foreach ($return->details as $oldDetail) {
                $ingredient = $oldDetail->ingredient;
                if ($ingredient && $ingredient->id) {
                    $restoreQty = (float) ($oldDetail->base_quantity ?? $oldDetail->quantity);
                    $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
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

            // Delete old details and stock records
            $return->details()->delete();
            $return->stock()->delete();

            // Update return record
            $due = $request->return_amount - $request->paying_amount;
            $return->update([
                'return_date' => Carbon::createFromFormat('d-m-Y', $request->return_date),
                'return_amount' => $request->return_amount,
                'return_due' => $due,
                'note' => $request->note,
            ]);

            // Create new return details
            foreach ($request->ingredient_id as $key => $ingredientId) {
                if (!isset($request->return_quantity[$key]) || $request->return_quantity[$key] == 0) continue;

                $ingredient = \Modules\Ingredient\app\Models\Ingredient::find($ingredientId);
                if (!$ingredient) continue;

                $returnQuantity = (float) $request->return_quantity[$key];
                $returnUnitId = $request->return_unit_id[$key] ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id;

                // Get ingredient's stock unit
                $stockUnitId = $ingredient->purchase_unit_id ?? $ingredient->unit_id;

                // Convert return quantity to stock units
                $stockQuantity = $returnQuantity;
                if ($returnUnitId && $stockUnitId && $returnUnitId != $stockUnitId) {
                    $stockQuantity = \App\Helpers\UnitConverter::safeConvert($returnQuantity, $returnUnitId, $stockUnitId);
                }

                // Get base unit quantity
                $baseUnitId = \App\Helpers\UnitConverter::getBaseUnitId($stockUnitId);
                $baseQuantity = \App\Helpers\UnitConverter::safeConvert($stockQuantity, $stockUnitId, $baseUnitId);

                SalesReturnDetails::create([
                    'sale_return_id' => $return->id,
                    'ingredient_id' => $ingredientId,
                    'unit_id' => $returnUnitId,
                    'quantity' => $returnQuantity,
                    'base_quantity' => $stockQuantity,
                    'price' => $request->price[$key],
                    'sub_total' => $request->return_subtotal[$key],
                ]);

                // Update ingredient stock (add back returned quantity in stock units)
                $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
                $ingredient->stock = $oldStock + $stockQuantity;

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
                    'sale_return_id' => $return->id,
                    'ingredient_id' => $ingredientId,
                    'unit_id' => $returnUnitId,
                    'date' => Carbon::createFromFormat('d-m-Y', $request->return_date),
                    'type' => 'Sale Return',
                    'invoice' => $return->invoice,
                    'in_quantity' => $returnQuantity,
                    'base_in_quantity' => $baseQuantity,
                    'sku' => $ingredient->sku,
                    'rate' => $request->price[$key],
                    'created_by' => auth('admin')->user()->id,
                ]);
            }

            // Update payment
            $return->payments()->delete();
            if ($request->paying_amount) {
                $account = Account::where('account_type', $request->payment_type);
                if ($request->payment_type == 'cash') {
                    $account = $account->first();
                } else {
                    $account = $account->where('id', $request->account_id)->first();
                }

                CustomerPayment::create([
                    'customer_id' => $return->customer_id,
                    'payment_type' => 'sale return',
                    'sale_return_id' => $return->id,
                    'is_paid' => 1,
                    'is_received' => 0,
                    'account_id' => $account->id,
                    'amount' => $request->paying_amount,
                    'payment_date' => now(),
                    'created_by' => auth('admin')->user()->id,
                ]);
            }

            // Update ledger
            if ($return->ledger) {
                $return->ledger->update([
                    'amount' => $request->paying_amount,
                    'due_amount' => $due,
                    'note' => $request->note,
                    'date' => Carbon::createFromFormat('d-m-Y', $request->return_date),
                ]);
            }

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.sales.return.list', [], ['messege' => 'Sales return updated successfully', 'alert-type' => 'success']);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => $ex->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('sales.return.delete');
        $return = SalesReturn::find($id);

        // IMPORTANT: Restore stock BEFORE deleting details
        foreach ($return->details as $detail) {
            $ingredient = $detail->product; // product() returns Ingredient
            if ($ingredient && $ingredient->id) {
                // Use base_quantity (stock units) for accurate stock restoration
                $restoreQty = (float) ($detail->base_quantity ?? $detail->quantity);
                $oldStock = (float) str_replace(',', '', $ingredient->getRawOriginal('stock') ?? 0);
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

        // Now delete related records
        $return->details()->delete();

        // delete ledger (check if exists first)
        if ($return->ledger) {
            $return->ledger->delete();
        }

        // delete payments
        $return->payments()->delete();

        // delete stock
        $return->stock()->delete();

        // delete return
        $return->delete();

        return $this->redirectWithMessage(RedirectType::DELETE->value, '', [], ['messege' => 'Sales return deleted successfully', 'alert-type' => 'success']);
    }

    public function genLedgerInvoiceNumber($type = 'Sale Payment')
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = Ledger::where('invoice_type', $type)->latest()->first();
        if ($purchase) {
            $purchaseInvoice = $purchase->invoice_no;

            if ($purchaseInvoice) {
                // split the invoice number
                $split_invoice = explode('-', $purchaseInvoice);
                if (isset($split_invoice[1])) {
                    $invoice_number = (int) $split_invoice[1] + 1;
                } else {
                    preg_match('/(\d+)$/', $purchaseInvoice, $matches);
                    $invoice_number = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
                }
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }

    public function returnInvoice()
    {
        $number = 1;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $return = SalesReturn::latest()->first();
        if ($return) {
            $purchaseInvoice = $return->invoice;

            if ($purchaseInvoice) {
                // split the invoice number
                $split_invoice = explode($prefix, $purchaseInvoice);
                if (isset($split_invoice[1])) {
                    $invoice_number = (int) $split_invoice[1] + 1;
                } else {
                    preg_match('/(\d+)$/', $purchaseInvoice, $matches);
                    $invoice_number = isset($matches[1]) ? (int) $matches[1] + 1 : 1;
                }
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }
}
