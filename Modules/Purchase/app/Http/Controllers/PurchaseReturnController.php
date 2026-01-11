<?php

namespace Modules\Purchase\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Models\Account;
use Modules\Purchase\app\Services\PurchaseService;

class PurchaseReturnController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private PurchaseService $purchaseService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('purchase.return.view');
        $returns = $this->purchaseService->allReturn();

        if (request()->keyword) {
            $returns = $returns->where(function ($q) {
                $q->where('invoice', 'like', '%' . request()->keyword . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('name', 'like', '%' . request()->keyword . '%');
                    })
                ;
            });
        }
        if (request('from_date') && request('to_date')) {
            $returns = $returns->whereBetween('return_date', [now()->parse(request('from_date')), now()->parse(request('to_date'))]);
        }
        if (request()->order_by) {
            $returns = $returns->orderBy('return_date', request()->order_by);
        } else {
            $returns = $returns->orderBy('return_date', 'desc');
        }
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        $returns = $returns->paginate($parpage);
        $returns->appends(request()->query());

        return view('purchase::return.index', compact('returns'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        checkAdminHasPermissionAndThrowException('purchase.return.create');
        $purchase = $this->purchaseService->getPurchase($id);
        $returnTypes = $this->purchaseService->getReturnTypes();
        $accounts = $this->purchaseService->getAccounts();
        return view('purchase::return.create', compact('purchase', 'returnTypes', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('purchase.return.create');
        $request->validate([
            'supplier_id' => 'required',
            'return_date' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $this->purchaseService->storeReturn($request, $id);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.purchase.return.index', [], ['messege' => 'Purchase Return Created Successfully', 'alert-type' => 'success']);
        } catch (Exception $ex) {

            DB::rollBack();
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.purchase.return.index', [], ['messege' => 'Something Went Wrong', 'alert-type' => 'error']);
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('purchase.return.edit');
        $return = $this->purchaseService->getPurchaseReturn($id);
        $purchase = $return->purchase;
        $returnTypes = $this->purchaseService->getReturnTypes();
        $accounts = $this->purchaseService->getAccounts();
        $payment = $return->payment;
        return view('purchase::return.edit', compact('return', 'purchase', 'returnTypes', 'accounts', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('purchase.return.edit');
        $request->validate([
            'supplier_id' => 'required',
            'return_date' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $this->purchaseService->updateReturn($request, $id);

            DB::commit();
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.purchase.return.index', [], ['messege' => 'Purchase Return Created Successfully', 'alert-type' => 'success']);
        } catch (Exception $ex) {

            DB::rollBack();
            Log::error($ex->getMessage());
            return $this->redirectWithMessage(RedirectType::ERROR->value, 'admin.purchase.return.index', [], ['messege' => 'Something Went Wrong', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('purchase.return.delete');
        // delete ledger

        $this->purchaseService->deleteReturn($id);


        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.purchase.return.index', [], ['messege' => 'Purchase Return Deleted Successfully', 'alert-type' => 'success']);
    }
}
