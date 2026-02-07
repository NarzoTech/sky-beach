<?php

namespace Modules\POS\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Employee\app\Models\Employee;
use Modules\Menu\app\Models\Combo;
use Modules\Menu\app\Models\MenuCategory;
use Modules\Menu\app\Models\MenuItem;
use Modules\POS\app\Models\PosSettings;
use Modules\POS\app\Services\PrintService;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Services\SaleService;
use Modules\TableManagement\app\Models\RestaurantTable;

class WaiterDashboardController extends Controller
{
    protected $printService;

    public function __construct(PrintService $printService)
    {
        $this->printService = $printService;
    }

    /**
     * Display waiter dashboard
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('waiter.dashboard');

        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        // Get tables
        $tables = RestaurantTable::orderBy('sort_order')->get();

        // Get waiter's active orders (pending, confirmed, preparing, ready statuses)
        $activeOrders = Sale::where('waiter_id', $employee?->id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->with(['table', 'details.menuItem', 'details.service'])
            ->latest()
            ->get();

        // Count statistics
        $stats = [
            'active_orders' => $activeOrders->count(),
            'available_tables' => $tables->where('status', 'available')->count(),
            'occupied_tables' => $tables->where('status', 'occupied')->count(),
        ];

        return view('pos::waiter.dashboard', compact('tables', 'activeOrders', 'stats', 'employee'));
    }

    /**
     * Show table selection for new order
     */
    public function selectTable()
    {
        checkAdminHasPermissionAndThrowException('waiter.table.view');

        $tables = RestaurantTable::orderBy('sort_order')->get();

        return view('pos::waiter.select-table', compact('tables'));
    }

    /**
     * Show order creation page for a specific table
     */
    public function createOrder($tableId)
    {
        checkAdminHasPermissionAndThrowException('waiter.order.create');

        $table = RestaurantTable::findOrFail($tableId);

        // Check if table is available or partially occupied
        if ($table->status === 'maintenance' || $table->status === 'reserved') {
            return back()->with('error', 'This table is not available for orders.');
        }

        $categories = MenuCategory::where('status', 1)
            ->with(['menuItems' => function ($query) {
                $query->where('status', 1)->with(['addons' => function ($q) {
                    $q->where('status', 1);
                }]);
            }])
            ->orderBy('display_order')
            ->get();

        // Get active combos
        $combos = Combo::currentlyAvailable()
            ->with(['items.menuItem', 'items.variant'])
            ->orderBy('name')
            ->get();

        $posSettings = PosSettings::first();
        $setting = cache('setting');

        return view('pos::waiter.create-order', compact('table', 'categories', 'combos', 'posSettings', 'setting'));
    }

    /**
     * Store new order from waiter
     */
    public function storeOrder(Request $request)
    {
        checkAdminHasPermissionAndThrowException('waiter.order.create');

        $validated = $request->validate([
            'table_id' => 'required|exists:restaurant_tables,id',
            'guest_count' => 'required|integer|min:1',
            'items' => 'nullable|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.addons' => 'nullable|array',
            'items.*.note' => 'nullable|string|max:500',
            'combos' => 'nullable|array',
            'combos.*.combo_id' => 'required|exists:combos,id',
            'combos.*.quantity' => 'required|integer|min:1',
            'combos.*.note' => 'nullable|string|max:500',
            'special_instructions' => 'nullable|string|max:1000',
        ]);

        // Must have at least one item or combo
        if (empty($validated['items']) && empty($validated['combos'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please add at least one item or combo to the order.',
            ], 400);
        }

        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Waiter profile not found.',
            ], 400);
        }

        $table = RestaurantTable::findOrFail($validated['table_id']);

        // Build cart items from request
        $cartItems = [];
        $subtotal = 0;

        // Process regular menu items
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                $itemTotal = $menuItem->price * $item['quantity'];

                $addons = [];
                if (!empty($item['addons'])) {
                    foreach ($item['addons'] as $addonData) {
                        $addons[] = [
                            'id' => $addonData['id'],
                            'name' => $addonData['name'],
                            'price' => $addonData['price'],
                            'qty' => $addonData['qty'] ?? 1,
                        ];
                        $itemTotal += ($addonData['price'] * ($addonData['qty'] ?? 1));
                    }
                }

                $cartItems[] = [
                    'id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'type' => 'menu_item',
                    'qty' => $item['quantity'],
                    'price' => $menuItem->price,
                    'base_price' => $menuItem->price,
                    'sub_total' => $itemTotal,
                    'addons' => $addons,
                    'note' => $item['note'] ?? null,
                ];

                $subtotal += $itemTotal;
            }
        }

        // Process combo items
        if (!empty($validated['combos'])) {
            foreach ($validated['combos'] as $comboItem) {
                $combo = Combo::with(['items.menuItem', 'items.variant'])->findOrFail($comboItem['combo_id']);
                $comboTotal = $combo->combo_price * $comboItem['quantity'];

                // Build combo items list for display/printing
                $comboContents = [];
                foreach ($combo->items as $item) {
                    $itemName = $item->menuItem->name;
                    if ($item->variant) {
                        $itemName .= ' (' . $item->variant->name . ')';
                    }
                    $comboContents[] = [
                        'menu_item_id' => $item->menu_item_id,
                        'name' => $itemName,
                        'quantity' => $item->quantity,
                        'variant_id' => $item->variant_id,
                    ];
                }

                $cartItems[] = [
                    'id' => $combo->id,
                    'name' => $combo->name,
                    'type' => 'combo',
                    'qty' => $comboItem['quantity'],
                    'price' => $combo->combo_price,
                    'base_price' => $combo->combo_price,
                    'original_price' => $combo->original_price,
                    'sub_total' => $comboTotal,
                    'combo_items' => $comboContents,
                    'addons' => [],
                    'note' => $comboItem['note'] ?? null,
                ];

                $subtotal += $comboTotal;
            }
        }

        // Get tax rate from POS settings or default to 15%
        $posSettings = PosSettings::first();
        $taxRate = optional($posSettings)->pos_tax_rate ?: 15;
        $taxAmount = $subtotal * ($taxRate / 100);
        $grandTotal = $subtotal + $taxAmount;

        // Create sale using SaleService
        $saleData = [
            'order_type' => 'dine_in',
            'table_id' => $table->id,
            'waiter_id' => $employee->id,
            'guest_count' => $validated['guest_count'],
            'special_instructions' => $validated['special_instructions'] ?? null,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax' => $taxAmount,
            'total' => $grandTotal,
            'status' => 'pending', // Processing
            'payment_status' => 0, // Unpaid
        ];

        try {
            $saleService = app(SaleService::class);
            $sale = $saleService->createWaiterOrder($saleData, $cartItems, null, []);

            // Mark table as occupied
            $table->occupy($sale, $validated['guest_count']);

            // Trigger printing to both printers
            $this->printService->printNewOrder($sale);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $sale->id,
                'redirect' => route('admin.waiter.dashboard'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View waiter's orders
     */
    public function myOrders()
    {
        checkAdminHasPermissionAndThrowException('waiter.order.view');

        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        $orders = Sale::where('waiter_id', $employee?->id)
            ->with(['table', 'details.menuItem', 'details.service', 'customer'])
            ->latest()
            ->paginate(20);

        return view('pos::waiter.my-orders', compact('orders'));
    }

    /**
     * View single order details
     */
    public function orderDetails($id)
    {
        checkAdminHasPermissionAndThrowException('waiter.order.view');

        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        $order = Sale::where('id', $id)
            ->where('waiter_id', $employee?->id)
            ->with(['table', 'details.menuItem', 'details.service', 'customer', 'payments'])
            ->firstOrFail();

        return view('pos::waiter.order-details', compact('order'));
    }

    /**
     * Show add items to order page
     */
    public function showAddToOrder($id)
    {
        checkAdminHasPermissionAndThrowException('waiter.order.update');

        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        $order = Sale::where('id', $id)
            ->where('waiter_id', $employee?->id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->with(['table', 'details.menuItem', 'details.service'])
            ->firstOrFail();

        $categories = MenuCategory::where('status', 1)
            ->with(['menuItems' => function ($query) {
                $query->where('status', 1)->with(['addons' => function ($q) {
                    $q->where('status', 1);
                }]);
            }])
            ->orderBy('display_order')
            ->get();

        // Get active combos
        $combos = Combo::currentlyAvailable()
            ->with(['items.menuItem', 'items.variant'])
            ->orderBy('name')
            ->get();

        $posSettings = PosSettings::first();
        $setting = cache('setting');

        return view('pos::waiter.add-to-order', compact('order', 'categories', 'combos', 'posSettings', 'setting'));
    }

    /**
     * Add items to existing order
     */
    public function addToOrder(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('waiter.order.update');

        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        $order = Sale::where('id', $id)
            ->where('waiter_id', $employee?->id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->firstOrFail();

        $validated = $request->validate([
            'items' => 'nullable|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.addons' => 'nullable|array',
            'items.*.note' => 'nullable|string|max:500',
            'combos' => 'nullable|array',
            'combos.*.combo_id' => 'required|exists:combos,id',
            'combos.*.quantity' => 'required|integer|min:1',
            'combos.*.note' => 'nullable|string|max:500',
        ]);

        // Must have at least one item or combo
        if (empty($validated['items']) && empty($validated['combos'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please add at least one item or combo.',
            ], 400);
        }

        $newItems = [];
        $addedTotal = 0;

        // Process regular menu items
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                $itemTotal = $menuItem->price * $item['quantity'];

                $addons = [];
                if (!empty($item['addons'])) {
                    foreach ($item['addons'] as $addonData) {
                        $addons[] = [
                            'id' => $addonData['id'],
                            'name' => $addonData['name'],
                            'price' => $addonData['price'],
                            'qty' => $addonData['qty'] ?? 1,
                        ];
                        $itemTotal += ($addonData['price'] * ($addonData['qty'] ?? 1) * $item['quantity']);
                    }
                }

                // Add to order details
                $order->details()->create([
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'price' => $menuItem->price,
                    'sub_total' => $itemTotal,
                    'addons' => !empty($addons) ? json_encode($addons) : null,
                    'note' => $item['note'] ?? null,
                ]);

                $newItems[] = [
                    'name' => $menuItem->name,
                    'qty' => $item['quantity'],
                    'addons' => $addons,
                    'note' => $item['note'] ?? null,
                ];

                $addedTotal += $itemTotal;
            }
        }

        // Process combo items
        if (!empty($validated['combos'])) {
            foreach ($validated['combos'] as $comboItem) {
                $combo = Combo::with(['items.menuItem', 'items.variant'])->findOrFail($comboItem['combo_id']);
                $comboTotal = $combo->combo_price * $comboItem['quantity'];

                // Build combo items list for display/printing
                $comboContents = [];
                foreach ($combo->items as $item) {
                    $itemName = $item->menuItem->name;
                    if ($item->variant) {
                        $itemName .= ' (' . $item->variant->name . ')';
                    }
                    $comboContents[] = [
                        'menu_item_id' => $item->menu_item_id,
                        'name' => $itemName,
                        'quantity' => $item->quantity,
                        'variant_id' => $item->variant_id,
                    ];
                }

                // Add to order details
                $order->details()->create([
                    'combo_id' => $combo->id,
                    'combo_name' => $combo->name,
                    'quantity' => $comboItem['quantity'],
                    'price' => $combo->combo_price,
                    'sub_total' => $comboTotal,
                    'note' => $comboItem['note'] ?? null,
                ]);

                $newItems[] = [
                    'name' => $combo->name . ' (Combo)',
                    'qty' => $comboItem['quantity'],
                    'addons' => [],
                    'combo_items' => $comboContents,
                    'note' => $comboItem['note'] ?? null,
                ];

                $addedTotal += $comboTotal;
            }
        }

        // Update order totals with tax recalculation
        $posSettings = PosSettings::first();
        $taxRate = $order->tax_rate ?: (optional($posSettings)->pos_tax_rate ?: 15);

        $newSubtotal = ($order->total_price ?? 0) + $addedTotal;
        $newTaxAmount = $newSubtotal * ($taxRate / 100);
        $newGrandTotal = $newSubtotal + $newTaxAmount;

        $order->total_price = $newSubtotal;
        $order->tax_rate = $taxRate;
        $order->total_tax = $newTaxAmount;
        $order->grand_total = $newGrandTotal;
        $order->due_amount = $newGrandTotal;
        $order->save();

        // Print only new items to kitchen
        $this->printService->printOrderUpdate($order, $newItems);

        return response()->json([
            'success' => true,
            'message' => 'Items added to order successfully!',
        ]);
    }

    /**
     * Cancel order (if allowed)
     */
    public function cancelOrder($id)
    {
        checkAdminHasPermissionAndThrowException('waiter.order.cancel');

        $admin = Auth::guard('admin')->user();
        $employee = $admin->employee;

        $order = Sale::where('id', $id)
            ->where('waiter_id', $employee?->id)
            ->whereIn('status', ['pending', 'confirmed', 'preparing'])
            ->firstOrFail();

        // Release table
        if ($order->table) {
            $order->table->release($order->guest_count);
        }

        $order->update(['status' => 'cancelled']);

        // Print void ticket
        $this->printService->printVoid($order);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.',
        ]);
    }

    /**
     * Get menu items by category (AJAX)
     */
    public function getMenuItems(Request $request)
    {
        $categoryId = $request->get('category_id');

        $query = MenuItem::where('status', 1)
            ->with(['addons' => function ($q) {
                $q->where('status', 1);
            }]);

        if ($categoryId) {
            $query->where('menu_category_id', $categoryId);
        }

        $items = $query->orderBy('name')->get();

        return response()->json($items);
    }

    /**
     * Get table status (AJAX)
     */
    public function getTableStatus($id)
    {
        $table = RestaurantTable::with(['activeOrders' => function ($q) {
            $q->with('waiter');
        }])->findOrFail($id);

        return response()->json([
            'id' => $table->id,
            'name' => $table->name,
            'status' => $table->status,
            'capacity' => $table->capacity,
            'occupied_seats' => $table->occupied_seats,
            'active_orders' => $table->activeOrders,
        ]);
    }

    /**
     * Print kitchen ticket for an order
     */
    public function printKitchenTicket($id)
    {
        $sale = Sale::with(['table', 'waiter', 'details.menuItem', 'details.service', 'customer'])
            ->findOrFail($id);

        return view('pos::print.kitchen-ticket', [
            'sale' => $sale,
            'printer' => null,
            'setting' => cache('setting'),
        ]);
    }

    /**
     * Print cash slip for an order
     */
    public function printCashSlip($id)
    {
        $sale = Sale::with(['table', 'waiter', 'details.menuItem', 'details.service', 'customer'])
            ->findOrFail($id);

        return view('pos::print.cash-slip', [
            'sale' => $sale,
            'printer' => null,
            'setting' => cache('setting'),
        ]);
    }
}
