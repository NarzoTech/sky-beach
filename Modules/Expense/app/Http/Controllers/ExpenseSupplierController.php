<?php

namespace Modules\Expense\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\ExpenseSupplierExport;
use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Expense\app\Models\ExpenseSupplierPayment;
use Modules\Expense\app\Services\ExpenseSupplierService;

class ExpenseSupplierController extends Controller
{
    use RedirectHelperTrait;

    public function __construct(
        private ExpenseSupplierService $expenseSupplierService,
        private AreaService $areaService,
        private AccountsService $accountsService
    ) {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.view');
        $suppliers = $this->expenseSupplierService->allSuppliers();

        if (checkAdminHasPermission('expense_supplier.excel.download')) {
            if (request('export')) {
                $fileName = 'expense-suppliers-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new ExpenseSupplierExport($suppliers), $fileName);
            }
        }

        $data['totalExpense'] = 0;
        $data['pay'] = 0;
        $data['total_due'] = 0;
        $data['total_advance'] = 0;

        $supplierData = request()->order_type ? $suppliers : $suppliers->get();

        foreach ($supplierData as $supplier) {
            $data['totalExpense'] += $supplier->expenses->sum('amount');
            $data['pay'] += $supplier->payments->whereIn('payment_type', ['expense', 'due_pay'])->sum('amount');
            $data['total_due'] += $supplier->total_due;
            $data['total_advance'] += $supplier->advance;
        }

        if (checkAdminHasPermission('expense_supplier.pdf.download')) {
            if (request('export_pdf')) {
                return view('expense::expense-suppliers.pdf.index', ['suppliers' => $supplierData, 'data' => $data]);
            }
        }

        $perPage = 20;
        if (request('par-page')) {
            $perPage = request('par-page') == 'all' ? $suppliers->count() : request('par-page');
        }

        if (request()->order_type) {
            $page = request('page', 1);
            $paginatedSuppliers = $suppliers->slice(($page - 1) * $perPage, $perPage)->values();
            $suppliers = new LengthAwarePaginator(
                $paginatedSuppliers,
                $suppliers->count(),
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $suppliers = $suppliers->paginate($perPage);
        }

        $suppliers->appends(request()->query());
        $areaList = $this->areaService->getArea()->get();

        return view('expense::expense-suppliers.index', compact('suppliers', 'areaList', 'data'));
    }

    public function create()
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.create');
        $areaList = $this->areaService->getArea()->get();
        return view('expense::expense-suppliers.create', compact('areaList'));
    }

    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.create');
        $request->validate(['name' => 'required']);

        try {
            $this->expenseSupplierService->store($request);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Expense Supplier created successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Expense Supplier creation failed.', 'alert-type' => 'error']);
        }
    }

    public function show($id)
    {
        return view('expense::expense-suppliers.show');
    }

    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.edit');
        $supplier = $this->expenseSupplierService->find($id);
        $areaList = $this->areaService->getArea()->get();
        return view('expense::expense-suppliers.edit', compact('supplier', 'areaList'));
    }

    public function update(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.edit');
        $request->validate(['name' => 'required']);

        try {
            $this->expenseSupplierService->update($request, $id);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Expense Supplier updated successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Expense Supplier update failed.', 'alert-type' => 'error']);
        }
    }

    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.delete');
        try {
            $this->expenseSupplierService->delete($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Expense Supplier deleted successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Expense Supplier deletion failed.', 'alert-type' => 'error']);
        }
    }

    public function duePay($id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.due_pay');
        $supplier = $this->expenseSupplierService->find($id);
        $accounts = $this->accountsService->all()->with('bank')->get();
        return view('expense::expense-suppliers.due-pay', compact('supplier', 'accounts'));
    }

    public function duePayStore(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.due_pay');
        $rule = [
            'expense_id' => 'required|array',
            'expense_id.*' => 'required',
            'amount' => 'required|array',
            'amount.*' => 'numeric',
            'payment_date' => 'required|date',
            'paying_amount' => 'required',
            'payment_type' => 'required',
        ];

        $message = [
            'expense_id.required' => 'Expense is required',
            'amount.required' => 'Amount is required',
            'date.required' => 'Date is required',
            'paying_amount.required' => 'Paying amount is required',
            'payment_type.required' => 'Payment type is required',
        ];

        $request->validate($rule, $message);
        DB::beginTransaction();
        try {
            $this->expenseSupplierService->duePay($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Due payment successful.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Due payment failed.', 'alert-type' => 'error']);
        }
    }

    public function duePayHistory()
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.due_pay');
        $payments = $this->expenseSupplierService->duePayHistory();

        $data['total'] = $payments->sum('amount');

        $perPage = 20;
        if (request('par-page')) {
            $perPage = request('par-page') == 'all' ? $payments->count() : request('par-page');
        }

        $payments = $payments->paginate($perPage);
        $payments->appends(request()->query());

        if (request('export_pdf')) {
            return view('expense::expense-suppliers.pdf.due-pay-history', ['payments' => $payments, 'data' => $data]);
        }

        return view('expense::expense-suppliers.due-pay-history', compact('payments', 'data'));
    }

    public function duePayDelete($id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.due_pay');

        $this->expenseSupplierService->duePayDelete($id);

        return back()->with(['messege' => 'Payment deleted successfully', 'alert-type' => 'success']);
    }

    public function changeStatus($id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.edit');
        $supplier = $this->expenseSupplierService->find($id);

        $status = $supplier->status == 1 ? 0 : 1;
        $supplier->status = $status;
        $supplier->save();

        $notification = $status == 1 ? 'Expense Supplier activated' : 'Expense Supplier deactivated';

        return response()->json(['status' => 'success', 'message' => $notification]);
    }

    public function advance($id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.advance');
        $supplier = $this->expenseSupplierService->find($id);
        $accounts = $this->accountsService->all()->with('bank')->get();
        return view('expense::expense-suppliers.advance', compact('supplier', 'accounts'));
    }

    public function advanceStore(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.advance');
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
                $validator->errors()->add('paying_amount', 'Either paying_amount or refund_amount must be provided.');
                $validator->errors()->add('refund_amount', 'Either paying_amount or refund_amount must be provided.');
            } elseif (!is_null($request->paying_amount) && !is_null($request->refund_amount)) {
                $validator->errors()->add('paying_amount', 'Only one of paying_amount or refund_amount can be provided.');
                $validator->errors()->add('refund_amount', 'Only one of paying_amount or refund_amount can be provided.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $this->expenseSupplierService->advancePay($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Advance payment successful.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense-suppliers.index', [], ['messege' => 'Advance payment failed.', 'alert-type' => 'error']);
        }
    }

    public function ledger($id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.ledger');
        $supplier = $this->expenseSupplierService->find($id);

        $ledgers = Ledger::where('expense_supplier_id', $supplier->id)->orderBy('date', 'desc')->paginate(20);
        $ledgers->appends(request()->query());

        $title = __('Expense Supplier Ledger');

        if (request('export_pdf')) {
            return view('expense::expense-suppliers.pdf.ledger', ['ledgers' => $ledgers, 'title' => $title, 'supplier' => $supplier]);
        }

        return view('expense::expense-suppliers.ledger', compact('ledgers', 'title', 'supplier'));
    }

    public function ledgerDetails($id)
    {
        checkAdminHasPermissionAndThrowException('expense_supplier.ledger');
        $ledger = Ledger::with('details', 'expenseSupplier')->find($id);
        return view('expense::expense-suppliers.ledger-details', compact('ledger'));
    }

    public function getSuppliers()
    {
        $suppliers = $this->expenseSupplierService->all()->where('status', 1)->get();
        return response()->json($suppliers);
    }
}
