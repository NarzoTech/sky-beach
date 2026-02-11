<?php

namespace Modules\Report\app\Http\Controllers;

use App\Exports\CustomerReportExport;
use App\Exports\DetailsSaleReportExport;
use App\Exports\ExpenseReportExport;
use App\Exports\MenuItemSalesExport;
use App\Exports\OrderTypeExport;
use App\Exports\PurchaseReportExport;
use App\Exports\SalaryReportExport;
use App\Exports\SuppliersPaymentReportExport;
use App\Exports\SuppliersReportExport;
use App\Exports\TablePerformanceExport;
use App\Exports\WaiterPerformanceExport;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Employee\app\Models\Employee;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Employee\app\Services\EmployeeService;
use Modules\Expense\app\Models\Expense;
use Modules\Ingredient\app\Services\BrandService;
use Modules\Ingredient\app\Services\IngredientCategoryService;
use Modules\Ingredient\app\Services\IngredientService;
use Modules\Menu\app\Models\MenuCategory;
use Modules\Menu\app\Models\MenuItem;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseReturn;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;
use Modules\Supplier\app\Services\SupplierService;
use Modules\TableManagement\app\Models\RestaurantTable;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Supplier\app\Models\SupplierPayment;

class ReportController extends Controller
{

    public function __construct(private BrandService $brandService, private IngredientCategoryService $categoryService, private IngredientService $productService, private SupplierService $supplierService, private EmployeeService $employeeService, private AccountsService $accountsService)
    {
        $this->middleware('auth:admin');
    }

    /**
     * Menu Item Sales Report
     */
    public function menuItemSales()
    {
        $query = ProductSale::whereNotNull('menu_item_id')
            ->whereHas('sale', fn($q) => $q->where('status', '!=', 'cancelled'))
            ->selectRaw('menu_item_id, SUM(quantity) as total_qty, SUM(sub_total) as total_revenue, SUM(cogs_amount) as total_cogs, SUM(profit_amount) as total_profit')
            ->groupBy('menu_item_id');

        // Date filter via sale relationship
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $query->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                $q->where('status', '!=', 'cancelled')->whereBetween('order_date', [$fromDate, $toDate]);
            });
        }

        // Category filter
        if (request('category')) {
            $query->whereHas('menuItem', function ($q) {
                $q->where('category_id', request('category'));
            });
        }

        // Keyword search
        if (request('keyword')) {
            $query->whereHas('menuItem', function ($q) {
                $q->where('name', 'like', '%' . request('keyword') . '%');
            });
        }

        $allItems = $query->with(['menuItem.category'])->get();

        // Calculate totals
        $data = [
            'totalQty' => $allItems->sum('total_qty'),
            'totalRevenue' => $allItems->sum('total_revenue'),
            'totalCogs' => $allItems->sum('total_cogs'),
            'totalProfit' => $allItems->sum('total_profit'),
        ];

        // Excel export
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'menu-item-sales-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new MenuItemSalesExport($allItems, $data), $fileName);
            }
        }

        // PDF export
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.menu-item-sales', [
                    'items' => $allItems,
                    'data' => $data
                ]);
            }
        }

        // Pagination
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }

        if ($parpage === null) {
            $items = $allItems;
        } else {
            $items = ProductSale::whereNotNull('menu_item_id')
                ->whereHas('sale', fn($q) => $q->where('status', '!=', 'cancelled'))
                ->selectRaw('menu_item_id, SUM(quantity) as total_qty, SUM(sub_total) as total_revenue, SUM(cogs_amount) as total_cogs, SUM(profit_amount) as total_profit')
                ->groupBy('menu_item_id');

            if (request('from_date') || request('to_date')) {
                $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
                $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
                $items->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                    $q->where('status', '!=', 'cancelled')->whereBetween('order_date', [$fromDate, $toDate]);
                });
            }
            if (request('category')) {
                $items->whereHas('menuItem', function ($q) {
                    $q->where('category_id', request('category'));
                });
            }
            if (request('keyword')) {
                $items->whereHas('menuItem', function ($q) {
                    $q->where('name', 'like', '%' . request('keyword') . '%');
                });
            }

            $items = $items->with(['menuItem.category'])->paginate($parpage);
            $items->appends(request()->query());
        }

        $categories = MenuCategory::where('status', 1)->orderBy('name')->get();

        return view('report::menu-item-sales', compact('items', 'data', 'categories'));
    }

    /**
     * Waiter Performance Report
     */
    public function waiterPerformance()
    {
        $query = Sale::whereNotNull('waiter_id')
            ->where('status', '!=', 'cancelled')
            ->selectRaw('waiter_id, COUNT(*) as total_orders, SUM(grand_total) as total_revenue, SUM(total_cogs) as total_cogs, SUM(gross_profit) as total_profit')
            ->groupBy('waiter_id');

        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $query->whereBetween('order_date', [$fromDate, $toDate]);
        }

        if (request('waiter_id')) {
            $query->where('waiter_id', request('waiter_id'));
        }

        $allWaiters = $query->with('waiter')->get();

        $data = [
            'totalOrders' => $allWaiters->sum('total_orders'),
            'totalRevenue' => $allWaiters->sum('total_revenue'),
            'totalCogs' => $allWaiters->sum('total_cogs'),
            'totalProfit' => $allWaiters->sum('total_profit'),
        ];

        // Excel export
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'waiter-performance-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new WaiterPerformanceExport($allWaiters, $data), $fileName);
            }
        }

        // PDF export
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.waiter-performance', [
                    'waiters' => $allWaiters,
                    'data' => $data
                ]);
            }
        }

        // Pagination
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }

        if ($parpage === null) {
            $waiters = $allWaiters;
        } else {
            $paginateQuery = Sale::whereNotNull('waiter_id')
                ->where('status', '!=', 'cancelled')
                ->selectRaw('waiter_id, COUNT(*) as total_orders, SUM(grand_total) as total_revenue, SUM(total_cogs) as total_cogs, SUM(gross_profit) as total_profit')
                ->groupBy('waiter_id');

            if (request('from_date') || request('to_date')) {
                $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
                $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
                $paginateQuery->whereBetween('order_date', [$fromDate, $toDate]);
            }
            if (request('waiter_id')) {
                $paginateQuery->where('waiter_id', request('waiter_id'));
            }

            $waiters = $paginateQuery->with('waiter')->paginate($parpage);
            $waiters->appends(request()->query());
        }

        $waiterList = Admin::role('Waiter')->orderBy('name')->get();

        return view('report::waiter-performance', compact('waiters', 'data', 'waiterList'));
    }

    /**
     * Order Type Report
     */
    public function orderType()
    {
        $query = Sale::where('status', '!=', 'cancelled')
            ->selectRaw('order_type, COUNT(*) as total_orders, SUM(grand_total) as total_revenue, SUM(total_cogs) as total_cogs, SUM(gross_profit) as total_profit')
            ->groupBy('order_type');

        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $query->whereBetween('order_date', [$fromDate, $toDate]);
        }

        $orderTypes = $query->get();

        $grandTotalOrders = $orderTypes->sum('total_orders');

        $data = [
            'totalOrders' => $grandTotalOrders,
            'totalRevenue' => $orderTypes->sum('total_revenue'),
            'totalCogs' => $orderTypes->sum('total_cogs'),
            'totalProfit' => $orderTypes->sum('total_profit'),
        ];

        // Excel export
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'order-type-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new OrderTypeExport($orderTypes, $data), $fileName);
            }
        }

        // PDF export
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.order-type', [
                    'orderTypes' => $orderTypes,
                    'data' => $data,
                    'grandTotalOrders' => $grandTotalOrders,
                ]);
            }
        }

        return view('report::order-type', compact('orderTypes', 'data', 'grandTotalOrders'));
    }

    /**
     * Table Performance Report
     */
    public function tablePerformance()
    {
        $query = Sale::whereNotNull('table_id')
            ->where('status', '!=', 'cancelled')
            ->selectRaw('table_id, COUNT(*) as total_orders, SUM(grand_total) as total_revenue')
            ->groupBy('table_id');

        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $query->whereBetween('order_date', [$fromDate, $toDate]);
        }

        if (request('floor')) {
            $query->whereHas('table', function ($q) {
                $q->where('floor', request('floor'));
            });
        }

        $allTables = $query->with('table')->get();

        $data = [
            'totalOrders' => $allTables->sum('total_orders'),
            'totalRevenue' => $allTables->sum('total_revenue'),
        ];

        // Excel export
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'table-performance-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new TablePerformanceExport($allTables, $data), $fileName);
            }
        }

        // PDF export
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.table-performance', [
                    'tables' => $allTables,
                    'data' => $data
                ]);
            }
        }

        // Pagination
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }

        if ($parpage === null) {
            $tables = $allTables;
        } else {
            $paginateQuery = Sale::whereNotNull('table_id')
                ->where('status', '!=', 'cancelled')
                ->selectRaw('table_id, COUNT(*) as total_orders, SUM(grand_total) as total_revenue')
                ->groupBy('table_id');

            if (request('from_date') || request('to_date')) {
                $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
                $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
                $paginateQuery->whereBetween('order_date', [$fromDate, $toDate]);
            }
            if (request('floor')) {
                $paginateQuery->whereHas('table', function ($q) {
                    $q->where('floor', request('floor'));
                });
            }

            $tables = $paginateQuery->with('table')->paginate($parpage);
            $tables->appends(request()->query());
        }

        $floors = RestaurantTable::select('floor')->distinct()->whereNotNull('floor')->orderBy('floor')->pluck('floor');

        return view('report::table-performance', compact('tables', 'data', 'floors'));
    }

    public function customers(Request $request)
    {
        $query = User::query();

        $query = $query->with(['sales' => fn($q) => $q->where('status', '!=', 'cancelled'), 'payment']);

        $query->when($request->filled('keyword'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->keyword . '%')
                ->orWhere('email', 'like', '%' . $request->keyword . '%')
                ->orWhere('phone', 'like', '%' . $request->keyword . '%')
                ->orWhere('address', 'like', '%' . $request->keyword . '%');
        });

        // Order by name ascending
        $query->orderBy('name', 'asc');

        $allCustomers = $query->get();

        $totalSales = 0;
        $totalAmount = 0;
        $totalPaid = 0;
        $totalDue = 0;
        foreach ($allCustomers as $customer) {
            $totalSales += $customer->sales->count();
            $totalAmount += $customer->sales->sum('grand_total');
            $totalPaid += $customer->total_paid;
            $totalDue += $customer->total_due;
        }

        $data = [
            'totalSales' => $totalSales,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
        ];

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $customers = $allCustomers;
        } else {
            $customers = User::with(['sales' => fn($q) => $q->where('status', '!=', 'cancelled'), 'payment'])->orderBy('name', 'asc')->paginate($parpage);
            $customers->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'customers-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new CustomerReportExport($allCustomers, $data), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.customer-report', [
                    'customers' => $allCustomers,
                    'data' => $data
                ]);
            }
        }

        return view('report::customer', compact('customers', 'totalSales', 'totalAmount', 'totalPaid', 'totalDue'));
    }

    public function detailsSale()
    {
        $sales = Sale::where('status', '!=', 'cancelled')
            ->with(['customer', 'payment', 'payment.account', 'saleReturns']);

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }

        if (request()->keyword) {
            $sales = $sales->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%');
            })
                ->orWhere('invoice', request()->keyword);
        }

        // Get all data for calculations and export
        $allSales = $sales->get();

        $data['sale_amount'] = 0;
        $data['total_amount'] = 0;
        $data['paid_amount'] = 0;
        $data['due_amount'] = 0;
        $data['return_amount'] = 0;
        foreach ($allSales as $sale) {
            $data['sale_amount'] += $sale->total_price;
            $data['total_amount'] += $sale->grand_total;
            $data['paid_amount'] += $sale->paid_amount;
            $data['due_amount'] += $sale->due_amount;
            $data['return_amount'] += $sale->saleReturns->sum('return_amount');
        }

        // Export with ALL data (not paginated)
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'details-sale-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new DetailsSaleReportExport($allSales, $data), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.details-sale', [
                    'sales' => $allSales,
                    'data' => $data
                ]);
            }
        }

        // Paginate for view
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $sales = $allSales;
        } else {
            $sales = $sales->paginate($parpage);
            $sales->appends(request()->query());
        }

        return view('report::details-sale', compact('sales', 'data'));
    }

    public function expense()
    {
        $expenses = Expense::with('createdBy', 'expenseType');

        if (request()->keyword) {
            $expenses = $expenses->where(function ($q) {
                $q->where('note', 'like', '%' . request()->keyword . '%')
                    ->orWhereHas('expenseType', function ($query) {
                        $query->where('name', 'like', '%' . request()->keyword . '%');
                    });
            });
        }

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $expenses = $expenses->whereBetween('date', [$fromDate, $toDate]);
        }

        // Get all data for calculations and export
        $allExpenses = $expenses->get();
        $totalAmount = $allExpenses->sum('amount');

        $data = ['totalAmount' => $totalAmount];

        // Export with ALL data (not paginated)
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'expense-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new ExpenseReportExport($allExpenses, $data), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.expense', [
                    'expenses' => $allExpenses,
                    'data' => $data
                ]);
            }
        }

        // Paginate for view
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $expenses = $allExpenses;
        } else {
            $expenses = $expenses->paginate($parpage);
            $expenses->appends(request()->query());
        }

        return view('report::expense', compact('expenses', 'totalAmount'));
    }

    public function profitLoss()
    {
        checkAdminHasPermissionAndThrowException('report.view');

        // Build base queries
        $salesQuery = Sale::where('status', '!=', 'cancelled');
        $salesReturnQuery = SalesReturn::query();
        $purchaseReturnQuery = PurchaseReturn::query();
        $expenseQuery = Expense::query();
        $salaryQuery = EmployeeSalary::query();

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? Carbon::parse(request('from_date'))->startOfDay() : now()->subYear()->startOfDay();
            $toDate = request('to_date') ? Carbon::parse(request('to_date'))->endOfDay() : now()->endOfDay();

            $salesQuery->whereBetween('order_date', [$fromDate, $toDate]);
            $salesReturnQuery->whereBetween('created_at', [$fromDate, $toDate]);
            $purchaseReturnQuery->whereBetween('created_at', [$fromDate, $toDate]);
            $expenseQuery->whereBetween('date', [$fromDate, $toDate]);
            $salaryQuery->whereBetween('date', [$fromDate, $toDate]);

            $data['fromDate'] = $fromDate->format('d-m-Y');
            $data['toDate'] = $toDate->format('d-m-Y');
        } else {
            $data['fromDate'] = __('All Time');
            $data['toDate'] = now()->format('d-m-Y');
        }

        // Income - Total Sales Revenue
        $data['totalSales'] = $salesQuery->sum('grand_total');
        $data['salesReturns'] = $salesReturnQuery->sum('return_amount');
        $data['netSales'] = $data['totalSales'] - $data['salesReturns'];

        // Purchase Returns (money received back from supplier)
        $data['purchaseReturns'] = $purchaseReturnQuery->sum('return_amount');

        // Total Income
        $data['totalIncome'] = $data['netSales'] + $data['purchaseReturns'];

        // COGS - Use pre-calculated COGS from sales (weighted average cost based)
        $data['cogs'] = (clone $salesQuery)->sum('total_cogs') ?? 0;

        // If no pre-calculated COGS exists, fall back to legacy calculation
        if ($data['cogs'] == 0) {
            $productSaleQuery = ProductSale::where('source', 1)
                ->whereHas('sale', fn($q) => $q->where('status', '!=', 'cancelled'));
            if (isset($fromDate) && isset($toDate)) {
                $productSaleQuery->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                    $q->where('status', '!=', 'cancelled')->whereBetween('order_date', [$fromDate, $toDate]);
                });
            }

            // First try to get COGS from cogs_amount field (new method)
            $data['cogs'] = $productSaleQuery->sum('cogs_amount');

            // If still 0, calculate from purchase prices (legacy method)
            if ($data['cogs'] == 0) {
                $productSales = $productSaleQuery->with('product', 'menuItem')->get();
                $cogs = 0;
                foreach ($productSales as $sale) {
                    if ($sale->cogs_amount) {
                        $cogs += $sale->cogs_amount;
                    } else {
                        $purchasePrice = $sale->purchase_price ?? 0;
                        if (!$purchasePrice && $sale->product) {
                            $purchasePrice = $sale->product->average_cost ?? $sale->product->LastPurchasePrice ?? $sale->product->cost ?? 0;
                        }
                        $cogs += (float) remove_comma($purchasePrice) * abs($sale->quantity);
                    }
                }
                $data['cogs'] = $cogs;
            }
        }

        // Gross Profit = Net Sales - COGS
        $data['grossProfit'] = $data['netSales'] - $data['cogs'];

        // Operating Expenses
        $data['expenses'] = $expenseQuery->sum('amount');
        $data['salaries'] = $salaryQuery->sum('amount');

        // Wastage Cost (from stock adjustments)
        $wastageQuery = \Modules\StockAdjustment\app\Models\StockAdjustment::whereIn('adjustment_type', ['wastage', 'damage', 'theft', 'consumption'])
            ->where('status', 'approved');
        if (isset($fromDate) && isset($toDate)) {
            $wastageQuery->whereBetween('adjustment_date', [$fromDate, $toDate]);
        }
        $data['wastageCost'] = $wastageQuery->sum('total_cost');

        // Total Expenses = COGS + Operating Expenses + Salaries + Wastage
        $data['totalExpenses'] = $data['cogs'] + $data['expenses'] + $data['salaries'] + $data['wastageCost'];

        // Net Profit/Loss = Total Income - Total Expenses
        $data['profitLoss'] = $data['totalIncome'] - $data['totalExpenses'];

        // Gross Profit Margin
        $data['grossProfitMargin'] = $data['netSales'] > 0 ? ($data['grossProfit'] / $data['netSales']) * 100 : 0;

        // Net Profit Margin
        $data['netProfitMargin'] = $data['netSales'] > 0 ? ($data['profitLoss'] / $data['netSales']) * 100 : 0;

        // Excel Export
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'profit-loss-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new \App\Exports\ProfitLossExport($data), $fileName);
            }
        }

        // PDF Export
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.profit-loss', compact('data'));
            }
        }

        return view('report::profit-loss', compact('data'));
    }

    /**
     * Low Stock Alert Report - Shows ingredients below their stock alert threshold
     */
    public function lowStockAlert()
    {
        checkAdminHasPermissionAndThrowException('report.view');

        $ingredients = \Modules\Ingredient\app\Models\Ingredient::where('status', 1)
            ->whereRaw('CAST(REPLACE(stock, ",", "") AS DECIMAL(15,4)) <= stock_alert')
            ->with(['purchaseUnit', 'consumptionUnit', 'category'])
            ->orderByRaw('CAST(REPLACE(stock, ",", "") AS DECIMAL(15,4)) ASC')
            ->get();

        $criticalCount = $ingredients->filter(function ($item) {
            return (float) str_replace(',', '', $item->stock) <= 0;
        })->count();

        $lowCount = $ingredients->count() - $criticalCount;

        $data = [
            'totalItems' => $ingredients->count(),
            'criticalCount' => $criticalCount,
            'lowCount' => $lowCount,
        ];

        return view('report::low-stock-alert', compact('ingredients', 'data'));
    }

    public function purchase()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();

        $purchases = Purchase::with(['supplier', 'purchaseDetails', 'createdBy']);
        if (request('from_date') || request('to_date')) {
            $purchases = $purchases->whereBetween('purchase_date', [$fromDate, $toDate]);
        }

        if (request()->keyword) {
            $purchases = $purchases->where(function ($q) {
                $q->whereHas('supplier', function ($query) {
                    $query->where('name', 'like', '%' . request()->keyword . '%');
                })
                    ->orWhere('invoice_number', request()->keyword);
            });
        }

        // Get all data for calculations and export
        $allPurchases = $purchases->get();

        $data['total_amount'] = $allPurchases->sum('total_amount');
        $data['paid_amount'] = $allPurchases->sum('paid_amount');
        $data['due_amount'] = $allPurchases->sum('due_amount');

        // Export with ALL data (not paginated)
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'purchase-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new PurchaseReportExport($allPurchases, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.purchase', [
                    'purchases' => $allPurchases
                ]);
            }
        }

        // Paginate for view
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $purchases = $allPurchases;
        } else {
            $purchases = $purchases->paginate($parpage);
            $purchases->appends(request()->query());
        }

        return view('report::purchase', compact('purchases', 'data'));
    }

    public function supplier()
    {
        $suppliers = $this->supplierService->allSupplier();

        // Get all suppliers for calculations
        $allSuppliers = $suppliers->get();

        $data['totalPurchase'] = 0;
        $data['pay'] = 0;
        $data['total_return'] = 0;
        $data['total_return_pay'] = 0;
        $data['total_due'] = 0;
        $data['purchase_count'] = 0;

        foreach ($allSuppliers as $supplier) {
            $data['totalPurchase'] += $supplier->purchases->sum('total_amount');
            $data['pay'] += $supplier->payments->sum('amount');

            $totalReturn = $supplier->purchaseReturn->sum('return_amount');
            $data['total_return'] += $totalReturn;

            $data['total_return_pay'] += $supplier->purchaseReturn->sum(
                'received_amount',
            );

            $data['total_due'] += $supplier->total_due - $totalReturn;
            $data['purchase_count'] += $supplier->purchases->count();
        }

        // Export with ALL suppliers (not paginated)
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'suppliers-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new SuppliersReportExport($allSuppliers, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.supplier', [
                    'suppliers' => $allSuppliers,
                    'data' => $data
                ]);
            }
        }

        // Paginate for view
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $suppliers = $allSuppliers;
        } else {
            $suppliers = $suppliers->paginate($parpage);
            $suppliers->appends(request()->query());
        }

        return view('report::supplier', compact('suppliers', 'data'));
    }



    public function supplierPayment()
    {
        $supplierPayments = Purchase::with(['supplier', 'purchaseReturn']);

        if (request()->keyword) {
            $supplierPayments = $supplierPayments->where(function ($q) {
                $q->whereHas('supplier', function ($query) {
                    $query->where('name', 'like', '%' . request()->keyword . '%');
                })
                    ->orWhere('invoice_number', request()->keyword);
            });
        }

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $supplierPayments = $supplierPayments->whereBetween('purchase_date', [$fromDate, $toDate]);
        }

        // Get all data for calculations and export
        $allSupplierPayments = $supplierPayments->get();

        $data['total'] = 0;
        $data['paid_amount'] = 0;
        $data['due_amount'] = 0;
        $data['return_amount'] = 0;

        foreach ($allSupplierPayments as $payment) {
            $data['total'] += $payment->total_amount;
            $data['paid_amount'] += $payment->paid_amount;
            $data['due_amount'] += $payment->due_amount - $payment->purchaseReturn->sum('return_amount') + $payment->purchaseReturn->sum('received_amount');
            $data['return_amount'] += $payment->purchaseReturn->sum('return_amount');
        }

        $totalAmount = $data['total'];

        // Export with ALL data (not paginated)
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'suppliers-payment-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new SuppliersPaymentReportExport($allSupplierPayments, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.supplier-payment', [
                    'supplierPayments' => $allSupplierPayments,
                    'data' => $data
                ]);
            }
        }

        // Paginate for view
        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $supplierPayments = $allSupplierPayments;
        } else {
            $supplierPayments = $supplierPayments->paginate($parpage);
            $supplierPayments->appends(request()->query());
        }

        return view('report::supplier-payment', compact('supplierPayments', 'totalAmount', 'data'));
    }

    public function salary()
    {
        $months = [];
        $years = [];
        if (request('from_date') && request('to_date')) {
            $fromDate = Carbon::createFromFormat('d/m/Y', '01/' . request('from_date'));
            $toDate = Carbon::createFromFormat('d/m/Y', '01/' . request('to_date'));
            while ($fromDate <= $toDate) {
                $months[] = $fromDate->format('F');
                $years[] = $fromDate->year;
                $fromDate->addMonth();
            }
        } else {
            for ($month = 1; $month <= 12; $month++) {
                $months[] = Carbon::createFromDate(null, $month)->format('F');
                $years[] = now()->year;
            }
        }

        $employees = $this->employeeService->all();

        if (request()->keyword) {
            $employees = $employees->where('name', 'like', '%' . request()->keyword . '%');
        }
        if (request()->order_by) {
            $employees = $employees->orderBy('name', request()->order_by);
        } else {
            $employees = $employees->orderBy('name');
        }


        $employees = $employees->get()->map(function ($employee) use ($months, $years) {
            $totalSalary = 0;
            $paidSalary = 0;

            foreach ($months as $index => $month) {

                $year = $years[$index];

                $requestData = [
                    'month' => $month,
                    'year' => $year
                ];

                $newRequest = new Request($requestData);


                [,,,
                    $payableSalary
                ] = $this->employeeService->calculateSalary($newRequest, $employee->id);


                $totalSalary += $payableSalary;
                $paidSalary += $employee->currentSalary->where('month', $month)->where('year', $year)->sum('amount');
            }

            $employee->total_salary = $totalSalary;
            $employee->paid_salary = $paidSalary;

            return $employee;
        });

        // Calculate totals
        $data = [
            'total_salary' => $employees->sum('total_salary'),
            'paid_salary' => $employees->sum('paid_salary'),
        ];

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'salaries-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new SalaryReportExport($employees, $data), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.salary', [
                    'employees' => $employees,
                    'data' => $data
                ]);
            }
        }


        return view('report::salary', compact('employees', 'data'));
    }
}
