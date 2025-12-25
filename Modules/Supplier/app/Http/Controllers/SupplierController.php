<?php

namespace Modules\Supplier\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\LedgerExport;
use App\Exports\SupplierDuePaidExport;
use App\Exports\SupplierExport;
use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Traits\RedirectHelperTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\Supplier\app\Models\SupplierPayment;
use Modules\Supplier\app\Services\SupplierService;

class SupplierController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private SupplierService $supplierService, private UserGroupService $userGroup, private AreaService $areaService, private AccountsService $accountsService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('supplier.view');
        $suppliers = $this->supplierService->allSupplier();

        if (checkAdminHasPermission('supplier.excel.download')) {
            if (request('export')) {
                $fileName = 'suppliers-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new SupplierExport($suppliers), $fileName);
            }
        }


        $data['totalPurchase'] = 0;
        $data['pay'] = 0;
        $data['total_return'] = 0;
        $data['total_return_pay'] = 0;
        $data['total_due'] = 0;
        $data['total_advance'] = 0;
        $data['total_due_dismiss'] = 0;

        $supplierData = request()->order_type ? $suppliers : $suppliers->get();

        foreach ($supplierData as $supplier) {
            $data['totalPurchase'] += $supplier->purchases->sum('total_amount');
            $data['pay'] += $supplier->payments->sum('amount');

            $totalReturn = $supplier->purchaseReturn->sum('return_amount');
            $data['total_return'] += $totalReturn;

            $data['total_return_pay'] += $supplier->purchaseReturn->sum(
                'received_amount',
            );

            $data['total_due'] += $supplier->total_due - $totalReturn;
            $data['total_advance'] += $supplier->advance;
            $data['total_due_dismiss'] += $supplier->total_due_dismiss;
        }

        if (checkAdminHasPermission('supplier.pdf.download')) {
            if (request('export_pdf')) {
                return view('supplier::pdf.supplier', ['suppliers' => $supplierData,  'data' => $data]);

                // $pdf = $fileName = 'suppliers-' . date('Y-m-d') . '_' . date('h-i-s') . '.pdf';

                // $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape')->setOption('isRemoteEnabled', true)->setOption('enable_javascript')->setWarnings(false);
                // return $pdf->stream($fileName);
            }
        }


        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $perPage = $suppliers->count();
            } else {

                $perPage = request('par-page');
            }
        } else {
            $perPage = 20;
        }



        if (request()->order_type) {
            // Convert sorted collection to paginate manually
            $page = request('page', 1); // Default to page 1
            $paginatedSuppliers = $suppliers->slice(($page - 1) * $perPage, $perPage)->values();
        }


        // Create LengthAwarePaginator

        if (request()->order_type) {
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

        $groups = $this->userGroup->getUserGroup('dropdown')->where('type', 'supplier')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();


        return view('supplier::index', compact('suppliers', 'groups', 'areaList', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        checkAdminHasPermissionAndThrowException('supplier.store');
        $request->validate(['name' => 'required']);

        try {
            $this->supplierService->storeSupplier($request);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier created successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier creation failed.', 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('supplier::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        return view('supplier::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('supplier.update');
        $request->validate(['name' => 'required']);

        try {
            $this->supplierService->updateSupplier($request, $id);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier updated successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier update failed.', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('supplier.delete');
        try {
            $this->supplierService->deleteSupplier($id);
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier deleted successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier deletion failed.', 'alert-type' => 'error']);
        }
    }


    public function duePay($id)
    {
        checkAdminHasPermissionAndThrowException('supplier.due.pay');
        $supplier = $this->supplierService->find($id);
        $accounts = $this->accountsService->all()->with('bank')->get();
        return view('supplier::due-pay', compact('supplier', 'accounts'));
    }

    public function duePayStore(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('supplier.due.pay');
        $rule = [
            'invoice_no' => 'required|array',
            'invoice_no.*' => 'required',
            'amount' => 'required|array',
            'amount.*' => 'numeric',
            'payment_date' => 'required|date',
            'paying_amount' => 'required',
            'payment_type' => 'required',
        ];

        $message = [
            'invoice_no.required' => 'Invoice number is required',
            'amount.required' => 'Amount is required',
            'date.required' => 'Date is required',
            'paying_amount.required' => 'Paying amount is required',
            'payment_type.required' => 'Payment type is required',
        ];

        $request->validate($rule, $message);
        DB::beginTransaction();
        try {
            $this->supplierService->duePay($request, $id);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Due payment successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Due payment failed.', 'alert-type' => 'error']);
        }
    }

    public function duePayHistory()
    {
        checkAdminHasPermissionAndThrowException('supplier.due.pay.list');
        $payments = $this->supplierService->duePayHistory();

        if (request('export')) {
            $fileName = 'supplier-due-pay-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new SupplierDuePaidExport($payments->get()), $fileName);
        }
        $data['total'] = $payments->sum('amount');


        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $payments = $payments->paginate();
            } else {
                $payments = $payments->paginate(request('par-page'));
                $payments->appends(request()->query());
            }
        } else {
            $payments = $payments->paginate(20);
            $payments->appends(request()->query());
        }



        if (request('export_pdf')) {
            return view('supplier::pdf.due-pay-list', ['payments' => $payments,  'data' => $data]);
        }

        return view('supplier::due-pay-history', compact('payments', 'data'));
    }

    public function dueReceiveDelete($id)
    {
        checkAdminHasPermissionAndThrowException('supplier.due.pay.delete');

        $payments = $this->supplierService->dueReceiveDelete($id);

        return back()->with(['messege' => 'Payment deleted successfully', 'alert-type' => 'success']);
    }

    public function changeStatus($id)
    {
        checkAdminHasPermissionAndThrowException('supplier.status');
        $supplier = $this->supplierService->find($id);

        $status = $supplier->status == 1 ? 0 : 1;

        $supplier->status = $status;
        $supplier->save();

        $notification = $status == 1 ? 'Supplier activated' : 'Supplier deactivated';

        return response()->json(['status' => 'success', 'message' => $notification]);
    }

    public function advance($id)
    {
        checkAdminHasPermissionAndThrowException('supplier.advance');
        $supplier = $this->supplierService->find($id);
        $accounts = $this->accountsService->all()->with('bank')->get();
        return view('supplier::advance', compact('supplier', 'accounts'));
    }

    public function advanceStore(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('supplier.advance');
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
            $this->supplierService->advancePay($request, $id);
            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Advance payment successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.suppliers.index', [], ['messege' => 'Advance payment failed.', 'alert-type' => 'error']);
        }
    }

    public function ledger($id)
    {
        checkAdminHasPermissionAndThrowException('supplier.ledger');
        $supplier = $this->supplierService->find($id);

        $ledgers = Ledger::where('supplier_id', $supplier->id)->orderBy('date', 'desc')->paginate(20);
        $ledgers->appends(request()->query());

        $title = __('Supplier Ledger');


        if (request('export')) {
            $fileName = 'supplier-ledger-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new LedgerExport($ledgers, $title), $fileName);
        }

        if (request('export_pdf')) {
            return view('supplier::pdf.ledger', ['ledgers' => $ledgers,  'title' => $title]);
        }
        return view('supplier::ledger', compact('ledgers', 'title'));
    }

    public function ledgerDetails($id)
    {
        checkAdminHasPermissionAndThrowException('supplier.ledger');
        $ledger = Ledger::with('details', 'supplier')->find($id);
        return view('supplier::ledger-details', compact('ledger'));
    }


    public function bulkImport()
    {
        checkAdminHasPermissionAndThrowException('supplier.bulk.import');
        return view('supplier::bulk-import');
    }

    public function bulkImportStore(Request $request)
    {
        checkAdminHasPermissionAndThrowException('supplier.bulk.import');
        $request->validate(['file' => 'required']);
        try {
            $this->supplierService->bulkImport($request);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier imported successfully.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.suppliers.index', [], ['messege' => 'Supplier imported failed.', 'alert-type' => 'error']);
        }
    }
}
