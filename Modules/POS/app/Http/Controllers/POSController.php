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
use Modules\POS\app\Models\PosSettings;
use Modules\Menu\app\Models\MenuCategory;
use Modules\Menu\app\Models\MenuItem;
use Modules\Menu\app\Models\Combo;
use Modules\Menu\app\Services\MenuItemService;
use Modules\Sales\app\Services\SaleService;
use Modules\Service\app\Services\ServicesService;
use Modules\TableManagement\app\Models\RestaurantTable;
use Modules\Sales\app\Models\Sale;
use Modules\Membership\app\Services\LoyaltyService;
use Modules\Membership\app\Models\LoyaltyProgram;
use Modules\POS\app\Services\PrintService;

class POSController extends Controller
{
    protected $menuItemService;
    protected $orderService;
    protected $loyaltyService;
    protected $printService;

    public function __construct(private UserGroupService $userGroup, MenuItemService $menuItemService, OrderService $orderService, private AreaService $areaService, private SaleService $saleService, private ServicesService $services, LoyaltyService $loyaltyService, PrintService $printService)
    {
        $this->middleware('auth:admin');
        $this->menuItemService = $menuItemService;
        $this->orderService = $orderService;
        $this->loyaltyService = $loyaltyService;
        $this->printService = $printService;
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

        // Load POS settings
        $posSettings = PosSettings::first();
        if (!$posSettings) {
            $posSettings = new PosSettings([
                'show_phone' => true,
                'show_address' => true,
                'show_email' => true,
                'show_customer' => true,
                'show_warehouse' => false,
                'show_discount' => true,
                'show_barcode' => false,
                'show_note' => true,
                'is_printable' => true,
                'merge_cart_items' => true,
            ]);
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
            'posSettings' => $posSettings,
        ]);
    }

    public function load_products(Request $request)
    {
        Paginator::useBootstrap();

        $searchType = $request->search_type ?? 'all'; // 'regular', 'combo', or 'all'

        $menuItemView = '';
        $featuredMenuItemView = '';
        $serviceView = '';
        $favoriteServiceView = '';
        $comboView = '';

        // Load regular menu items if search type is 'regular' or 'all'
        if ($searchType === 'regular' || $searchType === 'all') {
            $menuItems = MenuItem::with('activeAddons')->where('status', 1)->where('is_available', 1)
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
            $featuredItems->appends(request()->query());

            // Paginate non-featured menu items (use clone of original query)
            $nonFeaturedItems = (clone $menuItems)->paginate(15);
            $nonFeaturedItems->appends(request()->query());

            $services = $this->services->all()->where('status', 1);

            if ($request->service_name) {
                $services = $services->where(function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->service_name . '%');
                });
            }

            if ($request->service_category_id) {
                $services = $services->where('category_id', $request->service_category_id);
            }

            $favoriteServices = clone $services;
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

            $menuItemView = view('pos::ajax_menu_items')->with([
                'menuItems' => $nonFeaturedItems,
            ])->render();

            $featuredMenuItemView = view('pos::ajax_menu_items')->with([
                'menuItems' => $featuredItems,
            ])->render();
        }

        // Load combos if search type is 'combo' or 'all'
        if ($searchType === 'combo' || $searchType === 'all') {
            $combos = Combo::currentlyAvailable()->ordered();

            if ($request->name) {
                $combos = $combos->where('name', 'LIKE', '%' . $request->name . '%');
            }

            $combos = $combos->paginate(15);
            $combos->appends(request()->query());

            $comboView = view('pos::ajax_combos')->with([
                'combos' => $combos,
            ])->render();
        }

        return response()->json([
            'productView' => $menuItemView,
            'serviceView' => $serviceView,
            'favProductView' => $featuredMenuItemView,
            'favoriteServiceView' => $favoriteServiceView,
            'comboView' => $comboView,
            'searchType' => $searchType
        ]);
    }

    public function featuredMenuItems($menuItems)
    {
        $menuItems = $menuItems->where('is_featured', 1)->paginate(15);

        $menuItems->appends(request()->query());
        return $menuItems;
    }

    public function load_products_list(Request $request)
    {
        $searchType = $request->search_type ?? 'regular';

        // If searching combos
        if ($searchType === 'combo') {
            $combos = Combo::currentlyAvailable()->ordered();

            if ($request->name) {
                $combos = $combos->where('name', 'LIKE', '%' . $request->name . '%');
            }

            $combos = $combos->get();

            $view = view('pos::ajax_combos')->with([
                'combos' => $combos,
                'isAutocomplete' => true
            ])->render();

            return response()->json(['view' => $view, 'total' => $combos->count(), 'type' => 'combo']);
        }

        // Regular menu items search
        $menuItems = MenuItem::with('activeAddons')->active()->available()
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

        return response()->json(['view' => $view, 'total' => $menuItems->count(), 'menuItem' => $menuItems->first(), 'type' => 'regular']);
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

    /**
     * Check if cart has items from different restaurant (legacy method)
     * Since this is a single restaurant system, always returns no conflict
     */
    public function check_cart_restaurant($id)
    {
        // Single restaurant system - no conflict possible
        return response()->json(['status' => false]);
    }

    /**
     * Get available addons for a menu item (for cart addon modal)
     */
    public function getItemAddons($menuItemId, $rowId)
    {
        $menuItem = MenuItem::with('activeAddons')->find($menuItemId);
        if (!$menuItem) {
            return response()->json(['html' => '<p class="text-danger">Item not found</p>'], 404);
        }

        // Get current cart item to check selected addons
        $cartName = 'POSCART';
        $cart = session()->get($cartName, []);
        $currentAddons = [];
        $currentAddonQtys = [];
        if (isset($cart[$rowId]) && !empty($cart[$rowId]['addons'])) {
            foreach ($cart[$rowId]['addons'] as $addon) {
                $currentAddons[] = $addon['id'];
                $currentAddonQtys[$addon['id']] = $addon['qty'] ?? 1;
            }
        }

        $html = '<div class="addon-list">';
        if ($menuItem->activeAddons->count() > 0) {
            foreach ($menuItem->activeAddons as $addon) {
                $checked = in_array($addon->id, $currentAddons) ? 'checked' : '';
                $qty = $currentAddonQtys[$addon->id] ?? 1;
                $html .= '<div class="form-check mb-2 d-flex align-items-center justify-content-between">';
                $html .= '<div>';
                $html .= '<input class="form-check-input cart-addon-checkbox" type="checkbox" value="' . $addon->id . '" id="cart_addon_' . $addon->id . '" ' . $checked . ' data-price="' . $addon->price . '">';
                $html .= '<label class="form-check-label" for="cart_addon_' . $addon->id . '">';
                $html .= $addon->name . ' <span class="text-success">(+' . currency($addon->price) . ')</span>';
                $html .= '</label>';
                $html .= '</div>';
                $html .= '<input type="number" min="1" value="' . $qty . '" class="form-control form-control-sm addon-qty-input" style="width: 60px;" data-addon-id="' . $addon->id . '">';
                $html .= '</div>';
            }
        } else {
            $html .= '<p class="text-muted text-center">' . __('No add-ons available for this item') . '</p>';
        }
        $html .= '</div>';

        return response()->json(['html' => $html]);
    }

    /**
     * Update cart item with selected addons
     */
    public function updateCartAddons(Request $request)
    {
        $cartName = 'POSCART';
        $rowId = $request->rowid;
        $addonIds = $request->addons ?? [];
        $addonQtys = $request->addon_qtys ?? []; // Addon quantities keyed by addon ID

        $cart = session()->get($cartName, []);

        if (!isset($cart[$rowId])) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem = $cart[$rowId];
        $menuItemId = $cartItem['id'];

        // Get the menu item's base price (without addons)
        $basePrice = $cartItem['base_price'] ?? $cartItem['price'];

        // Calculate addons
        $addons = [];
        $addonsPrice = 0;

        if (!empty($addonIds)) {
            $addonModels = \Modules\Menu\app\Models\MenuAddon::whereIn('id', $addonIds)->where('status', 1)->get();
            foreach ($addonModels as $addon) {
                $qty = isset($addonQtys[$addon->id]) ? max(1, intval($addonQtys[$addon->id])) : 1;
                $addons[] = [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => $addon->price,
                    'qty' => $qty,
                ];
                $addonsPrice += $addon->price * $qty;
            }
        }

        // Update cart item
        $cart[$rowId]['addons'] = $addons;
        $cart[$rowId]['addons_price'] = $addonsPrice;
        $cart[$rowId]['base_price'] = $basePrice;
        $cart[$rowId]['price'] = $basePrice + $addonsPrice;
        $cart[$rowId]['sub_total'] = $cart[$rowId]['price'] * $cart[$rowId]['qty'];

        session()->put($cartName, $cart);

        return view('pos::ajax_cart')->with([
            'cart_contents' => $cart,
        ]);
    }

    /**
     * Update addon quantity in cart item
     */
    public function updateAddonQty(Request $request)
    {
        $cartName = 'POSCART';
        $rowId = $request->rowid;
        $addonId = $request->addon_id;
        $qty = max(1, intval($request->qty));

        $cart = session()->get($cartName, []);

        if (!isset($cart[$rowId])) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem = $cart[$rowId];
        $basePrice = $cartItem['base_price'] ?? $cartItem['price'];

        // Update the addon quantity
        $addonsPrice = 0;
        if (!empty($cartItem['addons'])) {
            foreach ($cartItem['addons'] as $index => $addon) {
                if ($addon['id'] == $addonId) {
                    $cart[$rowId]['addons'][$index]['qty'] = $qty;
                }
                $addonQty = ($addon['id'] == $addonId) ? $qty : ($addon['qty'] ?? 1);
                $addonsPrice += $addon['price'] * $addonQty;
            }
        }

        // Recalculate price
        $cart[$rowId]['addons_price'] = $addonsPrice;
        $cart[$rowId]['price'] = $basePrice + $addonsPrice;
        $cart[$rowId]['sub_total'] = $cart[$rowId]['price'] * $cart[$rowId]['qty'];

        session()->put($cartName, $cart);

        return view('pos::ajax_cart')->with([
            'cart_contents' => $cart,
        ]);
    }

    /**
     * Remove addon from cart item
     */
    public function removeAddon(Request $request)
    {
        $cartName = 'POSCART';
        $rowId = $request->rowid;
        $addonId = $request->addon_id;

        $cart = session()->get($cartName, []);

        if (!isset($cart[$rowId])) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem = $cart[$rowId];
        $basePrice = $cartItem['base_price'] ?? $cartItem['price'];

        // Remove the addon
        $newAddons = [];
        $addonsPrice = 0;
        if (!empty($cartItem['addons'])) {
            foreach ($cartItem['addons'] as $addon) {
                if ($addon['id'] != $addonId) {
                    $newAddons[] = $addon;
                    $addonsPrice += $addon['price'] * ($addon['qty'] ?? 1);
                }
            }
        }

        // Update cart item
        $cart[$rowId]['addons'] = $newAddons;
        $cart[$rowId]['addons_price'] = $addonsPrice;
        $cart[$rowId]['price'] = $basePrice + $addonsPrice;
        $cart[$rowId]['sub_total'] = $cart[$rowId]['price'] * $cart[$rowId]['qty'];

        session()->put($cartName, $cart);

        return view('pos::ajax_cart')->with([
            'cart_contents' => $cart,
        ]);
    }

    public function add_to_cart(Request $request)
    {

        $cartName = 'POSCART';
        if ($request->edit) {
            $cartName = 'UPDATE_CART';
        }
        $type = $request->serviceType ?? 'menu_item';

        // Handle combo type separately
        if ($type == 'combo') {
            return $this->addComboToCartHandler($request, $cartName);
        }

        // Get POS settings for merge behavior
        $posSettings = PosSettings::first();
        $mergeCartItems = $posSettings ? $posSettings->merge_cart_items : true;

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

        // Handle addons
        $addons = [];
        $addonsPrice = 0;
        $addonIds = $request->addons ?? [];
        if ($menuItem && !empty($addonIds)) {
            $addonModels = \Modules\Menu\app\Models\MenuAddon::whereIn('id', $addonIds)->where('status', 1)->get();
            foreach ($addonModels as $addon) {
                $addons[] = [
                    'id' => $addon->id,
                    'name' => $addon->name,
                    'price' => $addon->price,
                ];
                $addonsPrice += $addon->price;
            }
        }

        $cart_contents = session()->get($cartName);
        $cart_contents = $cart_contents ? $cart_contents : [];

        // Check if item already exists in cart
        $existing_rowid = null;
        $sku = $type != 'service' ? ($request->variant_sku ? $request->variant_sku : ($menuItem ? $menuItem->sku : '')) : '';
        $variant_id = $variant ? $variant->id : null;

        // Only merge if no addons selected (items with addons are unique)
        if ($mergeCartItems && count($cart_contents) > 0 && empty($addonIds)) {
            foreach ($cart_contents as $rowid => $cart_content) {
                // Match by SKU and variant_id for menu items, or by id for services
                if ($type != 'service') {
                    // Only merge if both have no addons
                    $existingHasAddons = !empty($cart_content['addons'] ?? []);
                    if ($sku && $cart_content['sku'] == $sku && ($cart_content['variant_id'] ?? null) == $variant_id && !$existingHasAddons) {
                        $existing_rowid = $rowid;
                        break;
                    }
                } else {
                    if ($service && $cart_content['id'] == $service->id && $cart_content['type'] == 'service') {
                        $existing_rowid = $rowid;
                        break;
                    }
                }
            }
        }

        // If item exists and merge is enabled, update quantity
        if ($existing_rowid !== null) {
            $addQty = $request->qty ? $request->qty : 1;
            $cart_contents[$existing_rowid]['qty'] += $addQty;
            $cart_contents[$existing_rowid]['sub_total'] = (float)$cart_contents[$existing_rowid]['price'] * $cart_contents[$existing_rowid]['qty'];
            session()->put($cartName, $cart_contents);
        } else {
            // Add as new item
            $data = array();
            $data["rowid"] = uniqid();
            $data['id'] = $type == 'service' ? $service->id : $menuItem->id;
            $data['name'] = $type == 'service' ? $service->name : $menuItem->name;
            $data['type'] = $type == 'service' ? 'service' : 'menu_item';
            $data['image'] = $type == 'service' ? $service->singleImage : $menuItem->image_url;
            $data['qty'] = $request->qty ? $request->qty : 1;

            // Calculate price - use variant price if applicable, otherwise final_price (discount_price or base_price)
            $basePrice = $type == 'service' ? $service->price : ($menuItem->final_price);
            if ($variant) {
                $basePrice = $menuItem->final_price + ($variant->price_adjustment ?? 0);
            }

            // Add addons price to item price
            $price = $basePrice + $addonsPrice;

            $data['price'] = $price;
            $data['base_price'] = $basePrice;
            $data['sub_total'] = (float)$data['price'] * $data['qty'];
            $data['sku'] = $sku;
            $data['unit'] = '-'; // Menu items don't have unit like ingredients
            $data['source'] = 1;
            $data['purchase_price'] = $menuItem ? ($menuItem->cost_price ?? 0) : 0;
            $data['selling_price'] = $price;
            $data['variant_id'] = $variant_id;
            $data['addons'] = $addons;
            $data['addons_price'] = $addonsPrice;

            if ($request->type == null && $variant) {
                $data['variant']['attribute'] =  $attributes;
                $data['variant']['options'] =  $options;
            }

            session()->put($cartName, [...$cart_contents, $data["rowid"] => $data]);
        }

        $cart_contents = session($cartName);

        return view('pos::ajax_cart')->with([
            'cart_contents' => $cart_contents,
        ]);
    }

    /**
     * Handle adding combo to cart
     */
    private function addComboToCartHandler(Request $request, string $cartName)
    {
        $combo = Combo::with('items.menuItem')->find($request->combo_id);

        if (!$combo || !$combo->is_currently_available) {
            return response()->json(['message' => __('Combo not available')], 403);
        }

        $cart_contents = session()->get($cartName, []);

        // Get POS settings for merge behavior
        $posSettings = PosSettings::first();
        $mergeCartItems = $posSettings ? $posSettings->merge_cart_items : true;

        // Check if combo already exists in cart
        $existing_rowid = null;
        if ($mergeCartItems && count($cart_contents) > 0) {
            foreach ($cart_contents as $rowid => $cart_content) {
                if (($cart_content['type'] ?? '') === 'combo' && $cart_content['id'] == $combo->id) {
                    $existing_rowid = $rowid;
                    break;
                }
            }
        }

        // If combo exists and merge is enabled, update quantity
        if ($existing_rowid !== null) {
            $addQty = $request->qty ? $request->qty : 1;
            $cart_contents[$existing_rowid]['qty'] += $addQty;
            $cart_contents[$existing_rowid]['sub_total'] = (float)$cart_contents[$existing_rowid]['price'] * $cart_contents[$existing_rowid]['qty'];
            session()->put($cartName, $cart_contents);
        } else {
            // Add as new item
            $data = [
                'rowid' => uniqid(),
                'id' => $combo->id,
                'name' => $combo->name,
                'type' => 'combo',
                'image' => $combo->image_url,
                'qty' => $request->qty ? $request->qty : 1,
                'price' => (float) $combo->combo_price,
                'base_price' => (float) $combo->combo_price,
                'sub_total' => (float) $combo->combo_price * ($request->qty ?: 1),
                'sku' => '',
                'unit' => '-',
                'source' => 1,
                'purchase_price' => 0,
                'selling_price' => (float) $combo->combo_price,
                'variant_id' => null,
                'addons' => [],
                'addons_price' => 0,
            ];

            $cart_contents[$data['rowid']] = $data;
            session()->put($cartName, $cart_contents);
        }

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

    /**
     * Search customer by phone number
     * Searches in both customers table and previous orders
     */
    public function getCustomerByPhone(Request $request)
    {
        $phone = $request->get('phone');

        if (empty($phone) || strlen($phone) < 5) {
            return response()->json([
                'success' => false,
                'message' => 'Phone number too short'
            ]);
        }

        // Normalize phone: remove non-digits for flexible matching
        $phoneDigits = preg_replace('/[^0-9]/', '', $phone);

        // First, search in customers (users) table
        $customer = User::whereRaw("REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '+', '') LIKE ?", ['%' . $phoneDigits . '%'])
            ->where(function($query) {
                $query->where('status', 'active')
                      ->orWhere('status', 1);
            })
            ->first();

        if ($customer) {
            return response()->json([
                'success' => true,
                'source' => 'customer',
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'address' => $customer->address ?? '',
                ]
            ]);
        }

        // If not found in customers, search in previous orders (sales table)
        $sale = Sale::whereNotNull('delivery_phone')
            ->where('delivery_phone', '!=', '')
            ->whereRaw("REPLACE(REPLACE(REPLACE(delivery_phone, '-', ''), ' ', ''), '+', '') LIKE ?", ['%' . $phoneDigits . '%'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($sale) {
            // Try to get customer name from notes JSON
            $notes = json_decode($sale->notes ?? '{}', true);
            $customerName = $notes['customer_name'] ?? '';

            return response()->json([
                'success' => true,
                'source' => 'order',
                'customer' => [
                    'id' => null,
                    'name' => $customerName,
                    'phone' => $sale->delivery_phone,
                    'email' => $notes['customer_email'] ?? '',
                    'address' => $sale->delivery_address ?? '',
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ]);
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

            // Check if this is a deferred payment (running order)
            // Deferred payment applies to: Dine-in and Take Away when payment_status is 0
            $isDeferredPayment = $request->defer_payment || $sale->payment_status == 0;

            // Trigger printing to both printers (kitchen & cash counter)
            try {
                $this->printService->printNewOrder($sale);
            } catch (\Exception $printException) {
                // Log print error but don't fail the order
                Log::warning('Print service error: ' . $printException->getMessage());
            }

            if ($isDeferredPayment) {
                $orderTypeLabel = match($sale->order_type) {
                    Sale::ORDER_TYPE_DINE_IN => __('Dine-in'),
                    Sale::ORDER_TYPE_TAKE_AWAY => __('Take Away'),
                    default => __('Order')
                };

                return response()->json([
                    'success' => true,
                    'order' => $order_result,
                    'order_id' => $order_result->id,
                    'invoice' => null,
                    'invoiceRoute' => null,
                    'is_deferred' => true,
                    'table_name' => $sale->table?->name,
                    'order_type' => $sale->order_type,
                    'message' => $orderTypeLabel . ' ' . __('order started successfully'),
                    'alert-type' => 'success',
                ], 200);
            }

            // Award loyalty points server-side for paid orders
            $pointsEarned = 0;
            try {
                $customerPhone = $sale->customer->phone ?? null;
                if ($customerPhone && ($sale->points_earned ?? 0) <= 0) {
                    $saleContext = [
                        'amount' => $sale->grand_total,
                        'sale_id' => $sale->id,
                        'items' => [],
                    ];
                    $loyaltyResult = $this->loyaltyService->handleSaleCompletion($customerPhone, 1, $saleContext);
                    if ($loyaltyResult['success'] ?? false) {
                        $pointsEarned = $loyaltyResult['points_earned'] ?? 0;
                        $sale->update(['points_earned' => $pointsEarned]);
                    }
                }
            } catch (\Exception $loyaltyException) {
                Log::warning('Loyalty points error (POS): ' . $loyaltyException->getMessage());
            }

            // Generate POS receipt for paid orders
            $setting = \Illuminate\Support\Facades\Cache::get('setting');
            $sale->load(['details.menuItem', 'details.service', 'details.ingredient', 'customer', 'createdBy', 'waiter', 'table', 'payment.account']);

            $receiptHtml = view('pos::pos-receipt')->with([
                'sale' => $sale,
                'setting' => $setting,
            ])->render();

            $invoiceRoute = route('admin.sales.invoice', $order_result->id) . '?print=true';

            return response()->json([
                'success' => true,
                'order' => $order_result,
                'order_id' => $order_result->id,
                'invoice' => $receiptHtml,
                'invoiceRoute' => $invoiceRoute,
                'is_deferred' => false,
                'points_earned' => $pointsEarned,
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
     * Get running orders (active orders - Dine-in, Take Away)
     */
    public function getRunningOrders(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = 9;
            $search = $request->get('search', '');

            // Note: status column is integer - 0 = processing/pending, 1 = completed
            // Include all order types: Dine-in, Take Away
            $query = Sale::with(['table', 'details.menuItem', 'customer', 'waiter'])
                ->whereIn('order_type', [Sale::ORDER_TYPE_DINE_IN, Sale::ORDER_TYPE_TAKE_AWAY])
                ->where('status', 0); // 0 = processing/pending

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice', 'like', "%{$search}%")
                      ->orWhereHas('table', function ($tq) use ($search) {
                          $tq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%");
                      })
                      ->orWhereHas('waiter', function ($wq) use ($search) {
                          $wq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $runningOrders = $query->orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            $html = view('pos::running-orders', compact('runningOrders'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $runningOrders->total(),
                'currentPage' => $runningOrders->currentPage(),
                'lastPage' => $runningOrders->lastPage(),
                'hasMorePages' => $runningOrders->hasMorePages()
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
            // Note: status column is integer - 0 = processing/pending, 1 = completed
            // Include all order types: Dine-in, Take Away
            $count = Sale::whereIn('order_type', [Sale::ORDER_TYPE_DINE_IN, Sale::ORDER_TYPE_TAKE_AWAY])
                ->where('status', 0) // 0 = processing/pending
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
     * Get available tables for AJAX refresh
     */
    public function getAvailableTables()
    {
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

            $html = view('pos::partials.tables-list', compact('availableTables'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching available tables: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading tables'
            ], 500);
        }
    }

    /**
     * Get order details for running order
     */
    public function getOrderDetails($id)
    {
        try {
            Log::info('getOrderDetails called', ['id' => $id]);

            $order = Sale::with(['table', 'details.menuItem', 'details.service', 'details.ingredient', 'customer', 'waiter'])
                ->findOrFail($id);

            Log::info('Order found', [
                'id' => $order->id,
                'invoice' => $order->invoice,
                'total_price' => $order->total_price,
                'grand_total' => $order->grand_total,
                'details_count' => $order->details->count()
            ]);

            // Try to render view, but catch errors separately
            $html = '';
            try {
                $html = view('pos::order-details', compact('order'))->render();
            } catch (\Exception $viewError) {
                Log::warning('View render error: ' . $viewError->getMessage());
                $html = '<div class="alert alert-warning">Could not render order details view</div>';
            }

            return response()->json([
                'success' => true,
                'html' => $html,
                'order' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Order not found: ' . $e->getMessage()
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
        Log::info('completeRunningOrder called', ['id' => $id, 'request' => $request->all()]);

        try {
            $order = Sale::with(['table', 'details', 'customer'])->findOrFail($id);
            Log::info('Order found', ['order_id' => $order->id, 'current_status' => $order->status]);

            // Handle discount if provided
            $discount = $request->discount_amount ?? $request->discount ?? $order->order_discount ?? 0;

            // Handle tax if provided
            $taxRate = $request->tax_rate ?? 0;
            $taxAmount = $request->tax_amount ?? 0;

            // If tax amount not provided but rate is, calculate it
            if ($taxRate > 0 && $taxAmount == 0) {
                $taxableAmount = $order->total_price - $discount;
                $taxAmount = ($taxableAmount * $taxRate) / 100;
            }

            $grandTotal = $request->total_amount ?? ($order->total_price - $discount + $taxAmount);

            // Calculate payment totals - ensure all are arrays
            $paymentTypes = $request->payment_type;
            if (!is_array($paymentTypes)) {
                $paymentTypes = $paymentTypes ? [$paymentTypes] : ['cash'];
            }

            $accountIds = $request->account_id;
            if (!is_array($accountIds)) {
                $accountIds = $accountIds ? [$accountIds] : [null];
            }

            $payingAmounts = $request->paying_amount;
            if (!is_array($payingAmounts)) {
                $payingAmounts = $payingAmounts ? [$payingAmounts] : [$grandTotal];
            }

            $totalPaid = array_sum($payingAmounts);
            $dueAmount = max(0, $grandTotal - $totalPaid);

            // Update order status and payment information (critical - must succeed)
            // Note: status column is integer - 0 = processing, 1 = completed
            $order->update([
                'status' => 1, // 1 = completed
                'payment_status' => $dueAmount <= 0 ? 1 : 0, // 1 = paid, 0 = partial/unpaid
                'payment_method' => json_encode($paymentTypes),
                'order_discount' => $discount,
                'total_tax' => $taxAmount,
                'tax_rate' => $taxRate,
                'grand_total' => $grandTotal,
                'paid_amount' => $totalPaid,
                'due_amount' => $dueAmount,
                'receive_amount' => $request->receive_amount ?? $totalPaid,
                'return_amount' => $request->return_amount ?? 0,
                'gross_profit' => $grandTotal - ($order->total_cogs ?? 0),
                'updated_by' => auth('admin')->id(),
            ]);

            // Refresh order from database to confirm update
            $order->refresh();
            Log::info('Order updated', ['order_id' => $order->id, 'new_status' => $order->status]);

            // Release the table if this is a dine-in order
            if ($order->table) {
                Log::info('Releasing table', ['table_id' => $order->table->id, 'table_name' => $order->table->name]);
                $order->table->release();
                Log::info('Table released', ['table_id' => $order->table->id, 'new_status' => $order->table->status, 'occupied_seats' => $order->table->occupied_seats]);
            } else {
                Log::info('No table to release (Take Away order)', ['order_id' => $order->id, 'order_type' => $order->order_type]);
            }

            // Create CustomerPayment records (optional - log errors but don't fail)
            try {
                foreach ($paymentTypes as $key => $paymentType) {
                    $amount = $payingAmounts[$key] ?? 0;
                    if ($amount <= 0) continue;

                    // Find the appropriate account (auto-create cash if missing)
                    $account = null;
                    if ($paymentType === 'cash') {
                        $account = Account::firstOrCreate(
                            ['account_type' => 'cash'],
                            ['bank_account_name' => 'Cash Register']
                        );
                    } else {
                        $accountId = $accountIds[$key] ?? null;
                        if ($accountId) {
                            $account = Account::find($accountId);
                        }
                        if (!$account) {
                            $account = Account::where('account_type', $paymentType)->first();
                        }
                    }

                    if (!$account) {
                        Log::warning("No account found for payment type: $paymentType");
                        continue;
                    }

                    $isGuestOrder = !$order->customer_id || $order->customer_id == 0;
                    \Modules\Customer\app\Models\CustomerPayment::create([
                        'sale_id' => $order->id,
                        'customer_id' => $isGuestOrder ? null : $order->customer_id,
                        'is_guest' => $isGuestOrder ? 1 : 0,
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
            } catch (\Exception $paymentException) {
                Log::warning('Payment record creation failed: ' . $paymentException->getMessage());
            }

            // Generate POS receipt (optional - don't fail if this errors)
            $receiptHtml = '';
            try {
                $sale = Sale::with(['table', 'details.menuItem', 'details.service', 'details.ingredient', 'customer', 'createdBy', 'waiter', 'payment.account'])->find($order->id);
                $setting = \Illuminate\Support\Facades\Cache::get('setting');

                $receiptHtml = view('pos::pos-receipt')->with([
                    'sale' => $sale,
                    'setting' => $setting,
                ])->render();
            } catch (\Exception $receiptException) {
                Log::warning('Receipt generation failed: ' . $receiptException->getMessage());
            }

            // Award loyalty points server-side for completed running/waiter orders
            $pointsEarned = 0;
            try {
                $customerPhone = $order->customer->phone ?? null;
                if ($customerPhone && ($order->points_earned ?? 0) <= 0) {
                    $saleContext = [
                        'amount' => $order->grand_total,
                        'sale_id' => $order->id,
                        'items' => [],
                    ];
                    $loyaltyResult = $this->loyaltyService->handleSaleCompletion($customerPhone, 1, $saleContext);
                    if ($loyaltyResult['success'] ?? false) {
                        $pointsEarned = $loyaltyResult['points_earned'] ?? 0;
                        $order->update(['points_earned' => $pointsEarned]);
                    }
                }
            } catch (\Exception $loyaltyException) {
                Log::warning('Loyalty points error (running order): ' . $loyaltyException->getMessage());
            }

            Log::info('Order completion successful', ['order_id' => $order->id]);

            return response()->json([
                'success' => true,
                'message' => __('Order completed successfully'),
                'receipt' => $receiptHtml,
                'invoice' => $order->invoice,
                'points_earned' => $pointsEarned,
                'invoiceRoute' => route('admin.sales.invoice', $order->id) . '?print=true'
            ]);
        } catch (\Exception $e) {
            Log::error('Error completing order: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error completing order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete an already-paid running order (no payment processing needed)
     */
    public function completePaidOrder($id)
    {
        try {
            $order = Sale::with('table')->findOrFail($id);

            // Verify the order is actually paid
            if ($order->payment_status != 1 && $order->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => __('Order is not yet paid. Please use Complete & Pay instead.')
                ], 422);
            }

            // Mark as completed
            $order->update([
                'status' => 1,
                'updated_by' => auth('admin')->id(),
            ]);

            // Release the table if dine-in
            if ($order->table) {
                $order->table->release();
            }

            // Generate receipt
            $receiptHtml = '';
            try {
                $sale = Sale::with(['table', 'details.menuItem', 'details.service', 'details.ingredient', 'customer', 'createdBy', 'waiter', 'payment.account'])->find($order->id);
                $setting = \Illuminate\Support\Facades\Cache::get('setting');

                $receiptHtml = view('pos::pos-receipt')->with([
                    'sale' => $sale,
                    'setting' => $setting,
                ])->render();
            } catch (\Exception $e) {
                Log::warning('Receipt generation failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => __('Order completed successfully'),
                'receipt' => $receiptHtml,
                'invoice' => $order->invoice,
                'invoiceRoute' => route('admin.sales.invoice', $order->id) . '?print=true'
            ]);
        } catch (\Exception $e) {
            Log::error('Error completing paid order: ' . $e->getMessage());
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

            // Note: status column is integer - 0 = processing, 1 = completed, 2 = cancelled
            $order->update([
                'status' => 2, // 2 = cancelled
                'updated_by' => auth('admin')->id(),
            ]);

            // Fully release the table (free all seats for next order)
            if ($order->table) {
                $order->table->release(); // Release all seats
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
     * Print POS receipt for an order (thermal printer format)
     */
    public function printOrderReceipt($id)
    {
        try {
            $sale = Sale::with([
                'table',
                'details.menuItem',
                'details.service',
                'details.ingredient',
                'customer',
                'createdBy',
                'waiter',
                'payment.account'
            ])->findOrFail($id);

            $setting = \Illuminate\Support\Facades\Cache::get('setting');

            return view('pos::print.pos-receipt', [
                'sale' => $sale,
                'setting' => $setting,
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating receipt: ' . $e->getMessage());
            return response()->view('errors.404', [], 404);
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
