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
use Modules\Ingredient\app\Models\Category;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Ingredient\app\Services\BrandService;
use Modules\Sales\app\Services\SaleService;
use Modules\Service\app\Services\ServicesService;
use Modules\Sales\app\Models\Sale;
use App\Models\Payment;

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

        // Filter by payment status (default: show only unpaid/due)
        $paymentStatus = request('payment_status', 'due'); // default to 'due'
        if ($paymentStatus === 'due') {
            $sales = $sales->whereRaw('paid_amount < grand_total');
        } elseif ($paymentStatus === 'paid') {
            $sales = $sales->whereRaw('paid_amount >= grand_total');
        }
        // 'all' shows everything

        // Filter by waiter
        if (request('waiter_id')) {
            $sales = $sales->where('waiter_id', request('waiter_id'));
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

        // Get waiters for filter dropdown
        $waiters = \Modules\Employee\app\Models\Employee::activeWaiters()->orderBy('name')->get();

        // Get accounts for payment modal
        $accounts = Account::with('bank')->get();

        // Get POS settings for payment modal
        $posSettings = \Modules\POS\app\Models\PosSettings::first();

        $title = 'Sales List';
        return view('sales::index', compact('sales', 'title', 'data', 'waiters', 'accounts', 'posSettings'));
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

        $categories = \Modules\Ingredient\app\Models\Category::where('status', 1)->get();
        $brands = $this->brandService->getActiveBrands();
        $customers = User::orderBy('name', 'asc')->where('status', 1)->get();
        $accounts = Account::with('bank')->get();
        $groups = $this->userGroup->getUserGroup()->where('type', 'customer')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();

        $services = $this->services->all()->where('status', 1)->paginate(20);
        $services->appends(request()->query());

        $serviceCategories = $this->services->getCategories();

        $cart_holds = CartHold::where('status', 'hold')->orderBy('id', 'desc')->get();
        $posSettings = \Modules\POS\app\Models\PosSettings::first();
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
            'sale' => $sale,
            'posSettings' => $posSettings
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

    /**
     * Get sale details for payment modal
     */
    public function getSaleDetails($id): JsonResponse
    {
        $sale = Sale::with(['details.ingredient', 'details.menuItem', 'details.service', 'details.combo', 'customer', 'table', 'waiter', 'createdBy'])->find($id);

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Sale not found'], 404);
        }

        $items = [];
        foreach ($sale->details as $detail) {
            $itemName = '-';
            $itemImage = null;

            if ($detail->combo_id && $detail->combo) {
                $itemName = $detail->combo_name ?? $detail->combo->name ?? '-';
                $itemImage = $detail->combo->image ?? null;
            } elseif ($detail->ingredient_id && $detail->ingredient) {
                $itemName = $detail->ingredient->name ?? '-';
                $itemImage = $detail->ingredient->single_image ?? null;
            } elseif ($detail->menu_item_id && $detail->menuItem) {
                $itemName = $detail->menuItem->name ?? '-';
                $itemImage = $detail->menuItem->image ?? null;
            } elseif ($detail->service_id && $detail->service) {
                $itemName = $detail->service->name ?? '-';
                $itemImage = $detail->service->single_image ?? null;
            }

            $items[] = [
                'name' => $itemName,
                'image' => $itemImage ? asset($itemImage) : null,
                'quantity' => $detail->quantity,
                'price' => (float) $detail->price,
                'sub_total' => (float) $detail->sub_total,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $sale->id,
                'invoice' => $sale->invoice,
                'order_date' => $sale->order_date ? $sale->order_date->format('d M Y') : '-',
                'order_type' => $sale->order_type ? ucfirst(str_replace('_', ' ', $sale->order_type)) : '-',
                'customer' => [
                    'name' => $sale->customer->name ?? 'Guest',
                    'phone' => $sale->customer->phone ?? null,
                ],
                'table' => $sale->table ? $sale->table->name : null,
                'guest_count' => $sale->guest_count,
                'waiter' => $sale->waiter ? $sale->waiter->name : null,
                'items' => $items,
                'subtotal' => (float) $sale->total_price,
                'discount' => (float) $sale->order_discount,
                'tax' => (float) $sale->total_tax,
                'grand_total' => (float) $sale->grand_total,
                'paid_amount' => (float) $sale->paid_amount,
                'due_amount' => (float) $sale->due_amount,
                'created_by' => $sale->createdBy->name ?? '-',
            ]
        ]);
    }

    /**
     * Receive payment against a sale/order
     */
    public function receivePayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required|exists:sales,id',
            'receive_amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|in:cash,card,bank,mobile_banking',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $sale = Sale::findOrFail($request->sale_id);
            $receiveAmount = (float) $request->receive_amount;
            $dueAmount = (float) $sale->due_amount;

            // Amount to apply to the order (can't be more than due)
            $payingAmount = min($receiveAmount, $dueAmount);
            $changeAmount = max(0, $receiveAmount - $dueAmount);

            // Get or find cash account if payment type is cash
            $accountId = $request->account_id;
            if ($request->payment_type === 'cash' && !$accountId) {
                $cashAccount = \Modules\Accounts\app\Models\Account::where('account_type', 'cash')->first();
                $accountId = $cashAccount?->id;
            }

            // Create payment record (customer_id must be null if 0 due to foreign key)
            $customerId = $sale->customer_id > 0 ? $sale->customer_id : null;

            $payment = Payment::create([
                'sale_id' => $sale->id,
                'customer_id' => $customerId,
                'account_id' => $accountId,
                'payment_type' => $request->payment_type,
                'amount' => $payingAmount,
                'payment_date' => now()->format('Y-m-d'),
                'note' => $request->note,
                'is_received' => 1,
                'is_paid' => 1,
                'created_by' => auth('admin')->id(),
            ]);

            // Update sale paid amount and due amount
            $newPaidAmount = (float) $sale->paid_amount + $payingAmount;
            $newDueAmount = max(0, (float) $sale->grand_total - $newPaidAmount);

            $sale->paid_amount = $newPaidAmount;
            $sale->due_amount = $newDueAmount;
            $sale->return_amount = ($sale->return_amount ?? 0) + $changeAmount;

            // Update status if fully paid
            if ($newDueAmount <= 0) {
                $sale->status = 'completed'; // Completed
            }

            $sale->save();

            DB::commit();

            $message = __('Payment of :amount received successfully', ['amount' => currency($payingAmount)]);
            if ($changeAmount > 0) {
                $message .= '. ' . __('Change: :change', ['change' => currency($changeAmount)]);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'payment_id' => $payment->id,
                    'paid_amount' => $newPaidAmount,
                    'due_amount' => $newDueAmount,
                    'change_amount' => $changeAmount,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error receiving payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Failed to process payment: ') . $e->getMessage()
            ], 500);
        }
    }
}
