<?php

namespace Modules\Sales\app\Http\Controllers;

use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Models\Account;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\POS\app\Models\CartHold;
use Modules\Ingredient\app\Models\IngredientCategory;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Services\BrandService;
use Modules\Sales\app\Services\SaleService;
use Modules\Service\app\Services\ServicesService;

class SalesController extends Controller
{
    public function __construct(private UserGroupService $userGroup, private SaleService $saleService, private BrandService $brandService, private AreaService $areaService, private ServicesService $services)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('sales.view');
        $sales = $this->saleService->getSales();

        if (request()->keyword !== null) {
            $sales = $sales->where(function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . request()->keyword . '%')
                        ->orWhere('email', 'like', '%' . request()->keyword . '%')
                        ->orWhere('phone', 'like', '%' . request()->keyword . '%')
                        ->orWhere('address', 'like', '%' . request()->keyword . '%');
                })->orWhere('invoice', 'like', '%' . request()->keyword . '%');
            });
        }

        $fromDate = request('from_date') ? now()->parse(request('from_date'))->format('Y-m-d') : '';
        $toDate = request('to_date') ? now()->parse(request('to_date'))->format('Y-m-d') : date('Y-m-d');


        // from date and to date
        if (request('from_date') || request('to_date')) {
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }
        $sort = request()->order_by ? request()->order_by : 'desc';
        $sales = $sales->orderBy('order_date', $sort)->orderBy('invoice', $sort);

        $data['sale_amount'] = 0;
        $data['total_amount'] = 0;
        $data['paid_amount'] = 0;
        $data['due_amount'] = 0;

        foreach ($sales->get() as $sale) {
            $data['sale_amount'] += $sale->total_price;
            $data['total_amount'] += $sale->grand_total;
            $data['paid_amount'] += $sale->paid_amount;
            $data['due_amount'] += $sale->due_amount;
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $sales = $sales->get();
        } else {
            $sales = $sales->paginate($parpage);
            $sales->appends(request()->query());
        }

        if (checkAdminHasPermission('sales.excel.download')) {
            if (request('export')) {
                $fileName = 'sales-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new SalesExport($sales), $fileName);
            }
        }

        if (checkAdminHasPermission('sales.pdf.download')) {
            if (request('export_pdf')) {
                return view('sales::pdf.sales', [
                    'sales' => $sales,
                ]);
            }
        }

        $title = 'Sales List';
        return view('sales::index', compact('sales', 'title', 'data'));
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        checkAdminHasPermissionAndThrowException('sales.view');
        $sale = $this->saleService->getSales()->find($id);
        return view('sales::view-modal', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('sales.edit');

        session()->forget('UPDATE_CART');
        [$cart_contents, $sale] = $this->saleService->editSale($id);
        $products = Ingredient::where('status', 1)->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->orderBy('id', 'desc');

        if ($request->category_id) {
            $products = $products->where(function ($query) use ($request) {
                $query->where('category_id', $request->category_id)->where('status', 1);
            });
        }

        if ($request->name) {
            $products = $products->whereHas('translations', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            });
        }

        $products = $products->paginate(5);

        $products = $products->appends($request->all());

        $categories = IngredientCategory::where('status', 1)->get();
        $brands = $this->brandService->getActiveBrands();
        $customers = User::orderBy('name', 'asc')->where('status', 1)->get();
        $accounts = Account::with('bank')->get();
        $groups = $this->userGroup->getUserGroup()->where('type', 'customer')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();

        $services = $this->services->all()->where('status', 1)->paginate(20);
        $services->appends(request()->query());

        $serviceCategories = $this->services->getCategories();

        $cart_holds = CartHold::where('status', 'hold')->orderBy('id', 'desc')->get();
        return view('sales::edit')->with([
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers,
            'cart_contents' => $cart_contents,
            'brands' => $brands,
            'groups' => $groups,
            'accounts' => $accounts,
            'areaList' => $areaList,
            'services' => $services,
            'cart_holds' => $cart_holds,
            'serviceCategories' => $serviceCategories,
            'sale' => $sale
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        checkAdminHasPermissionAndThrowException('sales.edit');
        $user = null;
        if ($request->order_customer_id && $request->order_customer_id !=  'walk-in-customer') {

            Validator::make($request->all(), [
                'order_customer_id' => 'required',
            ], [
                'order_customer_id.required' => trans('Customer is required'),
            ])->validate();

            $user = User::find($request->order_customer_id);
        }

        $cart = session('UPDATE_CART');

        DB::beginTransaction();
        try {
            $order = $this->saleService->updateSale($request, $user,  $cart, $id);
            DB::commit();
            session()->put('UPDATE_CART', []);
            return response()->json([
                'order' => $order,
                'message' => 'Order Updated successfully',
                'alert-type' => 'success',
            ], 200);
        } catch (Exception $ex) {
            DB::rollBack();
            Log::error($ex->getMessage());

            return response()->json([
                'message' => $ex->getMessage(),
                'alert-type' => 'error',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('sales.delete');
        try {
            $this->saleService->deleteSale($id);
            return back()->with(['alert-type' => 'success', 'messege' => 'Sale deleted successfully']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return back()->with(['alert-type' => 'danger', 'messege' => 'Something went wrong!']);
        }
    }

    public function invoice($id)
    {
        checkAdminHasPermissionAndThrowException('sales.invoice');
        $sale = $this->saleService->getSales()->find($id);
        return view('sales::invoice', compact('sale'));
    }
}
