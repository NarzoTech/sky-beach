<?php
namespace Modules\Purchase\app\Services;

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
        if (request()->ingredient_id) {
            $purchase = $purchase->whereHas('purchaseDetails', function ($q) {
                $q->where('ingredient_id', request('ingredient_id'));
            });
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
        // $this->updateLedger($request, $purchase->id, $paidAmount, 'purchase');

        foreach ($request->ingredient_id as $index => $id) {
            $ingredient = Ingredient::find($id);
            $purchaseUnitId = $request->purchase_unit_id[$index] ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id;
            $quantity = $request->quantity[$index];

            // Convert quantity to ingredient's base unit for stock tracking
            $baseQuantity = $quantity;
            if ($purchaseUnitId != $ingredient->unit_id) {
                $baseQuantity = \App\Helpers\UnitConverter::convert(
                    $quantity,
                    $purchaseUnitId,
                    $ingredient->unit_id
                );
            }

            $purchaseDetails                 = new PurchaseDetails();
            $purchaseDetails->purchase_id    = $purchase->id;
            $purchaseDetails->ingredient_id  = $id;
            $purchaseDetails->unit_id        = $purchaseUnitId;
            $purchaseDetails->quantity       = $quantity;
            $purchaseDetails->base_quantity  = $baseQuantity;
            $purchaseDetails->purchase_price = $request->unit_price[$index];
            $purchaseDetails->sale_price     = 0;
            $purchaseDetails->sub_total      = $request->total[$index];
            $purchaseDetails->profit         = 0;
            $purchaseDetails->created_by     = Auth::id();
            $purchaseDetails->save();

            // Update ingredient stock with base quantity
            $ingredient->stock += $baseQuantity;
            $ingredient->cost  = $request->unit_price[$index];
            $ingredient->save();

            // create stock with unit tracking
            Stock::create([
                'purchase_id'       => $purchase->id,
                'ingredient_id'     => $id,
                'unit_id'           => $purchaseUnitId,
                'date'              => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'type'              => 'Purchase',
                'invoice'           => route('admin.purchase.invoice', $purchase->id),
                'in_quantity'       => $quantity,
                'base_in_quantity'  => $baseQuantity,
                'sku'               => $ingredient->sku,
                'purchase_price'    => $request->unit_price[$index],
                'sale_price'        => 0,
                'rate'              => $request->unit_price[$index],
                'profit'            => 0,
                'created_by'        => auth('admin')->user()->id,
            ]);
        }

        // if ($paidAmount) {
        //     $this->purchaseLedger($request, $purchase->id, -$paidAmount, 'purchase payment', 1, $request->due_amount);
        // }

        // create payments
        foreach ($request->payment_type as $key => $item) {
            $account = Account::where('account_type', $item);
            if ($item == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
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
        $purchase->payment_status = $request->paid_amount == $request->total_amount ? 'paid' : 'due';
        $purchase->payment_type   = $request->payment_type;
        $purchase->note           = $request->note;
        $purchase->updated_by     = Auth::id();
        $purchase->save();

        $ledger = $this->getLedger($request, $id, 'purchase', 1);

        $this->purchaseLedger($request, $purchase->id, $paidAmount, $request->total_amount, 'purchase', 1, $request->due_amount, $ledger);

        // restore ingredient stock using base quantity
        foreach ($purchase->purchaseDetails as $purchaseDetail) {
            $ingredient = Ingredient::find($purchaseDetail->ingredient_id);
            if ($ingredient) {
                $ingredient->stock -= ($purchaseDetail->base_quantity ?? $purchaseDetail->quantity);
                $ingredient->save();
            }
        }

        // delete old purchase details
        $purchase->purchaseDetails()->delete();
        $purchase->payments()->delete();
        $purchase->stock()->delete();

        // store new purchase details with unit conversion
        foreach ($request->ingredient_id as $index => $id) {
            $ingredient = Ingredient::find($id);
            $purchaseUnitId = $request->purchase_unit_id[$index] ?? $ingredient->purchase_unit_id ?? $ingredient->unit_id;
            $quantity = $request->quantity[$index];

            // Convert quantity to ingredient's base unit for stock tracking
            $baseQuantity = $quantity;
            if ($purchaseUnitId != $ingredient->unit_id) {
                $baseQuantity = \App\Helpers\UnitConverter::convert(
                    $quantity,
                    $purchaseUnitId,
                    $ingredient->unit_id
                );
            }

            $purchaseDetails                 = new PurchaseDetails();
            $purchaseDetails->purchase_id    = $purchase->id;
            $purchaseDetails->ingredient_id  = $id;
            $purchaseDetails->unit_id        = $purchaseUnitId;
            $purchaseDetails->quantity       = $quantity;
            $purchaseDetails->base_quantity  = $baseQuantity;
            $purchaseDetails->purchase_price = $request->unit_price[$index];
            $purchaseDetails->sale_price     = 0;
            $purchaseDetails->sub_total      = $request->total[$index];
            $purchaseDetails->profit         = 0;
            $purchaseDetails->created_by     = Auth::id();
            $purchaseDetails->save();

            // Update ingredient stock with base quantity
            $ingredient->stock += $baseQuantity;
            $ingredient->cost  = $request->unit_price[$index];
            $ingredient->save();

            // create stock with unit tracking
            Stock::create([
                'purchase_id'       => $purchase->id,
                'ingredient_id'     => $id,
                'unit_id'           => $purchaseUnitId,
                'date'              => Carbon::createFromFormat('d-m-Y', $request->purchase_date),
                'type'              => 'Purchase',
                'invoice'           => route('admin.purchase.invoice', $purchase->id),
                'in_quantity'       => $quantity,
                'base_in_quantity'  => $baseQuantity,
                'sku'               => $ingredient->sku,
                'purchase_price'    => $request->unit_price[$index],
                'sale_price'        => 0,
                'rate'              => $request->unit_price[$index],
                'profit'            => 0,
                'created_by'        => auth('admin')->user()->id,
            ]);
        }

        // create payments
        foreach ($request->payment_type as $key => $item) {
            $account = Account::where('account_type', $item);
            if ($item == 'cash') {
                $account = $account->first();
            } else {
                $account = $account->where('id', $request->account_id[$key])->first();
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

        // update ledger
        // $ledger = $this->getLedger($request, $purchase->id, 1, 'purchase payment');

        // if ($paidAmount) {
        //     $this->purchaseLedger($request, $purchase->id, -$paidAmount, 'purchase payment', 1, $request->due_amount, $ledger);
        // }

        return $purchase;
    }

    public function destroy($id)
    {
        $purchase = $this->purchase->find($id);

        // restore ingredient stock
        foreach ($this->purchase->find($id)->purchaseDetails as $purchaseDetail) {
            $ingredient = Ingredient::find($purchaseDetail->ingredient_id);
            if ($ingredient) {
                $ingredient->stock -= $purchaseDetail->quantity;
                $ingredient->save();
            }
        }

        PurchaseDetails::where('purchase_id', $id)?->delete();
        Stock::where('purchase_id', $id)?->delete();
        SupplierPayment::where('purchase_id', $id)?->delete();

        // delete ledger
        $ledger = Ledger::where('invoice_type', 'purchase')->orWhere('invoice_type', 'purchase payment')
            ->where('invoice_no', $purchase->invoice_number)
            ->get();

        $ledger = Ledger::where('invoice_type', 'purchase')->orWhere('invoice_type', 'purchase payment')
            ->where('invoice_no', $purchase->invoice_number)
            ->get();
        foreach ($ledger as $item) {
            $item->delete();
        }

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

            // split the invoice number
            $split_invoice  = explode('-', $purchaseInvoice);
            $invoice_number = (int) $split_invoice[1] + 1;
            $invoice_number = $prefix . $invoice_number;
        }

        return $invoice_number;
    }

    public function getPurchase($id)
    {
        return $this->purchase->with('supplier', 'warehouse', 'purchaseDetails.ingredient.unit', 'payments')->find($id);
    }

    public function getPurchaseDetails($id)
    {
        return PurchaseDetails::with('ingredient')->where('purchase_id', $id)->get();
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
        return $ingredients->with('unit', 'purchaseUnit', 'consumptionUnit')->get();
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
        // store purchase return
        $purchase = $this->purchaseReturn->create([
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

        // store purchase return details

        foreach ($request->ingredient_id as $index => $val) {
            $purchase->purchaseDetails()->create([
                'ingredient_id'  => $val,
                'purchase_id' => $request->purchase_id,
                'quantity'    => $request->return_quantity[$index],
                'total'       => $request->return_subtotal[$index],
            ]);

            // update ingredient stock
            $ingredient = Ingredient::find($val);
            $ingredient->stock = $ingredient->stock - $request->return_quantity[$index];
            $ingredient->save();

            // update stock
            Stock::create([
                'invoice_number'     => $purchase->invoice,
                'purchase_return_id' => $purchase->id,
                'type'               => 'purchase return',
                'ingredient_id'      => $val,
                'date'               => now(),
                'out_quantity'       => $request->return_quantity[$index],
                'sku'                => $ingredient->sku,
                'created_by'         => auth('admin')->user()->id,
            ]);
        }

        $account = Account::where('account_type', $request->payment_type);
        if ($request->payment_type == 'cash') {
            $account = $account->first();
        } else {
            $account = $account->where('id', $request->account_id)->first();
        }

        if ($request->received_amount) {
            // create ledger
            $ledger = $this->purchaseReturnLedger($request, $purchase->id, $request->received_amount, 'purchase return', 0);
            SupplierPayment::create([
                'payment_type'       => 'purchase_receive',
                'purchase_return_id' => $request->purchase_id,
                'supplier_id'        => $purchase->supplier_id,
                'account_id'         => $account->id,
                'is_received'        => 1,
                'account_type'       => accountList()[$request->payment_type],
                'amount'             => $request->received_amount,
                'payment_date'       => now(),
                'created_by'         => auth()->user()->id,
                'ledger_id'          => $ledger->id,
            ]);
        }

        return $purchase;
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
            'invoice'         => $this->returnInvoice(),
        ]);

        // restore ingredient stock

        foreach ($return->purchaseDetails as $purchaseDetail) {
            $ingredient = Ingredient::find($purchaseDetail->ingredient_id);
            if ($ingredient) {
                $ingredient->stock += $purchaseDetail->quantity;
                $ingredient->save();
            }
        }

        // delete old purchase details
        $return->purchaseDetails()->delete();
        $return->payments()?->delete();
        $return->stock()->delete();
    }

    public function getPurchaseReturn($id)
    {
        return $this->purchaseReturn->with('supplier', 'purchaseDetails.ingredient')->find($id);
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

        // check if ledger already exist

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

        // restore ingredient stock

        foreach ($return->purchaseDetails as $purchaseDetail) {
            $ingredient = Ingredient::find($purchaseDetail->ingredient_id);
            if ($ingredient) {
                $ingredient->stock += $purchaseDetail->quantity;
                $ingredient->save();
            }
        }

        $return->payment()->delete();
        $return->purchaseDetails()->delete();
        $return->delete();
    }

    public function returnInvoice($id = 0)
    {
        $number         = 1;
        $prefix         = 'INV-';
        $invoice_number = $prefix . $number;

        $return = $this->purchaseReturn->find($id);
        if ($return) {
            $purchaseInvoice = $return->invoice;

            // split the invoice number
            $split_invoice  = explode($prefix, $purchaseInvoice);
            $invoice_number = (int) $split_invoice[1] + 1;
            $invoice_number = $prefix . $invoice_number;
        }

        return $invoice_number;
    }
}
