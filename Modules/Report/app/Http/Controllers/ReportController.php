<?php

namespace Modules\Report\app\Http\Controllers;

use App\Exports\BarcodeWiseProductExport;
use App\Exports\BarcodeWiseSaleExport;
use App\Exports\CategoryWiseExport;
use App\Exports\CustomerReportExport;
use App\Exports\DetailsSaleReportExport;
use App\Exports\DueDateSaleReportExport;
use App\Exports\ExpenseReportExport;
use App\Exports\PurchaseReportExport;
use App\Exports\ReceivableReportExport;
use App\Exports\SalaryReportExport;
use App\Exports\SuppliersPaymentReportExport;
use App\Exports\SuppliersReportExport;
use App\Exports\TotalReceiveReportExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Employee\app\Services\EmployeeService;
use Modules\Expense\app\Models\Expense;
use Modules\Ingredient\app\Services\BrandService;
use Modules\Ingredient\app\Services\IngredientCategoryService;
use Modules\Ingredient\app\Services\IngredientService;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseDetails;
use Modules\Purchase\app\Models\PurchaseReturn;
use Modules\Sales\app\Models\ProductSale;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;
use Modules\Sales\app\Models\SalesReturnDetails;
use Modules\Service\app\Models\Service;
use Modules\Supplier\app\Services\SupplierService;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Service\app\Models\ServiceCategory;
use Modules\Supplier\app\Models\SupplierPayment;

class ReportController extends Controller
{

    public function __construct(private BrandService $brandService, private IngredientCategoryService $categoryService, private IngredientService $productService, private SupplierService $supplierService, private EmployeeService $employeeService, private AccountsService $accountsService)
    {
        $this->middleware('auth:admin');
    }
    public function barcodeWiseProduct()
    {
        $products = $this->productService->getProducts();
        $products = $products->where('status', 1);

        // Calculate totals from ALL products before pagination
        $allProducts = $products->get();

        $totalSalePrice = 0;
        $totalSaleQty = 0;
        $totalReturnPrice = 0;
        $totalReturnQty = 0;
        $totalPurchasePrice = 0;
        $totalPurchaseQty = 0;

        foreach ($allProducts as $product) {
            $totalSalePrice += (int) $product->sales['price'];
            $totalSaleQty += $product->sales['qty'];
            $totalReturnPrice += (int) $product->sales_return['price'];
            $totalReturnQty += $product->sales_return['qty'];
            $totalPurchasePrice += (int) $product->total_purchase['price'];
            $totalPurchaseQty += $product->total_purchase['qty'];
        }

        $data = [
            'totalSalePrice' => $totalSalePrice,
            'totalSaleQty' => $totalSaleQty,
            'totalReturnPrice' => $totalReturnPrice,
            'totalReturnQty' => $totalReturnQty,
            'totalPurchasePrice' => $totalPurchasePrice,
            'totalPurchaseQty' => $totalPurchaseQty,
        ];

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $products = $allProducts;
        } else {
            $products = $this->productService->getProducts()->where('status', 1)->paginate($parpage);
            $products->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'barcode-wise-product-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new BarcodeWiseProductExport($allProducts, $data), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.barcode-wise-product', [
                    'products' => $allProducts,
                    'data' => $data
                ]);
            }
        }


        return view('report::barcode-wise-product', compact('products', 'data'));
    }

    public function barcodeSale()
    {
        $products = $this->productService->getProducts();
        $products = $products->where('status', 1);
        $allProducts = $products->get();

        $totalStock = 0;
        $sellCount = 0;
        $sellPrice = 0;
        $totalPurchasePrice = 0;
        $totalProfitLoss = 0;

        foreach ($allProducts as $product) {
            $sellQty = $product->sales['qty'] - $product->sales_return['qty'];
            $sellingPrice = $sellQty > 0 ? $product->sales['price'] / $sellQty : 0;
            $profitLoss = $sellQty * $sellingPrice - $sellQty * $product->purchase_price;

            $totalStock += $product->stock_count;
            $sellCount += $sellQty;
            $sellPrice += $sellingPrice;
            $totalPurchasePrice += $product->purchase_price;
            $totalProfitLoss += $profitLoss;
        }

        $data = [
            'totalStock' => $totalStock,
            'sellCount' => $sellCount,
            'sellPrice' => $sellPrice,
            'totalPurchasePrice' => $totalPurchasePrice,
            'totalProfitLoss' => $totalProfitLoss,
        ];

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $products = $allProducts;
        } else {
            $products = $this->productService->getProducts()->where('status', 1)->paginate($parpage);
            $products->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'barcode-wise-sale-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new BarcodeWiseSaleExport($allProducts, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.barcode-sale', [
                    'products' => $allProducts,
                    'data' => $data
                ]);
            }
        }

        return view('report::barcode-sale', compact('products', 'totalStock', 'sellCount', 'sellPrice', 'totalPurchasePrice'));
    }


    public function categories()
    {
        $categories = $this->categoryService->getCategories();

        // Calculate totals from ALL categories before pagination
        $allCategories = $categories->get();

        $data = [
            'totalPurchaseCount' => 0,
            'totalSalesCount' => 0,
            'totalPurchaseAmount' => 0,
            'totalSalesAmount' => 0,
        ];

        foreach ($allCategories as $category) {
            $data['totalPurchaseCount'] += $category->PurchaseSummary['count'];
            $data['totalSalesCount'] += $category->sales_count;
            $data['totalPurchaseAmount'] += $category->PurchaseSummary['amount'];
            $data['totalSalesAmount'] += $category->sales_amount;
        }

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $categories = $allCategories;
        } else {
            $categories = $this->categoryService->getCategories()->paginate($parpage);
            $categories->appends(request()->query());
        }

        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'category-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new CategoryWiseExport($allCategories, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.categories', [
                    'categories' => $allCategories,
                    'data' => $data
                ]);
            }
        }

        return view('report::categories', compact('categories', 'data'));
    }
    public function customers(Request $request)
    {
        $query = User::query();

        $query = $query->with(['sales', 'payment']);

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
            $customers = User::with(['sales', 'payment'])->orderBy('name', 'asc')->paginate($parpage);
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

    public function receivable()
    {
        $sales = Sale::with('customer')->where('payment_status', 1)->where('due_amount', '>', 0);

        if (request()->keyword) {
            $sales = $sales->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%');
            })
                ->orWhere('invoice', request()->keyword);
        }

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subYear();
            $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }

        // Get all data for calculations and export
        $allSales = $sales->get();
        $totalDues = $allSales->sum('due_amount');

        $data = ['totalDues' => $totalDues];

        // Export with ALL data (not paginated)
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'receivable-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new ReceivableReportExport($allSales, $data), $fileName);
            }
        }
        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.receivable', [
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

        return view('report::receiveable', compact('sales', 'totalDues'));
    }

    public function detailsSale()
    {
        $sales = Sale::with(['customer', 'payment', 'payment.account', 'saleReturns']);

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

    public function dueDateSale()
    {
        $sales = Sale::with(['customer', 'payment', 'payment.account', 'saleReturns'])->where('due_amount', '>', 0);

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

        $data['due'] = 0;
        foreach ($allSales as $sale) {
            $data['due'] += $sale->due_amount;
        }

        // Export with ALL data (not paginated)
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'due-date-sale-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new DueDateSaleReportExport($allSales, $data), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.due-date-sale', [
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

        return view('report::due-date-sale', compact('sales', 'data'));
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

    public function masterSale()
    {

        $fromDate = request('from_date') ? now()->parse(request('from_date')) : now()->subDay();
        $toDate = request('to_date') ? now()->parse(request('to_date')) : now();
        // ->whereBetween('order_date', [$fromDate, $toDate])
        $sales = Sale::with('customer');
        if (request('from_date') || request('to_date')) {
            $sales = $sales->whereBetween('order_date', [$fromDate, $toDate]);
        }

        $totalAmount = $sales->sum('grand_total');
        $sales = $sales->paginate(20);
        $sales->appends(request()->query());
        return view('report::master-sale', compact('sales', 'totalAmount'));
    }

    public function monthlySale()
    {
        $month = request('month') ? now()->parse(request('month')) : now()->month;

        $sales = Sale::with('customer')->whereMonth('order_date', $month);
        $totalAmount = $sales->sum('grand_total');
        $sales = $sales->paginate(20);
        $sales->appends(request()->query());
        return view('report::monthly-sale', compact('sales', 'totalAmount'));
    }

    public function profitLoss()
    {
        checkAdminHasPermissionAndThrowException('report.view');

        // Build base queries
        $salesQuery = Sale::query();
        $productSaleQuery = ProductSale::whereNotNull('product_id')->where('source', 1);
        $salesReturnQuery = SalesReturn::query();
        $salesReturnDetailsQuery = SalesReturnDetails::whereNotNull('product_id')->where('source', 1);
        $purchaseReturnQuery = PurchaseReturn::query();
        $expenseQuery = Expense::query();
        $salaryQuery = EmployeeSalary::query();

        // Only filter by date if dates are provided
        if (request('from_date') || request('to_date')) {
            $fromDate = request('from_date') ? Carbon::parse(request('from_date'))->startOfDay() : now()->subYear()->startOfDay();
            $toDate = request('to_date') ? Carbon::parse(request('to_date'))->endOfDay() : now()->endOfDay();

            $salesQuery->whereBetween('order_date', [$fromDate, $toDate]);
            $productSaleQuery->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('order_date', [$fromDate, $toDate]);
            });
            $salesReturnQuery->whereBetween('created_at', [$fromDate, $toDate]);
            $salesReturnDetailsQuery->whereHas('saleReturn', function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('created_at', [$fromDate, $toDate]);
            });
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

        // Cost of Goods Sold (COGS) - based on sold products purchase price, not all purchases
        $productSales = $productSaleQuery->with('product')->get();
        $cogs = 0;
        foreach ($productSales as $sale) {
            // Use purchase_price from ProductSale if available, otherwise get from Product model
            $purchasePrice = $sale->purchase_price;
            if (!$purchasePrice && $sale->product) {
                $purchasePrice = $sale->product->LastPurchasePrice ?: $sale->product->cost;
            }
            $cogs += (float) remove_comma($purchasePrice ?? 0) * abs($sale->quantity);
        }

        // Reduce COGS for returned items (they go back to inventory)
        $returnDetails = $salesReturnDetailsQuery->with('product')->get();
        $returnsCogs = 0;
        foreach ($returnDetails as $detail) {
            $purchasePrice = $detail->product->LastPurchasePrice ?: $detail->product->cost;
            $returnsCogs += (float) remove_comma($purchasePrice ?? 0) * abs($detail->quantity);
        }

        $data['cogs'] = $cogs - $returnsCogs;

        // Gross Profit = Net Sales - COGS
        $data['grossProfit'] = $data['netSales'] - $data['cogs'];

        // Operating Expenses
        $data['expenses'] = $expenseQuery->sum('amount');
        $data['salaries'] = $salaryQuery->sum('amount');

        // Total Expenses = COGS + Operating Expenses + Salaries
        $data['totalExpenses'] = $data['cogs'] + $data['expenses'] + $data['salaries'];

        // Net Profit/Loss = Total Income - Total Expenses
        $data['profitLoss'] = $data['totalIncome'] - $data['totalExpenses'];

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

    public function productSaleReport()
    {
        $products = $this->productService->getProducts();
        $products = $products->where('status', 1);
        $totalProducts = $products->get();

        $totalStock = 0;
        $sellCount = 0;
        $sellPrice = 0;
        $totalPurchasePrice = 0;

        $totalProducts->map(function ($product) use (&$totalStock, &$sellCount, &$sellPrice, &$totalPurchasePrice) {
            $sellQty = $product->sales['qty'] - $product->sales_return['qty'];
            $sellCount += $sellQty;

            $sellPrice += $sellQty > 0 ? $product->sales['price'] / $sellQty : 0;

            $totalPurchasePrice += $sellCount * $product->purchase_price;

            $totalStock += $product->stock_count;

            return;
        });

        $products = $products->paginate(20);
        $products = $products->appends(request()->query());
        return view('report::product-sale-report', compact('products', 'totalStock', 'sellCount', 'sellPrice', 'totalPurchasePrice'));
    }

    public function  receivedReport()
    {
        $totalReceive = CustomerPayment::with(['account', 'sale', 'customer'])->where('is_received', 1)->where('amount', '>', 0);

        if (request('from_date') || request('to_date')) {
            $totalReceive = $totalReceive->whereBetween('created_at', [request('from_date'), request('to_date')]);
        }


        if (request()->keyword) {
            $totalReceive = $totalReceive->where(function ($q) {
                $q->whereHas('customer', function ($query) {
                    $query->where('name', 'like', '%' . request()->keyword . '%');
                });
            });
        }

        // Get all data for calculations and export
        $allReceive = $totalReceive->get();

        $data['receive'] = $allReceive->sum('amount');

        // Export with ALL data (not paginated)
        if (checkAdminHasPermission('report.excel.download')) {
            if (request('export')) {
                $fileName = 'received-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new TotalReceiveReportExport($allReceive, $data), $fileName);
            }
        }

        if (checkAdminHasPermission('report.pdf.download')) {
            if (request('export_pdf')) {
                return view('report::pdf.received-report', [
                    'totalReceive' => $allReceive
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
            $totalReceive = $allReceive;
        } else {
            $totalReceive = $totalReceive->paginate($parpage);
            $totalReceive->appends(request()->query());
        }

        return view('report::received-report', compact('totalReceive', 'data'));
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
