<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\CustomerExport;
use App\Exports\LedgerExport;
use App\Http\Controllers\Controller;
use App\Imports\CustomersImport;
use App\Models\Ledger;
use App\Models\LedgerDetails;
use App\Models\User;
use Carbon\Carbon;
use App\Traits\RedirectHelperTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\Customer\app\Models\BannedHistory;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Sales\app\Models\Sale;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    use RedirectHelperTrait;

    public function __construct(private UserGroupService $userGroup, private AreaService $areaService, private AccountsService $account)
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.view');

        $query = User::query();

        $query->with('sales', 'payment');

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%')
                ->orWhere('address', 'like', '%' . $request->keyword . '%');
        });


        $orderBy = $request->filled('order_by') ? $request->order_by : 'asc';

        if ($orderBy) {
            $users = $query->orderBy('name', $orderBy);
        }
        $customers = null;
        if (request()->order_type) {
            $orderBy = request()->order_by;
            $orderBy = $orderBy == 'asc' ? 'sortBy' : 'sortByDesc';
            switch (request()->order_type) {
                case 'due':
                    $customers = $query->with(['sales', 'payment'])
                        ->get();
                    $customers = $customers->filter(function ($customer) {
                        return $customer->getTotalDueAttribute() > 0;
                    });
                    $customers = $customers->$orderBy(function ($customer) {
                        $totalPurchase = $customer->sales->sum('grand_total');
                        $totalPaid = $customer->payment->sum('amount');
                        $totalDue = $totalPurchase - $totalPaid;
                        return $totalDue;
                    });
                    break;

                case 'paid':
                    $customers = $query->with(['payment', 'sales'])
                        ->whereHas('sales')
                        ->get();
                    $customers = $customers->filter(function ($customer) {
                        return $customer->sales->sum('grand_total') == $customer->getTotalPaidAttribute();
                    });
                    $customers = $customers->$orderBy(function ($customer) {
                        return $customer->payment->sum('amount');
                    });
                    break;

                case 'total':
                    $customers = $query->with(['sales'])
                        ->get()
                        ->$orderBy(function ($customer) {
                            return $customer->sales->sum('grand_total');
                        });
                    break;

                default:
                    // Default sorting logic
                    break;
            }
        }


        $data['totalSale'] = 0;
        $data['pay'] = 0;
        $data['total_return'] = 0;
        $data['total_return_pay'] = 0;
        $data['total_return_due'] = 0;
        $data['total_due'] = 0;
        $data['total_advance'] = 0;
        $data['total_due_dismiss'] = 0;

        $customerData = request()->order_type ? $customers : $query->get();
        foreach ($customerData as $index => $customer) {
            $data['totalSale'] += $customer->sales->sum('grand_total');
            $data['pay'] += $customer->total_paid;

            $data['total_due'] += $customer->total_due;

            $data['total_advance'] += $customer->advances();
            $data['total_due_dismiss'] += $customer->total_due_dismiss;
        }


        if (request('par-page')) {
            if (request('par-page') == 'all') {

                $perPage = request()->order_type ? $customers->count() : $customerData->count();
            } else {
                $perPage = request('par-page');
            }
        } else {
            $perPage = 20;
        }

        if (request()->order_type) {
            // Convert sorted collection to paginate manually
            $page = request('page', 1); // Default to page 1
            $paginatedCustomers = $customerData->slice(($page - 1) * $perPage, $perPage)->values();
        }

        if (checkAdminHasPermission('customer.excel.download')) {
            if (request('export')) {
                $fileName = 'customers-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new CustomerExport($customerData, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('customer.pdf.download')) {
            if (request('export_pdf')) {
                $html = view('customer::pdf.customer', [
                    'users' => $customerData,
                    'data' => $data
                ])->render();

                $pdf = $fileName = 'customer-list-' . date('Y-m-d') . '_' . date('h-i-s') . '.pdf';
                $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape')->setOption('enable_javascript')->setOption('isTableHeaderRepeat', false)->setOption('isRemoteEnabled', true)->setWarnings(false);
                return $pdf->download($fileName);
            }
        }



        if (request()->order_type) {
            $users = new LengthAwarePaginator(
                $paginatedCustomers,
                $customerData->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $users = $query->paginate($perPage);
        }
        $users = $users->appends(request()->query());

        $groups = $this->userGroup->getUserGroup('dropdown')->where('type', 'customer')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();
        return view('customer::customer')->with([
            'users' => $users,
            'groups' => $groups,
            'areaList' => $areaList,
            'data' => $data
        ]);
    }

    // store
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.create');

        $this->saveUser($request);

        // check if request is ajax
        if ($request->ajax()) {
            $customers = User::orderBy('id', 'desc')->where('status', 1)->get();
            $view = view('pos::customer-drop-down', compact('customers'))->render();
            return response()->json([
                'message' => 'Customer created successfully.',
                'alert-type' => 'success',
                'view' => $view
            ]);
        }

        return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.customers.index', [], ['messege' => 'Customer created successfully.', 'alert-type' => 'success']);
    }
    public function show($id)
    {
        checkAdminHasPermissionAndThrowException('customer.view');

        $user = User::findOrFail($id);

        $banned_histories = BannedHistory::where('user_id', $id)->orderBy('id', 'desc')->get();

        return view('customer::customer_show')->with([
            'user' => $user,
            'banned_histories' => $banned_histories,
        ]);
    }

    // update

    public function update(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('customer.edit');

        $request->validate([
            'name' => 'required',
            'phone' => 'nullable|unique:users,phone,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->group_id = $request->group_id;
        $user->area_id = $request->area_id;
        $user->membership = $request->membership;
        $user->plate_number = $request->plate_number;
        $user->date = Carbon::createFromFormat('d-m-Y', $request->date);
        $user->status = $request->status;
        $user->guest = $request->guest ? 1 : 0;
        $user->address = $request->address;
        $user->wallet_balance = $request->due;
        $user->initial_advance = $request->initial_advance;
        $user->save();

        return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customers.index', [], ['messege' => 'Customer updated successfully.', 'alert-type' => 'success']);
    }

    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('customer.delete');
        $user = User::findOrFail($id);

        $user->due()->delete();
        $user->payment()->delete();
        $user->sales()->details()->delete();
        $user->sales()->stock()->delete();
        $user->sales()->delete();
        $user->delete();
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.customers.index', [], ['messege' => 'Customer deleted successfully.', 'alert-type' => 'success']);
    }


    public function saveUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->group_id = $request->group_id;
        $user->area_id = $request->area_id;
        $user->membership = $request->membership;
        $user->plate_number = $request->plate_number;
        $user->wallet_balance = $request->due;
        $user->initial_advance = $request->initial_advance;
        $user->date = Carbon::createFromFormat('d-m-Y', $request->date);
        $user->status = $request->status;
        $user->guest = $request->guest ? 1 : 0;
        $user->address = $request->address;
        $user->save();

        return $user;
    }

    public function singleCustomer($id)
    {
        $user = User::findOrFail($id);
        return $user;
    }

    public function dueReceiveForm(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive');
        if (!$request->customer) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Customer Not Found', 'alert-type' => 'error']);
        }

        $accounts = $this->account->all()->get();
        $customer = User::where('id', $request->customer)->first();

        return view('customer::due-receive', compact('customer', 'accounts'));
    }

    public function dueReceive(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive');
        $request->validate([
            'receiving_amount' => 'required',
        ]);

        DB::beginTransaction();
        try {

            // create ledger

            $ledger = new Ledger();
            $ledger->customer_id = $request->customer_id;
            $ledger->amount = $request->receiving_amount;
            $ledger->invoice_type = 'Due Receive';
            $ledger->is_paid = 0;
            $ledger->is_received = 1;
            $ledger->invoice_no = $this->genLedgerInvoiceNumber('Due Receive');
            $ledger->due_amount -= $request->receiving_amount;

            $ledger->note = $request->note;
            $ledger->date = Carbon::createFromFormat('d-m-Y', $request->payment_date);

            $ledger->created_by = auth('admin')->user()->id;
            $ledger->save();

            $ledger->invoice_url = route('admin.customers.ledger-details', $ledger->id);
            $ledger->save();

            $account = $request->account_id;

            if ($account == 'cash' || $account == 'advance') {
                $account = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $account = $this->account->all()->find($account);
            }


            foreach ($request->invoice_no as $index => $invo) {
                $sale = Sale::where('invoice', $invo)->first();

                $sale->payment_status = $sale->due_amount == $request->amount[$index] ? 'paid' : 'due';

                $sale->paid_amount = $sale->paid_amount + $request->amount[$index];
                $sale->due_amount = $sale->due_amount - $request->amount[$index];
                $sale->save();

                // create payment data
                CustomerPayment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $sale->customer_id,
                    'account_id' => $account->id,
                    'payment_type' => 'due_receive',
                    'is_received' => 1,
                    'amount' => $request->amount[$index],
                    'payment_date' => Carbon::createFromFormat('d-m-Y', $request->payment_date),
                    'note' => $request->note,
                    'created_by' => auth('admin')->user()->id,
                ]);

                if ($request->amount[$index]) {
                    // update customer due amount
                    $due = CustomerDue::where('invoice', $invo)->first();
                    $due->due_amount = $due->due_amount - $request->amount[$index];
                    $due->paid_amount = $due->paid_amount + $request->amount[$index];
                    $due->save();

                    // create ledger details
                    $ledger->details()->create([
                        'invoice' => $invo,
                        'amount' => $request->amount[$index],
                    ]);
                }
            }

            DB::commit();
            return to_route('admin.customers.index')->with([
                'messege' => 'Customer due receive successfully.',
                'alert-type' => 'success'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => $exception->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function dueReceiveList()
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive.list');
        $payments  = CustomerPayment::whereNotNull('sale_id')->where('payment_type', 'due_receive')->where('amount', '>', 0);
        // Date filtering
        if (request()->from_date && request()->to_date) {
            $fromDate = \Carbon\Carbon::parse(request()->from_date)->startOfDay();
            $toDate = \Carbon\Carbon::parse(request()->to_date)->endOfDay();
            $payments = $payments->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        // Keyword search
        if (request()->keyword) {
            $keyword = '%' . request()->keyword . '%';
            $payments = $payments->where(function ($q) use ($keyword) {
                $q->where('note', 'like', $keyword)
                    ->orWhere('amount', 'like', $keyword)

                    ->orWhereHas('customer', function ($query) use ($keyword) {
                        $query->where('name', 'like', $keyword)
                            ->orWhere('phone', 'like', $keyword)
                            ->orWhere('address', 'like', $keyword)
                            ->orWhere('email', 'like', $keyword);
                    });
            })
                ->orWhere('invoice', 'like', $keyword)
                ->orWhere('account_type', 'like', $keyword);
        }

        if (request()->order_by) {
            $payments = $payments->orderBy('payment_date', request()->order_by);
        } else {
            $payments = $payments->orderBy('payment_date', 'desc');
        }

        if (request('customer')) {
            $payments = $payments->where('customer_id', request('customer'));
        }
        $data['total'] = $payments->sum('amount');
        $payments = $payments->paginate(20);

        $payments->appends(request()->query());

        return view('customer::due-list', compact('payments', 'data'));
    }

    public function dueReceiveEdit($id)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive.edit');
        $payment = CustomerPayment::findOrFail($id);
        $accounts = $this->account->all()->get();
        return view('customer::due-receive-edit', compact('payment', 'accounts'));
    }

    public function dueReceiveUpdate(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive.edit');
        $payment = CustomerPayment::findOrFail($id);
        $payment->update($request->except('_token'));
        return to_route('admin.customer.due-receive.list')->with([
            'messege' => 'Customer due receive successfully.',
            'alert-type' => 'success'
        ]);
    }

    public function dueReceiveDelete($id)
    {
        checkAdminHasPermissionAndThrowException('customer.due.receive.delete');
        $payment = CustomerPayment::findOrFail($id);

        // update customer due amount
        $due = CustomerDue::where('invoice', $payment->sale->invoice)->first();
        $due->due_amount = $due->due_amount + $payment->amount;
        $due->paid_amount = $due->paid_amount - $payment->amount;
        $due->save();


        // update sale
        $sale = $payment->sale;
        $sale->payment_status = $sale->due_amount == $payment->amount ? 'paid' : 'due';
        $sale->paid_amount = $sale->paid_amount - $payment->amount;
        $sale->due_amount = $sale->due_amount + $payment->amount;
        $sale->save();


        // customer ledger delete
        $invoiceNumber = $payment->sale->invoice;
        $ledgerDetail = LedgerDetails::where('invoice', $invoiceNumber)->first();

        if ($ledgerDetail) {
            $ledger = $ledgerDetail->ledger;

            // Check if this is the only detail in the ledger
            $otherDetailsCount = LedgerDetails::where('ledger_id', $ledger->id)
                ->where('id', '!=', $ledgerDetail->id)
                ->count();

            // Delete the ledger detail
            $ledgerDetail->delete();

            if ($otherDetailsCount == 0) {
                // No other details, delete the entire ledger
                $ledger->delete();
            } else {
                // Update ledger amounts
                $ledger->amount = $ledger->amount - $payment->amount;
                $ledger->due_amount = $ledger->due_amount + $payment->amount;
                $ledger->save();
            }
        }

        $payment->delete();
        return to_route('admin.customer.due-receive.list')->with([
            'messege' => 'Customer due receive deleted successfully.',
            'alert-type' => 'success'
        ]);
    }

    public function changeStatus($id)
    {
        checkAdminHasPermissionAndThrowException('customer.status');
        $user = User::find($id);

        $status = $user->status == 1 ? 0 : 1;

        $user->status = $status;
        $user->save();

        $notification = $status == 1 ? 'Customer activated' : 'Customer deactivated';

        return response()->json(['status' => 'success', 'message' => $notification]);
    }

    public function advance($id)
    {
        checkAdminHasPermissionAndThrowException('customer.advance');
        $customer = User::find($id);
        $accounts = $this->account->all()->with('bank')->get();
        return view('customer::advance', compact('customer', 'accounts'));
    }

    public function advanceStore(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('customer.advance');
        $validator = Validator::make($request->all(), [
            'advance' => 'nullable',
            'paying_amount' => 'nullable',
            'refund_amount' => 'nullable',
            'date' => 'required',
            'total_amount' => 'required',
            'payment_type' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (is_null($request->paying_amount) && is_null($request->refund_amount)) {
                $validator->errors()->add('paying_amount', 'Either Receiving Amount or Refund Amount must be provided.');
                $validator->errors()->add('refund_amount', 'Either Receiving Amount or Refund Amount must be provided.');
            } elseif (!is_null($request->paying_amount) && !is_null($request->refund_amount)) {
                $validator->errors()->add('paying_amount', 'Only one of Receiving Amount or Refund Amount can be provided.');
                $validator->errors()->add('refund_amount', 'Only one of Receiving Amount or Refund Amount can be provided.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $this->advancePay($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customers.index', [], ['messege' => 'Advance payment successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.customers.index', [], ['messege' => 'Advance payment failed.', 'alert-type' => 'error']);
        }
    }


    public function advancePay(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('customer.advance');
        $account = $request->account_id;

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::firstOrCreate(
                ['account_type' => 'cash'],
                ['bank_account_name' => 'Cash Register']
            );
        } else {
            $account = Account::find($account);
        }


        // create payment data
        // advance_receive: is_paid=0, is_received=1 (receiving money from customer)
        // advance_refund: is_paid=1, is_received=0 (paying money back to customer)
        CustomerPayment::create([
            'customer_id' => $id,
            'account_id' => $account->id,
            'payment_type' => $request->refund_amount != null ? 'advance_refund' : 'advance_receive',
            'is_paid' => $request->refund_amount != null ? 1 : 0,
            'is_received' => $request->refund_amount != null ? 0 : 1,
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount,
            'account_type' => accountList()[$account->account_type],
            'note' => $request->note,
            'created_by' => auth('admin')->user()->id,
            'payment_date' => Carbon::createFromFormat('d-m-Y', $request->date),
            'invoice' => $this->genInvoiceNumber()
        ]);



        // create ledger

        $ledger = new Ledger();
        $ledger->customer_id = $id;
        $ledger->amount = $request->paying_amount ?? $request->refund_amount;
        $ledger->invoice_type = $request->refund_amount == null ? 'Advance Received' : 'Payment Return';
        $ledger->is_paid = $request->refund_amount != null ? 1 : 0;
        $ledger->is_received = $request->refund_amount != null ? 0 : 1;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber($ledger->invoice_type);
        $ledger->note = $request->note;


        if ($request->refund_amount != null) {
            $ledger->due_amount = $request->refund_amount;
            $ledger->amount = -$request->refund_amount;
        } else {
            $ledger->due_amount = -$request->paying_amount;
            $ledger->amount = $request->paying_amount;
        }
        $ledger->date = Carbon::createFromFormat('d-m-Y', $request->date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();

        $ledger->invoice_url = route('admin.customers.ledger-details', $ledger->id);
        $ledger->save();
    }

    public function genInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = CustomerPayment::latest()->first();

        if ($purchase) {
            $purchaseInvoice = $purchase->invoice;

            if ($purchaseInvoice) {
                // split the invoice number
                $split_invoice = explode('-', $purchaseInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
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


    public function ledger($id)
    {
        checkAdminHasPermissionAndThrowException('customer.ledger');
        $user = User::findOrFail($id);
        $ledgers = Ledger::where('customer_id', $user->id)->orderBy('date', 'asc')->paginate(20);
        $ledgers->appends(request()->query());
        $title = __('Customer Ledger');

        if (request('export')) {
            $fileName = 'customer-ledger-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new LedgerExport($ledgers, $title), $fileName);
        }
        if (request('export_pdf')) {
            return view('supplier::pdf.ledger', ['ledgers' => $ledgers,  'title' => $title]);
        }
        return view('supplier::ledger', compact('ledgers', 'title'));
    }

    public function ledgerDetails($id)
    {
        checkAdminHasPermissionAndThrowException('customer.ledger');
        $ledger = Ledger::with('details', 'customer')->find($id);
        $title = __('Customer Ledger Details');
        return view('supplier::ledger-details', compact('ledger', 'title'));
    }

    public function bulkImport()
    {
        checkAdminHasPermissionAndThrowException('customer.bulk.import');
        return view('customer::import');
    }
    public function bulkImportStore(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.bulk.import');
        $request->validate(['file' => 'required']);
        try {
            $file = $request->file('file');
            Excel::import(new CustomersImport, $file);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.customers.index', [], ['messege' => 'Supplier imported successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.customers.index', [], ['messege' => 'Supplier imported failed.', 'alert-type' => 'error']);
        }
    }


    public function deleteAllCustomer(Request $request)
    {
        checkAdminHasPermissionAndThrowException('customer.bulk.delete');
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth('admin')->user()->password)) {
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Password does not match.', 'alert-type' => 'error']);
        }

        $users = User::all();

        // delete ledger
        foreach ($users as $user) {
            if ($user->due()?->exists()) {
                $user->due()?->delete();
            }
            if ($user->payment()->exists()) {
                $user->payment()?->delete();
            }
            if ($user->sales()->exists()) {

                foreach ($user->sales as $sale) {
                    $sale->details()?->delete();
                    $sale->stock()?->delete();
                    $sale->delete();
                }
            }
            if ($user->orderReviews()->exists()) {
                $user->orderReviews()?->delete();
            }

            $user->delete();
        }


        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.customers.index', [], ['messege' => 'Customer deleted successfully.', 'alert-type' => 'success']);
    }
}
