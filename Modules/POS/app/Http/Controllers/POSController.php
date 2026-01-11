<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\OrderSuccessfulMailJob;
use App\Models\Address;
use App\Models\Quotation;
use App\Models\User;
use App\Models\Variant;
use Exception;
use Modules\Order\app\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Modules\Accounts\app\Models\Account;
use Modules\Customer\app\Http\Services\AreaService;
use Modules\Customer\app\Http\Services\UserGroupService;
use Modules\GlobalSetting\app\Models\EmailTemplate;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\OrderDetails;
use Modules\POS\app\Models\CartHold;
use Modules\Menu\app\Models\MenuCategory;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Services\MenuItemService;
use Modules\Sales\app\Services\SaleService;
use Modules\Service\app\Services\ServicesService;
use Modules\TableManagement\app\Models\RestaurantTable;
use Modules\Sales\app\Models\Sale;
use Modules\Membership\app\Services\LoyaltyService;
use Modules\Membership\app\Models\LoyaltyProgram;

class POSController extends Controller
{
    protected $menuItemService;
    protected $orderService;
    protected $loyaltyService;

    public function __construct(private UserGroupService $userGroup, MenuItemService $menuItemService, OrderService $orderService, private AreaService $areaService, private SaleService $saleService, private ServicesService $services, LoyaltyService $loyaltyService)
    {
        $this->middleware('auth:admin');
        $this->menuItemService = $menuItemService;
        $this->orderService = $orderService;
        $this->loyaltyService = $loyaltyService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        checkAdminHasPermissionAndThrowException('pos.view');
        if ($request->quotation_id) {
            $quotation = Quotation::find($request->quotation_id);

            foreach ($quotation->details as $detail) {
                $menuItem = MenuItem::find($detail->menu_item_id ?? $detail->product_id);
                $newReq = Request();
                $newReq->menu_item_id = $menuItem->id;
                $newReq->qty = $detail->quantity;
                $newReq->type = $menuItem->variants()->count() > 0 ? 'variant' : 'single';
                $newReq->serviceType = 'menu_item';
                $newReq->variant_price = $detail->price;

                $this->add_to_cart($newReq);
            }
        }

        Paginator::useBootstrap();

        $menuItems = MenuItem::active()->available()
            ->where(function ($query) {
                $query->whereNull('category_id')
                    ->orWhereHas('category', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->orderBy('display_order', 'asc');

        if ($request->category_id) {
            $menuItems = $menuItems->where('category_id', $request->category_id);
        }

        if ($request->name) {
            $menuItems = $menuItems->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('sku', 'LIKE', '%' . $request->name . '%');
            });
        }

        $menuItems = $menuItems->paginate(15);

        $menuItems->appends(request()->query());

        $categories = MenuCategory::where('status', 1)->orderBy('display_order', 'asc')->get();
        $customers = User::orderBy('name', 'asc')->where('status', 1)->get();

        $cart_contents = session('POSCART') ?? [];
        $accounts = Account::with('bank')->get();
        $groups = $this->userGroup->getUserGroup()->where('type', 'customer')->where('status', 1)->get();
        $areaList = $this->areaService->getArea()->get();

        $services = $this->services->all()->where('status', 1)->paginate(15);

        $services->appends(request()->query());

        $serviceCategories = $this->services->getCategories();

        $cart_holds = CartHold::where('status', 'hold')->orderBy('id', 'desc')->get();

        // Load available tables for dine-in orders
        $availableTables = [];
        try {
            $availableTables = RestaurantTable::active()
                ->with(['activeOrders'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            // Sync occupied_seats based on active orders (ensures accuracy)
            foreach ($availableTables as $table) {
                $activeGuestCount = $table->activeOrders->sum('guest_count') ?: $table->activeOrders->count();
                if ($table->occupied_seats != $activeGuestCount) {
                    $table->occupied_seats = $activeGuestCount;
                    $table->status = $activeGuestCount > 0 ? 'occupied' : 'available';
                    $table->save();
                }
            }
        } catch (\Exception $e) {
            // Tables module might not be installed yet
        }

        // Load active loyalty program
        $loyaltyProgram = null;
        try {
            $loyaltyProgram = LoyaltyProgram::where('is_active', 1)->first();
        } catch (\Exception $e) {
            // Membership module might not be installed
        }

        // Check if we're editing an existing order
        $editingOrderId = session('EDITING_ORDER_ID');
        $editingTableId = session('EDITING_TABLE_ID');
        $editingTableName = session('EDITING_TABLE_NAME');

        // Load waiters/employees for dine-in orders
        $waiters = [];
        try {
            $waiters = \Modules\Employee\app\Models\Employee::where('status', 1)
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // Employee module might not be installed
        }

        return view('pos::index')->with([
            'menuItems' => $menuItems,
            'categories' => $categories,
            'customers' => $customers,
            'cart_contents' => $cart_contents,
            'groups' => $groups,
            'accounts' => $accounts,
            'areaList' => $areaList,
            'services' => $services,
            'cart_holds' => $cart_holds,
            'serviceCategories' => $serviceCategories,
            'availableTables' => $availableTables,
            'loyaltyProgram' => $loyaltyProgram,
            'editingOrderId' => $editingOrderId,
            'editingTableId' => $editingTableId,
            'editingTableName' => $editingTableName,
            'waiters' => $waiters,
        ]);
    }

    public function load_products(Request $request)
    {
        Paginator::useBootstrap();

        $menuItems = MenuItem::where('status', 1)->where('is_available', 1)
            ->where(function ($query) {
                $query->whereNull('category_id')
                    ->orWhereHas('category', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->orderBy('display_order', 'asc');

        if ($request->category_id) {
            $menuItems = $menuItems->where('category_id', $request->category_id);
        }

        if ($request->name) {
            $menuItems = $menuItems->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('sku', 'LIKE', '%' . $request->name . '%');
            });
        }


        // Paginate featured menu items (clone to avoid modifying original query)
        $featuredItems = (clone $menuItems)->featured()->paginate(15);
        $featuredItems->appends(request()->query());  // Append request parameters

        // Paginate non-featured menu items (use clone of original query)
        $nonFeaturedItems = (clone $menuItems)->paginate(15);
        $nonFeaturedItems->appends(request()->query()); // Append request parameters



        $services = $this->services->all()->where('status', 1);

        if ($request->service_name) {
            $services = $services->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->service_name . '%');
            });
        }


        if ($request->service_category_id) {
            $services = $services->where('category_id', $request->service_category_id);
        }


        $favoriteServices = clone $services; // Clone the query to avoid conflicts
        $favoriteServices = $favoriteServices->where('is_favourite', 1)->paginate(15);
        $favoriteServices->appends(request()->query());


        $services = $services->paginate(15);
        $services->appends(request()->query());


        $serviceView = view('pos::ajax_service')->with([
            'services' => $services,
        ])->render();

        $favoriteServiceView = view('pos::ajax_service')->with([
            'services' => $favoriteServices,
        ])->render();

        $menuItemView =  view('pos::ajax_menu_items')->with([
            'menuItems' => $nonFeaturedItems,
        ])->render();

        $featuredMenuItemView =  view('pos::ajax_menu_items')->with([
            'menuItems' => $featuredItems,
        ])->render();

        return response()->json(['productView' => $menuItemView, 'serviceView' => $serviceView, 'favProductView' => $featuredMenuItemView, 'favoriteServiceView' => $favoriteServiceView]);
    }

    public function featuredMenuItems($menuItems)
    {
        $menuItems = $menuItems->where('is_featured', 1)->paginate(15);

        $menuItems->appends(request()->query());
        return $menuItems;
    }

    public function load_products_list(Request $request)
    {

        $menuItems = MenuItem::active()->available()
            ->where(function ($query) {
                $query->whereNull('category_id')
                    ->orWhereHas('category', function ($q) {
                        $q->where('status', 1);
                    });
            })
            ->orderBy('display_order', 'asc');

        if ($request->name) {
            $menuItems = $menuItems->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('barcode', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('sku', 'LIKE', '%' . $request->name . '%');
            });
        }
        if ($request->favorite == 1 || $request->featured == 1) {
            $menuItems = $menuItems->featured();
        }

        $menuItems = $menuItems->get();

        $view = view('pos::menu-item-list')->with([
            'menuItems' => $menuItems
        ])->render();

        return response()->json(['view' => $view, 'total' => $menuItems->count(), 'menuItem' => $menuItems->first()]);
    }

    public function load_product_modal($menu_item_id)
    {
        $menuItem = MenuItem::with(['variants', 'activeAddons'])->where('status', 1)->find($menu_item_id);
        if (!$menuItem) {
            $notification = trans('Something went wrong');
            return response()->json(['message' => $notification], 403);
        }

        $variants = $menuItem->activeVariants;

        return view('pos::ajax_menu_item_modal')->with([
            'menuItem' => $menuItem,
            'variants' => $variants,
        ]);
    }

    public function add_to_cart(Request $request)
    {

        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        $type = $request->serviceType ?? 'menu_item';
        if ($type == 'service') {
            //
        }

        // Handle menu item or service
        $menuItem = ($type != 'service') ? MenuItem::with('recipes.ingredient')->find($request->menu_item_id ?? $request->product_id) : null;
        $service = $type == 'service' ? $this->services->find($request->product_id ?? $request->menu_item_id) : null;
        $attributes = '';
        $options = collect([]);
        $variant = null;

        if ($menuItem && $menuItem->variants()->count() > 0 && $request->variant_id) {
            $variant = $menuItem->variants()->find($request->variant_id);
            if ($variant) {
                $attributes = $variant->name ?? '';
            }
        }



        $cart_contents = session()->get($cartName);
        $cart_contents = $cart_contents ? $cart_contents : [];


        // check if item already exist in cart
        $item_exist = false;
        $sku = $type != 'service' ? ($request->variant_sku ? $request->variant_sku : ($menuItem ? $menuItem->sku : '')) : '';
        if (count($cart_contents) > 0) {
            foreach ($cart_contents as $index => $cart_content) {
                if (($sku && $cart_content['sku'] == $sku) || ($service && $cart_content['id'] == $service->id && $cart_content['type'] == 'service')) {
                    $item_exist = true;
                }
            }
        }

        // if ($item_exist) {
        //     $notification = trans('Item already added');
        //     return response()->json(['message' => $notification, 'cart' => session()->get('POSCART')], 403);
        // }

        $data = array();
        $data["rowid"] = uniqid();
        $data['id'] = $type == 'service' ? $service->id : $menuItem->id;
        $data['name'] = $type == 'service' ? $service->name : $menuItem->name;
        $data['type'] = $type == 'service' ? 'service' : 'menu_item';
        $data['image'] = $type == 'service' ? $service->singleImage : $menuItem->image_url;
        $data['qty'] = $request->qty ? $request->qty : 1;

        // Calculate price - use variant price if applicable, otherwise base_price
        $price = $type == 'service' ? $service->price : ($request->variant_price ?? $menuItem->base_price);
        if ($variant) {
            $price = $menuItem->base_price + ($variant->price_adjustment ?? 0);
        }

        $data['price'] = $price;
        $data['sub_total'] = (float)$data['price'] * $data['qty'];
        $data['sku'] = $sku;
        $data['unit'] = '-'; // Menu items don't have unit like ingredients
        $data['source'] = 1;
        $data['purchase_price'] = $menuItem ? ($menuItem->cost_price ?? 0) : 0;
        $data['selling_price'] = $price;
        $data['variant_id'] = $variant ? $variant->id : null;

        if ($request->type == null && $variant) {
            $data['variant']['attribute'] =  $attributes;
            $data['variant']['options'] =  $options;
        }


        session()->put($cartName, [...$cart_contents, $data["rowid"] => $data]);

        $cart_contents = session($cartName);

        return view('pos::ajax_cart')->with([
            'cart_contents' => $cart_contents,
        ]);
    }

    public function cart_quantity_update(Request $request)
    {
        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        $cart_contents = session()->get($cartName);

        $cart_contents = $cart_contents ? $cart_contents : [];

        $cart_contents[$request->rowid]['qty'] = $request->quantity;
        $cart_contents[$request->rowid]['sub_total'] = (float)$cart_contents[$request->rowid]['price'] * $request->quantity;

        session()->put($cartName, $cart_contents);

        $cart_contents = session()->get($cartName);

        return view('pos::ajax_cart')->with([
            'cart_contents' => $cart_contents,
        ]);
    }

    /**
     * Quick cart quantity update - returns JSON for real-time UI update
     */
    public function cart_quantity_update_quick(Request $request)
    {
        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        $cart_contents = session()->get($cartName, []);

        if (!isset($cart_contents[$request->rowid])) {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }

        $price = (float)$cart_contents[$request->rowid]['price'];
        $quantity = (int)$request->quantity;
        $sub_total = $price * $quantity;

        $cart_contents[$request->rowid]['qty'] = $quantity;
        $cart_contents[$request->rowid]['sub_total'] = $sub_total;

        session()->put($cartName, $cart_contents);

        // Calculate totals
        $total = 0;
        foreach ($cart_contents as $item) {
            $total += $item['sub_total'];
        }

        return response()->json([
            'success' => true,
            'rowid' => $request->rowid,
            'quantity' => $quantity,
            'sub_total' => $sub_total,
            'sub_total_formatted' => currency($sub_total),
            'cart_total' => $total,
            'cart_total_formatted' => currency($total),
        ]);
    }

    public function remove_cart_item(Request $request, $rowId)
    {
        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        $cart_contents = session()->get($cartName);
        $cart_contents = $cart_contents ? $cart_contents : [];
        unset($cart_contents[$rowId]);
        session()->put($cartName, $cart_contents);

        $cart_contents = session()->get($cartName);

        return view('pos::ajax_cart')->with([
            'cart_contents' => $cart_contents,
        ]);
    }

    public function cart_clear(Request $request)
    {
        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }

        session()->put($cartName, []);

        // Clear editing session variables
        session()->forget('EDITING_ORDER_ID');
        session()->forget('EDITING_TABLE_ID');
        session()->forget('EDITING_TABLE_NAME');

        $notification = trans('Cart clear successfully');
        $notification = array('messege' => $notification, 'alert-type' => 'success');
        return redirect()->back()->with($notification);
    }

    public function create_new_customer(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'nullable|unique:users',
            'phone' => 'required',
            "address" => 'required',
            "address_type" => "required",
        ], [
            'first_name.required' => trans('First Name is required'),
            'last_name.required' => trans('Last Name is required'),
            'email.unique' => trans('Email already exist'),
            'phone.required' => trans('Phone is required'),
            'address.required' => trans('Address is required'),
            'address_type.required' => trans('Address Type is required'),
        ])->validate();

        try {
            $user = new User();
            $user->name = $request->first_name . ' ' . $request->last_name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->status = 'active';
            $user->email_verified_at = now();
            $user->save();

            $customers = User::orderBy('name', 'asc')->where('status', 'active')->get();

            $customer_html = "<option value=''>" . trans('Select Customer') . "</option><option value='walk-in-customer'>walk-in-customer</option>";
            foreach ($customers as $customer) {
                $customer_html .= "<option value=" . $customer->id . ">" . $customer->name . "-" . $customer->phone . "</option>";
            }

            $notification = trans('Created Successfully');
            return response()->json(['customer_html' => $customer_html, 'message' => $notification]);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
    }


    public function place_order(Request $request)
    {
        checkAdminHasPermissionAndThrowException('sales.create');

        $cart = session('POSCART');
        if (empty($cart) || count($cart) == 0) {
            return response()->json([
                'message' => trans('Your cart is empty!'),
                'alert-type' => 'error'
            ], 400);
        }

        $user = null;
        if ($request->order_customer_id && $request->order_customer_id !=  'walk-in-customer') {

            $validatedData = Validator::make($request->all(), [
                'order_customer_id' => 'required',
            ], [
                'order_customer_id.required' => trans('Customer is required'),
            ])->validate();

            $user = User::find($request->order_customer_id);
        }


        DB::beginTransaction();
        try {
            $order_result = $this->orderStore($user, $request);
            $sale = $this->saleService->getSales()->find($order_result->id);

            DB::commit();

            // For dine-in deferred payment, don't generate invoice
            $isDeferredPayment = $request->defer_payment ||
                ($sale->order_type === Sale::ORDER_TYPE_DINE_IN && $sale->payment_status == 0);

            if ($isDeferredPayment) {
                return response()->json([
                    'order' => $order_result,
                    'invoice' => null,
                    'invoiceRoute' => null,
                    'is_deferred' => true,
                    'table_name' => $sale->table?->name,
                    'message' => __('Dine-in order started successfully'),
                    'alert-type' => 'success',
                ], 200);
            }

            // Generate invoice for paid orders
            $invoiceBlade = view('sales::invoice-content')->with([
                'sale' => $sale,
                'details' => $sale->details,
            ])->render();

            $invoiceRoute = route('admin.sales.invoice', $order_result->id) . '?print=true';

            return response()->json([
                'order' => $order_result,
                'invoice' => $invoiceBlade,
                'invoiceRoute' => $invoiceRoute,
                'is_deferred' => false,
                'message' => __('Sale created successfully'),
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

    public function calculate_amount($delivery_charge)
    {

        $sub_total = 0;
        $coupon_price = 0.00;

        $cart_contents = session('POSCART');
        foreach ($cart_contents as $index => $cart_content) {
            $item_price = $cart_content['price'] * $cart_content['qty'];
            $item_total = $item_price + $cart_content['options']['optional_item_price'];
            $sub_total += $item_total;
        }

        $grand_total = ($sub_total - $coupon_price) + $delivery_charge;

        return array(
            'sub_total' => $sub_total,
            'coupon_price' => $coupon_price,
            'delivery_charge' => $delivery_charge,
            'grand_total' => $grand_total,
        );
    }

    public function orderStore($user, Request $request)
    {
        $cart = session('POSCART');

        $order = $this->saleService->createSale($request, $user,  $cart);

        session()->put('POSCART', []);

        return $order;
    }

    public function sendOrderSuccessMail($user, $order_result, $payment_method, $payment_status)
    {

        $template = EmailTemplate::where('name', 'Order Successfully')->first();

        $payment_status = $payment_status == 1 ? 'Paid' : 'Unpaid';
        $subject = $template->subject;
        $message = $template->message;

        $message = str_replace('{{user_name}}', $user->name, $message);
        $message = str_replace('{{total_amount}}', currency($order_result->total_amount), $message);
        $message = str_replace('{{payment_method}}', $payment_method, $message);
        $message = str_replace('{{payment_status}}', $payment_status, $message);
        $message = str_replace('{{order_status}}', 'Processing', $message);
        $message = str_replace('{{order_date}}', $order_result->created_at->format('d F, Y'), $message);

        // dispatch(new OrderSuccessfulMailJob($user, $subject, $message));
    }
    public function posCartItemDetails(Request $request, $rowId)
    {
        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        $cart_contents = session()->get($cartName);

        if ($cart_contents != null && count($cart_contents) > 0) {
            $item = $cart_contents[$rowId];
            return view('pos::ajax_cart_item_details')->with([
                'cart_content' => $item,
            ])->render();
        } else {
            return view('pos::ajax_cart_item_details')->with([
                'cart_content' => null,
            ]);
        }
    }

    public function modalClearCart(Request $request)
    {
        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        session()->put($cartName, []);
        return response()->json(['status' => true]);
    }
    public function cart_price_update(Request $request)
    {
        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        // get the item
        $cart_contents = session()->get($cartName);

        if ($cart_contents != null && count($cart_contents) > 0) {
            $item = $cart_contents[$request->rowId];
            $item['price'] = $request->price;
            $item['sub_total'] = $request->price * $item['qty'];
            $cart_contents[$request->rowId] = $item;

            session()->put($cartName, $cart_contents);
        }
        $cart_contents = session()->get($cartName);

        return view('pos::ajax_cart')->with([
            'cart_contents' => $cart_contents,
        ]);
    }

    public function cartHold(Request $request)
    {
        if (!session()->get('POSCART') || count(session()->get('POSCART')) == 0) {
            return back()->with(['alert-type' => 'error', 'messege' => 'Cart is Empty']);
        }
        $this->validate($request, [
            'note' => 'required',
        ]);


        CartHold::create([
            'user_id' => $request->user_id,
            'contents' => json_encode(session()->get('POSCART')),
            'status' => 'hold',
            'note' => $request->note
        ]);

        // forget pos cart
        session()->forget('POSCART');

        return back()->with(['alert-type' => 'success', 'messege' => 'Cart Hold Successfully']);
    }

    public function cartHoldDelete($id)
    {
        $cartHold = CartHold::find($id);
        $cartHold->delete();
        return back()->with(['alert-type' => 'success', 'messege' => 'Cart Hold Successfully']);
    }

    public function cartHoldEdit($id)
    {
        $cartHold = CartHold::find($id);

        // store in session
        session()->put('POSCART', json_decode($cartHold->contents, true));

        // delete from cart hold
        $cartHold->delete();
        $cart_contents = session()->get('POSCART');

        return view('pos::ajax_cart')->with([
            'cart_contents' => $cart_contents,
        ]);
    }

    public function cartSourceUpdate(Request $request)
    {
        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        $cart_contents = session()->get($cartName);

        $cart_contents = $cart_contents ? $cart_contents : [];

        $cart_contents[$request->rowid]['source'] = $request->source;

        session()->put($cartName, $cart_contents);

        return response()->json(['status' => true, 'cart' => session()->get($cartName)]);
    }

    public function cartPriceUpdate(Request $request)
    {
        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        $cart_contents = session()->get($cartName);
        $cart_contents = $cart_contents ? $cart_contents : [];
        $cart_contents[$request->rowid]['purchase_price'] = $request->purchase_price;
        $cart_contents[$request->rowid]['selling_price'] = $request->selling_price;
        $cart_contents[$request->rowid]['price'] = $request->val;
        session()->put($cartName, $cart_contents);
        return response()->json(['status' => true, 'cart' => session()->get($cartName)]);
    }

    /**
     * Get running orders (active dine-in orders)
     */
    public function getRunningOrders()
    {
        try {
            $runningOrders = Sale::with(['table', 'details.menuItem', 'customer', 'waiter'])
                ->where('order_type', Sale::ORDER_TYPE_DINE_IN)
                ->whereNotNull('table_id')
                ->whereIn('status', ['pending', 'processing'])
                ->orderBy('created_at', 'desc')
                ->get();

            $html = view('pos::running-orders', compact('runningOrders'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $runningOrders->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching running orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading running orders'
            ], 500);
        }
    }

    /**
     * Get running orders count
     */
    public function getRunningOrdersCount()
    {
        try {
            $count = Sale::where('order_type', Sale::ORDER_TYPE_DINE_IN)
                ->whereNotNull('table_id')
                ->whereIn('status', ['pending', 'processing'])
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

    /**
     * Get order details for running order
     */
    public function getOrderDetails($id)
    {
        try {
            $order = Sale::with(['table', 'details.menuItem', 'details.service', 'customer', 'waiter'])
                ->findOrFail($id);

            $html = view('pos::order-details', compact('order'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }

    /**
     * Load order items into cart for adding more items
     */
    public function loadOrderToCart($id)
    {
        try {
            $order = Sale::with(['details.menuItem', 'details.service'])
                ->findOrFail($id);

            // Store the order ID in session for later reference
            session()->put('EDITING_ORDER_ID', $order->id);
            session()->put('EDITING_TABLE_ID', $order->table_id);
            session()->put('EDITING_TABLE_NAME', $order->table->name ?? 'N/A');

            // Clear and populate POSCART with existing order items for display
            // We use empty cart so user can add NEW items only
            session()->put('POSCART', []);

            return response()->json([
                'success' => true,
                'message' => 'Order loaded to cart',
                'order_id' => $order->id,
                'table_name' => $order->table->name ?? 'N/A'
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading order to cart: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading order'
            ], 500);
        }
    }

    /**
     * Update running order with additional items
     */
    public function updateRunningOrder(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $order = Sale::with('details')->findOrFail($id);

            // Check if adding from POSCART (new items only)
            if ($request->add_from_cart) {
                $cart = session()->get('POSCART', []);

                if (empty($cart)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No items in cart'
                    ], 400);
                }

                // Add new items from cart to the order
                $addedQuantity = 0;
                $addedPrice = 0;
                $addedCogs = 0;
                $maxPrepTime = $order->estimated_prep_minutes ?? 0;

                foreach ($cart as $item) {
                    $menuItem = null;
                    if ($item['type'] === 'menu_item') {
                        $menuItem = MenuItem::find($item['id']);
                        // Track max prep time
                        if ($menuItem && $menuItem->preparation_time) {
                            $maxPrepTime = max($maxPrepTime, $menuItem->preparation_time);
                        }
                    }

                    // Create new order detail
                    \Modules\Sales\app\Models\ProductSale::create([
                        'sale_id' => $order->id,
                        'menu_item_id' => $item['type'] === 'menu_item' ? $item['id'] : null,
                        'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                        'product_sku' => $menuItem?->sku ?? '',
                        'variant_id' => $item['variant_id'] ?? null,
                        'attributes' => isset($item['variant']) ? ($item['variant']['attribute'] ?? null) : null,
                        'price' => $item['price'],
                        'selling_price' => $item['selling_price'] ?? $item['price'],
                        'purchase_price' => $item['purchase_price'] ?? 0,
                        'quantity' => $item['qty'],
                        'base_quantity' => $item['qty'],
                        'sub_total' => $item['qty'] * $item['price'],
                        'source' => $item['source'] ?? 1,
                    ]);

                    $addedQuantity += $item['qty'];
                    $addedPrice += $item['qty'] * $item['price'];
                    $addedCogs += $item['qty'] * ($item['purchase_price'] ?? 0);
                }

                // Update order totals
                $newTotalPrice = $order->total_price + $addedPrice;
                $newGrandTotal = $newTotalPrice - ($order->order_discount ?? 0) + ($order->total_tax ?? 0);

                $updateData = [
                    'quantity' => $order->quantity + $addedQuantity,
                    'total_price' => $newTotalPrice,
                    'grand_total' => $newGrandTotal,
                    'due_amount' => $newGrandTotal - ($order->paid_amount ?? 0),
                    'total_cogs' => ($order->total_cogs ?? 0) + $addedCogs,
                    'gross_profit' => $newGrandTotal - (($order->total_cogs ?? 0) + $addedCogs),
                    'updated_by' => auth('admin')->id(),
                ];

                // Update estimated prep time if new items have longer prep time
                if ($maxPrepTime > ($order->estimated_prep_minutes ?? 0)) {
                    $updateData['estimated_prep_minutes'] = $maxPrepTime;
                }

                $order->update($updateData);

                // Clear the cart and editing session
                session()->forget('POSCART');
                session()->forget('EDITING_ORDER_ID');
                session()->forget('EDITING_TABLE_ID');
                session()->forget('EDITING_TABLE_NAME');

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => __('Items added to order successfully'),
                    'order' => $order->fresh(['details', 'table'])
                ]);
            }

            // Original logic for UPDATE_CART
            $cart = session()->get('UPDATE_CART', []);

            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items in cart'
                ], 400);
            }

            // Get existing detail IDs from cart (items that were already in the order)
            $existingDetailIds = collect($cart)
                ->pluck('original_detail_id')
                ->filter()
                ->toArray();

            // Delete items that were removed from the order
            $order->details()->whereNotIn('id', $existingDetailIds)->delete();

            // Update existing items and add new ones
            $totalQuantity = 0;
            $totalPrice = 0;
            $totalCogs = 0;

            foreach ($cart as $item) {
                if (isset($item['original_detail_id']) && $item['original_detail_id']) {
                    // Update existing item
                    $detail = $order->details()->find($item['original_detail_id']);
                    if ($detail) {
                        $detail->update([
                            'quantity' => $item['qty'],
                            'price' => $item['price'],
                            'sub_total' => $item['qty'] * $item['price'],
                        ]);
                    }
                } else {
                    // Add new item
                    $order->details()->create([
                        'menu_item_id' => $item['type'] === 'menu_item' ? $item['id'] : null,
                        'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                        'variant_id' => $item['variant_id'] ?? null,
                        'quantity' => $item['qty'],
                        'price' => $item['price'],
                        'sub_total' => $item['qty'] * $item['price'],
                        'purchase_price' => $item['purchase_price'] ?? 0,
                    ]);
                }

                $totalQuantity += $item['qty'];
                $totalPrice += $item['qty'] * $item['price'];
                $totalCogs += $item['qty'] * ($item['purchase_price'] ?? 0);
            }

            // Update order totals
            $discount = $request->discount ?? $order->order_discount ?? 0;
            $tax = $request->tax ?? $order->total_tax ?? 0;
            $grandTotal = $totalPrice - $discount + $tax;

            $order->update([
                'quantity' => $totalQuantity,
                'total_price' => $totalPrice,
                'order_discount' => $discount,
                'total_tax' => $tax,
                'grand_total' => $grandTotal,
                'total_cogs' => $totalCogs,
                'gross_profit' => $grandTotal - $totalCogs,
                'updated_by' => auth('admin')->id(),
            ]);

            // Clear the update cart
            session()->forget('UPDATE_CART');
            session()->forget('EDITING_ORDER_ID');
            session()->forget('EDITING_TABLE_ID');
            session()->forget('EDITING_TABLE_NAME');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order' => $order->fresh(['details', 'table'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating running order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete a running order (process payment)
     */
    public function completeRunningOrder(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $order = Sale::with(['table', 'details', 'customer'])->findOrFail($id);

            // Calculate payment totals
            $paymentTypes = $request->payment_type ?? ['cash'];
            $accountIds = $request->account_id ?? [null];
            $payingAmounts = $request->paying_amount ?? [$order->grand_total];

            $totalPaid = array_sum($payingAmounts);
            $dueAmount = max(0, $order->grand_total - $totalPaid);

            // Update order payment information
            $order->update([
                'status' => 'completed',
                'payment_status' => $dueAmount <= 0 ? 1 : 0, // 1 = paid, 0 = partial/unpaid
                'payment_method' => json_encode($paymentTypes),
                'paid_amount' => $totalPaid,
                'due_amount' => $dueAmount,
                'receive_amount' => $request->receive_amount ?? $totalPaid,
                'return_amount' => $request->return_amount ?? 0,
                'updated_by' => auth('admin')->id(),
            ]);

            // Create CustomerPayment records for each payment method
            foreach ($paymentTypes as $key => $paymentType) {
                $amount = $payingAmounts[$key] ?? 0;
                if ($amount <= 0) continue;

                // Find the appropriate account
                $account = null;
                if ($paymentType === 'cash') {
                    $account = Account::where('account_type', 'cash')->first();
                } else {
                    $accountId = $accountIds[$key] ?? null;
                    if ($accountId) {
                        $account = Account::find($accountId);
                    } else {
                        // Fallback: find first account of the type
                        $account = Account::where('account_type', $paymentType)->first();
                    }
                }

                if (!$account) {
                    Log::warning("No account found for payment type: $paymentType");
                    continue;
                }

                // Create CustomerPayment record
                \Modules\Customer\app\Models\CustomerPayment::create([
                    'sale_id' => $order->id,
                    'customer_id' => $order->customer_id != 'walk-in-customer' ? $order->customer_id : null,
                    'is_guest' => $order->customer_id == 'walk-in-customer' || !$order->customer_id ? 1 : 0,
                    'account_id' => $account->id,
                    'payment_type' => 'sale',
                    'amount' => $amount,
                    'payment_date' => now(),
                    'is_received' => 1,
                    'is_paid' => 0,
                    'created_by' => auth('admin')->id(),
                ]);
            }

            // Create CustomerDue record if there's remaining due amount
            if ($dueAmount > 0 && $order->customer_id && $order->customer_id != 'walk-in-customer') {
                \Modules\Customer\app\Models\CustomerDue::create([
                    'invoice' => $order->invoice,
                    'due_amount' => $dueAmount,
                    'due_date' => now()->addDays(7),
                    'status' => 1,
                    'customer_id' => $order->customer_id,
                ]);
            }

            // Release the seats from this order
            if ($order->table) {
                $order->table->releaseForSale($order);
            }

            DB::commit();

            // Generate POS receipt
            $sale = Sale::with(['table', 'details.menuItem', 'details.service', 'details.ingredient', 'customer', 'createdBy', 'waiter', 'payment.account'])->find($order->id);
            $setting = \Illuminate\Support\Facades\Cache::get('setting');

            $receiptHtml = view('pos::pos-receipt')->with([
                'sale' => $sale,
                'setting' => $setting,
            ])->render();

            return response()->json([
                'success' => true,
                'message' => __('Order completed successfully'),
                'receipt' => $receiptHtml,
                'invoice' => $sale->invoice,
                'invoiceRoute' => route('admin.sales.invoice', $order->id) . '?print=true'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a running order
     */
    public function cancelRunningOrder($id)
    {
        DB::beginTransaction();
        try {
            $order = Sale::with('table')->findOrFail($id);

            $order->update([
                'status' => 'cancelled',
                'updated_by' => auth('admin')->id(),
            ]);

            // Release the seats from this order
            if ($order->table) {
                $order->table->releaseForSale($order);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling order'
            ], 500);
        }
    }

    /**
     * Update item quantity in a running order
     */
    public function updateOrderItemQty($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $order = Sale::findOrFail($id);
            $detailId = $request->detail_id;
            $quantity = max(1, intval($request->quantity));

            $detail = \Modules\Sales\app\Models\ProductSale::where('sale_id', $id)
                ->where('id', $detailId)
                ->firstOrFail();

            $oldSubtotal = $detail->sub_total;
            $newSubtotal = $detail->price * $quantity;

            $detail->update([
                'quantity' => $quantity,
                'base_quantity' => $quantity,
                'sub_total' => $newSubtotal,
            ]);

            // Update order totals
            $totalDiff = $newSubtotal - $oldSubtotal;
            $order->update([
                'total_price' => $order->total_price + $totalDiff,
                'grand_total' => $order->grand_total + $totalDiff,
                'due_amount' => $order->due_amount + $totalDiff,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Quantity updated'),
                'new_subtotal' => currency($newSubtotal),
                'order_total' => currency($order->fresh()->grand_total),
                'order_subtotal' => currency($order->fresh()->total_price),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating item quantity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating quantity'
            ], 500);
        }
    }

    /**
     * Remove item from a running order
     */
    public function removeOrderItem($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $order = Sale::findOrFail($id);
            $detailId = $request->detail_id;

            $detail = \Modules\Sales\app\Models\ProductSale::where('sale_id', $id)
                ->where('id', $detailId)
                ->firstOrFail();

            $subtotalToRemove = $detail->sub_total;

            // Delete the item
            $detail->delete();

            // Update order totals
            $order->update([
                'total_price' => $order->total_price - $subtotalToRemove,
                'grand_total' => $order->grand_total - $subtotalToRemove,
                'due_amount' => max(0, $order->due_amount - $subtotalToRemove),
            ]);

            // Check if order has no items left
            $remainingItems = \Modules\Sales\app\Models\ProductSale::where('sale_id', $id)->count();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Item removed'),
                'order_total' => currency($order->fresh()->grand_total),
                'order_subtotal' => currency($order->fresh()->total_price),
                'items_remaining' => $remainingItems,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error removing item'
            ], 500);
        }
    }

    /**
     * Add item to a running order
     */
    public function addOrderItem($id, Request $request)
    {
        DB::beginTransaction();
        try {
            $order = Sale::findOrFail($id);

            $menuItemId = $request->menu_item_id;
            $quantity = max(1, intval($request->quantity ?? 1));
            $price = $request->price;
            $variantId = $request->variant_id;

            $menuItem = \Modules\Menu\app\Models\MenuItem::findOrFail($menuItemId);

            if (!$price) {
                $price = $menuItem->price;
                if ($variantId) {
                    $variant = \Modules\Menu\app\Models\MenuVariant::find($variantId);
                    if ($variant) {
                        $price = $variant->price;
                    }
                }
            }

            $subTotal = $price * $quantity;

            // Create new order detail
            $detail = \Modules\Sales\app\Models\ProductSale::create([
                'sale_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'product_sku' => $menuItem->sku,
                'variant_id' => $variantId,
                'price' => $price,
                'selling_price' => $price,
                'quantity' => $quantity,
                'base_quantity' => $quantity,
                'sub_total' => $subTotal,
                'source' => 1,
            ]);

            // Update order totals
            $order->update([
                'total_price' => $order->total_price + $subTotal,
                'grand_total' => $order->grand_total + $subTotal,
                'due_amount' => $order->due_amount + $subTotal,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('Item added'),
                'item_name' => $menuItem->name,
                'order_total' => currency($order->fresh()->grand_total),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error adding item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer loyalty info by phone
     */
    public function getCustomerLoyalty(Request $request)
    {
        try {
            $phone = $request->phone;
            $customerId = $request->customer_id;

            if (!$phone && $customerId && $customerId != 'walk-in-customer') {
                $user = User::find($customerId);
                $phone = $user?->phone;
            }

            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number required'
                ]);
            }

            $loyaltyInfo = $this->loyaltyService->getCustomerBalance($phone);

            if (!$loyaltyInfo) {
                // Customer not enrolled in loyalty program
                return response()->json([
                    'success' => true,
                    'enrolled' => false,
                    'customer' => null,
                    'message' => 'Customer not enrolled in loyalty program'
                ]);
            }

            // Get active loyalty program for redemption calculation
            $program = LoyaltyProgram::where('is_active', 1)->first();

            $redemptionRate = $program?->points_per_unit ?? 100;
            $earningRate = $program?->earning_rate ?? 1;

            return response()->json([
                'success' => true,
                'enrolled' => true,
                'customer' => [
                    'id' => $loyaltyInfo['customer_id'] ?? null,
                    'phone' => $loyaltyInfo['phone'],
                    'total_points' => $loyaltyInfo['total_points'],
                    'lifetime_earned' => $loyaltyInfo['lifetime_earned'],
                    'redeemed' => $loyaltyInfo['redeemed'],
                ],
                'redemption_rate' => $redemptionRate,
                'earning_rate' => $earningRate,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching loyalty info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching loyalty information'
            ], 500);
        }
    }

    /**
     * Calculate points to be earned for an order
     */
    public function calculatePointsToEarn(Request $request)
    {
        try {
            $orderTotal = $request->amount ?? $request->order_total ?? 0;

            $program = LoyaltyProgram::where('is_active', 1)->first();

            if (!$program) {
                return response()->json([
                    'success' => false,
                    'points' => 0,
                    'message' => 'No active loyalty program'
                ]);
            }

            $pointsToEarn = 0;

            if ($program->earning_type === 'per_transaction') {
                $pointsToEarn = $program->earning_rate;
            } else {
                // per_amount - points based on spend
                if ($orderTotal >= ($program->min_transaction_amount ?? 0)) {
                    $pointsToEarn = floor($orderTotal / ($program->earning_rate ?: 1));
                }
            }

            return response()->json([
                'success' => true,
                'points' => $pointsToEarn,
                'earning_type' => $program->earning_type,
                'earning_rate' => $program->earning_rate,
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating points: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'points' => 0,
                'message' => 'Error calculating points'
            ], 500);
        }
    }

    /**
     * Award points after sale completion
     */
    public function awardLoyaltyPoints(Request $request)
    {
        try {
            $phone = $request->phone;
            $saleId = $request->sale_id;
            $orderTotal = $request->order_total;

            if (!$phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number required'
                ]);
            }

            $saleContext = [
                'amount' => $orderTotal,
                'sale_id' => $saleId,
                'items' => [],
            ];

            $result = $this->loyaltyService->handleSaleCompletion($phone, 1, $saleContext);

            if ($result['success'] ?? false) {
                // Update the sale with points earned
                if ($saleId) {
                    Sale::where('id', $saleId)->update([
                        'points_earned' => $result['points_earned'] ?? 0
                    ]);
                }
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error awarding loyalty points: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error awarding points: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Redeem loyalty points
     */
    public function redeemLoyaltyPoints(Request $request)
    {
        try {
            $phone = $request->phone;
            $pointsToRedeem = $request->points;

            if (!$phone || !$pointsToRedeem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone and points required'
                ]);
            }

            $program = LoyaltyProgram::where('is_active', 1)->first();

            if (!$program) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active loyalty program'
                ]);
            }

            // Calculate discount value
            $discountValue = $pointsToRedeem / $program->points_per_unit;

            $result = $this->loyaltyService->handleRedemption($phone, 1, [
                'points' => $pointsToRedeem,
                'redemption_type' => 'discount',
                'value' => $discountValue,
            ]);

            if ($result['success'] ?? false) {
                $result['discount_value'] = $discountValue;
            }

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Error redeeming points: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error redeeming points: ' . $e->getMessage()
            ], 500);
        }
    }
}
