<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\app\Http\Controllers\POSController;
use Modules\POS\app\Http\Controllers\PosSettingsController;
use Modules\POS\app\Http\Controllers\WaiterController;
use Modules\POS\app\Http\Controllers\WaiterDashboardController;
use Modules\POS\app\Http\Controllers\PrinterController;
use Modules\POS\app\Http\Controllers\PrintStationController;
use Modules\POS\app\Http\Controllers\KitchenDisplayController;
use Modules\POS\app\Http\Controllers\TableTransferController;
use Modules\POS\app\Http\Controllers\SplitBillController;
use Modules\POS\app\Http\Controllers\VoidItemController;
use Modules\POS\app\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {
    Route::prefix('pos')->group(function () {
        Route::get('/', [POSController::class, 'index'])->name('pos');
        Route::get('/load-products', [POSController::class, 'load_products'])->name('load-products');
        Route::get('load-products-list', [POSController::class, 'load_products_list'])->name('load-products-list');
        Route::get('/load-product-modal/{id}', [POSController::class, 'load_product_modal'])->name('load-product-modal');
        Route::get('/pos/load-customer-address/{id}', [POSController::class, 'load_customer_address'])->name('load-customer-address');
        Route::get('/add-to-cart', [POSController::class, 'add_to_cart'])->name('add-to-cart');
        Route::get('/cart-quantity-update', [POSController::class, 'cart_quantity_update'])->name('cart-quantity-update');
        Route::get('/cart-quantity-update-quick', [POSController::class, 'cart_quantity_update_quick'])->name('cart-quantity-update-quick');
        Route::get('cart-price-update', [POSController::class, 'cart_price_update'])->name('cart-price-update');
        Route::get('/remove-cart-item/{id}', [POSController::class, 'remove_cart_item'])->name('remove-cart-item');
        Route::get('/cart-clear', [POSController::class, 'cart_clear'])->name('cart-clear');
        Route::get('/pos-cart-item-details/{id}', [POSController::class, 'posCartItemDetails'])->name('pos-cart-item-details');
        Route::post('/create-new-customer', [POSController::class, 'create_new_customer'])->name('create-new-customer');
        Route::post('/create-new-address', [POSController::class, 'create_new_address'])->name('create-new-address');
        Route::post('/place-order', [POSController::class, 'place_order'])->name('place-order');

        // New Payment Modal Routes (aliases for unified payment flow)
        Route::post('/checkout', [POSController::class, 'place_order'])->name('pos.checkout');
        Route::post('/start-dine-in-order', [POSController::class, 'place_order'])->name('pos.start-dine-in-order');

        Route::get('/check-cart-restaurant/{id}', [POSController::class, 'check_cart_restaurant'])->name('check-cart-restaurant');
        Route::get('/modal-cart-clear', [POSController::class, 'modalClearCart'])->name('modal-cart-clear');

        // Cart Addon Routes
        Route::get('/get-item-addons/{menuItemId}/{rowId}', [POSController::class, 'getItemAddons'])->name('pos.get-item-addons');
        Route::post('/update-cart-addons', [POSController::class, 'updateCartAddons'])->name('pos.update-cart-addons');
        Route::post('/update-addon-qty', [POSController::class, 'updateAddonQty'])->name('pos.update-addon-qty');
        Route::post('/remove-addon', [POSController::class, 'removeAddon'])->name('pos.remove-addon');

        // Running Orders Routes
        Route::get('/running-orders', [POSController::class, 'getRunningOrders'])->name('pos.running-orders');
        Route::get('/running-orders/count', [POSController::class, 'getRunningOrdersCount'])->name('pos.running-orders.count');
        Route::get('/running-orders/{id}/details', [POSController::class, 'getOrderDetails'])->name('pos.running-orders.details');
        Route::post('/running-orders/{id}/load-to-cart', [POSController::class, 'loadOrderToCart'])->name('pos.running-orders.load-cart');
        Route::post('/running-orders/{id}/update', [POSController::class, 'updateRunningOrder'])->name('pos.running-orders.update');
        Route::post('/running-orders/{id}/complete', [POSController::class, 'completeRunningOrder'])->name('pos.running-orders.complete');
        Route::post('/running-orders/{id}/cancel', [POSController::class, 'cancelRunningOrder'])->name('pos.running-orders.cancel');
        Route::get('/running-orders/{id}/receipt', [POSController::class, 'printOrderReceipt'])->name('pos.running-orders.receipt');
        Route::post('/running-orders/{id}/update-item-qty', [POSController::class, 'updateOrderItemQty'])->name('pos.running-orders.update-item-qty');
        Route::post('/running-orders/{id}/remove-item', [POSController::class, 'removeOrderItem'])->name('pos.running-orders.remove-item');
        Route::post('/running-orders/{id}/add-item', [POSController::class, 'addOrderItem'])->name('pos.running-orders.add-item');

        // Tables Routes
        Route::get('/available-tables', [POSController::class, 'getAvailableTables'])->name('pos.available-tables');

        // Customer Phone Lookup
        Route::get('/customer-by-phone', [POSController::class, 'getCustomerByPhone'])->name('pos.customer-by-phone');

        // Loyalty Points Routes
        Route::get('/loyalty/customer', [POSController::class, 'getCustomerLoyalty'])->name('pos.loyalty.customer');
        Route::get('/loyalty/calculate-points', [POSController::class, 'calculatePointsToEarn'])->name('pos.loyalty.calculate');
        Route::post('/loyalty/award-points', [POSController::class, 'awardLoyaltyPoints'])->name('pos.loyalty.award');
        Route::post('/loyalty/redeem-points', [POSController::class, 'redeemLoyaltyPoints'])->name('pos.loyalty.redeem');
    });
    Route::get('cart/source/update', [POSController::class, 'cartSourceUpdate'])->name('cart.source.update');
    Route::get('cart/price/update', [POSController::class, 'cartPriceUpdate'])->name('cart.price.update');
    Route::post('/cart-hold', [POSController::class, 'cartHold'])->name('cart.hold');
    Route::get('/cart-hold/delete/{id}', [POSController::class, 'cartHoldDelete'])->name('cart.hold.delete');
    Route::get('/cart-hold/edit/{id}', [POSController::class, 'cartHoldEdit'])->name('cart.hold.edit');

    Route::get('pos/settings', [PosSettingsController::class, 'index'])->name('pos.settings');
    Route::post('pos/settings', [PosSettingsController::class, 'store'])->name('pos.settings.store');

    // Waiter Management Routes (Admin)
    Route::prefix('pos/waiters')->name('pos.waiters.')->group(function () {
        Route::get('/', [WaiterController::class, 'index'])->name('index');
        Route::get('/create', [WaiterController::class, 'create'])->name('create');
        Route::post('/', [WaiterController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [WaiterController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WaiterController::class, 'update'])->name('update');
        Route::delete('/{id}', [WaiterController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/status', [WaiterController::class, 'status'])->name('status');
    });

    // Printer Management Routes
    Route::prefix('pos/printers')->name('pos.printers.')->group(function () {
        Route::get('/', [PrinterController::class, 'index'])->name('index');
        Route::get('/create', [PrinterController::class, 'create'])->name('create');
        Route::post('/', [PrinterController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PrinterController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PrinterController::class, 'update'])->name('update');
        Route::delete('/{id}', [PrinterController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [PrinterController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{id}/test', [PrinterController::class, 'test'])->name('test');
        Route::get('/ajax/list', [PrinterController::class, 'getPrinters'])->name('ajax.list');
    });

    // Waiter Dashboard Routes
    Route::prefix('waiter')->name('waiter.')->group(function () {
        Route::get('/', [WaiterDashboardController::class, 'index'])->name('dashboard');
        Route::get('/select-table', [WaiterDashboardController::class, 'selectTable'])->name('select-table');
        Route::get('/create-order/{tableId}', [WaiterDashboardController::class, 'createOrder'])->name('create-order');
        Route::post('/store-order', [WaiterDashboardController::class, 'storeOrder'])->name('store-order');
        Route::get('/my-orders', [WaiterDashboardController::class, 'myOrders'])->name('my-orders');
        Route::get('/order/{id}', [WaiterDashboardController::class, 'orderDetails'])->name('order-details');
        Route::get('/add-to-order/{id}', [WaiterDashboardController::class, 'showAddToOrder'])->name('add-to-order');
        Route::post('/add-to-order/{id}', [WaiterDashboardController::class, 'addToOrder'])->name('add-to-order.store');
        Route::post('/cancel-order/{id}', [WaiterDashboardController::class, 'cancelOrder'])->name('cancel-order');
        Route::post('/order/{id}/remove-item', [WaiterDashboardController::class, 'removeOrderItem'])->name('order.remove-item');
        Route::get('/menu-items', [WaiterDashboardController::class, 'getMenuItems'])->name('menu-items');
        Route::get('/table-status/{id}', [WaiterDashboardController::class, 'getTableStatus'])->name('table-status');
        // Print Routes
        Route::get('/print/kitchen/{id}', [WaiterDashboardController::class, 'printKitchenTicket'])->name('print.kitchen');
        Route::get('/print/cash/{id}', [WaiterDashboardController::class, 'printCashSlip'])->name('print.cash');
        // Change Table Routes
        Route::get('/change-table/{id}', [TableTransferController::class, 'getTransferData'])->name('change-table.data');
        Route::post('/change-table/{id}', [TableTransferController::class, 'transfer'])->name('change-table');
    });

    // Print Job Routes
    Route::prefix('pos/print')->name('pos.print.')->group(function () {
        Route::get('/pending-jobs', [PrinterController::class, 'getPendingJobs'])->name('pending-jobs');
        Route::get('/job/{id}/content', [PrinterController::class, 'getJobContent'])->name('job-content');
        Route::post('/job/{id}/mark-printed', [PrinterController::class, 'markAsPrinted'])->name('mark-printed');
        Route::post('/job/{id}/mark-failed', [PrinterController::class, 'markAsFailed'])->name('mark-failed');
        Route::post('/job/{id}/retry', [PrinterController::class, 'retryJob'])->name('retry');
    });

    // Print Station Routes (Browser-based Auto Print)
    Route::prefix('pos/print-station')->name('pos.print-station.')->group(function () {
        Route::get('/', [PrintStationController::class, 'index'])->name('index');
        Route::get('/pending-jobs', [PrintStationController::class, 'getPendingJobs'])->name('pending-jobs');
        Route::get('/job/{id}/content', [PrintStationController::class, 'getJobContent'])->name('job-content');
        Route::post('/job/{id}/printed', [PrintStationController::class, 'markPrinted'])->name('job-printed');
        Route::post('/job/{id}/failed', [PrintStationController::class, 'markFailed'])->name('job-failed');
        Route::post('/job/{id}/retry', [PrintStationController::class, 'retryJob'])->name('job-retry');
        Route::get('/stats', [PrintStationController::class, 'getStats'])->name('stats');
        Route::get('/failed-jobs', [PrintStationController::class, 'getFailedJobs'])->name('failed-jobs');
        Route::post('/clear-old', [PrintStationController::class, 'clearOldJobs'])->name('clear-old');
    });

    // Kitchen Display Routes
    Route::prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/', [KitchenDisplayController::class, 'index'])->name('index');
        Route::get('/orders', [KitchenDisplayController::class, 'getOrders'])->name('orders');
        Route::get('/history', [KitchenDisplayController::class, 'history'])->name('history');
        Route::post('/item/{id}/status', [KitchenDisplayController::class, 'updateItemStatus'])->name('item.status');
        Route::post('/item/{id}/start', [KitchenDisplayController::class, 'startPreparing'])->name('item.start');
        Route::post('/item/{id}/ready', [KitchenDisplayController::class, 'markReady'])->name('item.ready');
        Route::post('/item/{id}/served', [KitchenDisplayController::class, 'markServed'])->name('item.served');
        Route::post('/order/{id}/status', [KitchenDisplayController::class, 'updateOrderStatus'])->name('order.status');
        Route::post('/order/{id}/bump', [KitchenDisplayController::class, 'bumpOrder'])->name('order.bump');
        Route::post('/order/{id}/recall', [KitchenDisplayController::class, 'recallOrder'])->name('order.recall');
    });

    // Table Transfer Routes
    Route::prefix('pos/table-transfer')->name('pos.table-transfer.')->group(function () {
        Route::get('/{orderId}', [TableTransferController::class, 'getTransferData'])->name('data');
        Route::post('/{orderId}', [TableTransferController::class, 'transfer'])->name('transfer');
        Route::post('/merge', [TableTransferController::class, 'merge'])->name('merge');
    });

    // Split Bill Routes
    Route::prefix('pos/split-bill')->name('pos.split-bill.')->group(function () {
        Route::get('/{orderId}', [SplitBillController::class, 'show'])->name('show');
        Route::get('/{orderId}/data', [SplitBillController::class, 'getOrderData'])->name('data');
        Route::post('/{orderId}/create', [SplitBillController::class, 'createSplits'])->name('create');
        Route::post('/{orderId}/split-equally', [SplitBillController::class, 'splitEqually'])->name('split-equally');
        Route::post('/{orderId}/remove', [SplitBillController::class, 'removeSplits'])->name('remove');
        Route::post('/pay/{splitId}', [SplitBillController::class, 'processPayment'])->name('pay');
        Route::get('/print/{splitId}', [SplitBillController::class, 'printSplitReceipt'])->name('print');
    });

    // Void Item Routes
    Route::prefix('pos/void')->name('pos.void.')->group(function () {
        Route::post('/item/{itemId}', [VoidItemController::class, 'voidItem'])->name('item');
        Route::post('/items', [VoidItemController::class, 'voidMultiple'])->name('items');
        Route::get('/history/{orderId}', [VoidItemController::class, 'getVoidHistory'])->name('history');
        Route::post('/restore/{itemId}', [VoidItemController::class, 'restoreItem'])->name('restore');
    });

    // Notification Routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread');
        Route::get('/count', [NotificationController::class, 'unreadCount'])->name('count');
        Route::post('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::post('/clear-old', [NotificationController::class, 'clearOld'])->name('clear-old');
    });
});
