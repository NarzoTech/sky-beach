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

            foreach ($request->product_id as $key => $prod_id) {
                if ($request->return_quantity[$key] == 0) continue;
                $details = SalesReturnDetails::create(
                    [
                        'sale_return_id' => $return->id,
                        'product_id' => $prod_id,
                        'quantity' => $request->return_quantity[$key],
                        'price' => $request->price[$key],
                        'sub_total' => $request->return_subtotal[$key],
                    ]
                );


                // update stock
                $stock = $details->product->stock;
                $stock = $stock + $request->return_quantity[$key];
                $details->product->update([
                    'stock' => $stock
                ]);

                // create stock
                Stock::create([
                    'sale_return_id' => $return->id,
                    'product_id' => $prod_id,
                    'date' => Carbon::createFromFormat('d-m-Y', $request->order_date),
                    'type' => 'Sale Return',
                    // 'invoice' => route('admin.sales.invoice', $sale->id),
                    // 'invoice_number' => $sale->invoice,
                    'in_quantity' => $request->return_quantity[$key],
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('sales.return.delete');
        $return = SalesReturn::find($id);

        // delete return details
        $return->details()->delete();

        // delete ledger
        $return->ledger->delete();

        // delete payments
        $return->payments()->delete();


        // update stock
        foreach ($return->details as $detail) {
            $product = $detail->product;
            $product->stock = $product->stock - $detail->quantity;
            $product->save();
        }

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
                $invoice_number = (int) $split_invoice[1] + 1;
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
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }
}
