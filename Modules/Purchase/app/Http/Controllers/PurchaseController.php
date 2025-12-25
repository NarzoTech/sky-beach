<?php

namespace Modules\Purchase\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\PurchaseExport;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Purchase\app\Http\Requests\PurchaseRequest;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Services\PurchaseService;

class PurchaseController extends Controller
{

    use RedirectHelperTrait;
    public function __construct(private PurchaseService $purchaseService, private PurchaseDetails $purchaseDetails)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('purchase.view');
        $purchases = $this->purchaseService->all();


        $data['total_amount'] = 0;
        $data['paid_amount'] = 0;
        $data['due_amount'] = 0;

        foreach ($purchases->get() as $purchase) {

            $data['total_amount'] += $purchase->total_amount;
            $data['paid_amount'] += $purchase->paid_amount;
            $data['due_amount'] += $purchase->due_amount;
        }

        if (checkAdminHasPermission('purchase.excel.download')) {
            if (request('export')) {
                $fileName = 'purchase-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new PurchaseExport($purchases->get()), $fileName);
            }
        }

        if (checkAdminHasPermission('purchase.pdf.download')) {
            if (request('export_pdf')) {
                return view('purchase::pdf.purchase', [
                    'purchases' => $purchases->get(),
                ]);
            }
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $purchases = $purchases->get();
        } else {
            $purchases = $purchases->paginate($parpage);
            $purchases->appends(request()->query());
        }

        $products = $this->purchaseService->getProducts(request());
        return view('purchase::index', compact('purchases', 'products', 'data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        checkAdminHasPermissionAndThrowException('purchase.create');
        $suppliers = $this->purchaseService->getSuppliers();
        $warehouses = $this->purchaseService->getWarehouses();
        $products = $this->purchaseService->getProducts($request);
        $invoiceNumber = $this->purchaseService->genInvoiceNumber();

        $accounts = $this->purchaseService->getAccounts();
        return view('purchase::create', compact('suppliers', 'warehouses', 'products', 'invoiceNumber', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PurchaseRequest $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('purchase.create');
        DB::beginTransaction();
        try {
            $this->purchaseService->store($request);

            DB::commit();

            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.purchase.index', [], ['messege' => 'Product Purchase successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('purchase::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('purchase.edit');
        $suppliers = $this->purchaseService->getSuppliers();
        $warehouses = $this->purchaseService->getWarehouses();
        $products = $this->purchaseService->getProducts($request);
        $invoiceNumber = $this->purchaseService->genInvoiceNumber();
        $accounts = $this->purchaseService->getAccounts();
        $purchase = $this->purchaseService->getPurchase($id);
        return view('purchase::edit', compact('suppliers', 'warehouses', 'products', 'invoiceNumber', 'purchase', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PurchaseRequest $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('purchase.edit');
        DB::beginTransaction();
        try {
            $this->purchaseService->update($request, $id);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.purchase.index', [], ['messege' => 'Product Purchase successfully', 'alert-type' => 'success']);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            DB::rollBack();
            return $this->redirectWithMessage(RedirectType::ERROR->value, null, [], ['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('purchase.delete');
        try {
            $this->purchaseService->destroy($id);

            DB::commit();
            return back()->with(['messege' => 'Product Purchase deleted successfully', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return back()->with(['messege' => 'Something went wrong', 'alert-type' => 'error']);
        }
    }

    public function invoice($id)
    {
        checkAdminHasPermissionAndThrowException('purchase.invoice');
        $purchase = $this->purchaseService->getPurchase($id);
        return view('purchase::invoice', compact('purchase'));
    }
}
