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

class POSController extends Controller
{
    protected $menuItemService;
    protected $orderService;

    public function __construct(private UserGroupService $userGroup, MenuItemService $menuItemService, OrderService $orderService, private AreaService $areaService, private SaleService $saleService, private ServicesService $services)
    {
        $this->middleware('auth:admin');
        $this->menuItemService = $menuItemService;
        $this->orderService = $orderService;
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

        $menuItems = MenuItem::where('status', 1)->where('is_available', 1)->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->orderBy('display_order', 'asc');

        if ($request->category_id) {
            $menuItems = $menuItems->where(function ($query) use ($request) {
                $query->where('category_id', $request->category_id)->where('status', 1);
            });
        }

        if ($request->name) {
            $menuItems = $menuItems->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('sku', 'LIKE', '%' . $request->name . '%');
            });
        }

        $menuItems = $menuItems->paginate(5);

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
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // Tables module might not be installed yet
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
        ]);
    }

    public function load_products(Request $request)
    {
        Paginator::useBootstrap();

        $menuItems = MenuItem::where('status', 1)->where('is_available', 1)->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->orderBy('display_order', 'asc');

        if ($request->category_id) {
            $menuItems = $menuItems->where(function ($query) use ($request) {
                $query->where('category_id', $request->category_id)->where('status', 1);
            });
        }

        if ($request->name) {
            $menuItems = $menuItems->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('sku', 'LIKE', '%' . $request->name . '%');
            });
        }


        // Paginate featured menu items (clone to avoid modifying original query)
        $featuredItems = (clone $menuItems)->where('is_featured', 1)->paginate(15);
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

        $menuItems = MenuItem::where('status', 1)->where('is_available', 1)->whereHas('category', function ($query) {
            $query->where('status', 1);
        })->orderBy('display_order', 'asc');

        if ($request->name) {
            $menuItems = $menuItems->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('barcode', 'LIKE', '%' . $request->name . '%')
                    ->orWhere('sku', 'LIKE', '%' . $request->name . '%');
            });
        }
        if ($request->favorite == 1 || $request->featured == 1) {
            $menuItems = $menuItems->where('is_featured', 1);
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
        if (session('POSCART') != null && count(session('POSCART')) == 0) {
            $notification = trans('Your cart is empty!');
            $notification = array('messege' => $notification, 'alert-type' => 'error');
            return redirect()->back()->with($notification);
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

            $invoiceBlade = view('sales::invoice-content')->with([
                'sale' => $sale,
            ])->render();

            $invoiceRoute = route('admin.sales.invoice', $order_result->id) . '?print=true';
            DB::commit();
            return response()->json([
                'order' => $order_result,
                'invoice' => $invoiceBlade,
                'invoiceRoute' => $invoiceRoute,
                'message' => 'Sale created successfully',
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
}
