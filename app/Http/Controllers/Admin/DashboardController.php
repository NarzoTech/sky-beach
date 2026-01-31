<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Modules\Customer\app\Models\CustomerDue;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseType;
use Modules\Language\app\Models\Language;
use Modules\Ingredient\app\Models\Ingredient;
use Modules\Purchase\app\Models\Purchase;
use Modules\Purchase\app\Models\PurchaseReturn;
use Modules\Purchase\app\Services\PurchaseService;
use Modules\Sales\app\Models\Sale;
use Modules\Sales\app\Models\SalesReturn;
use Modules\Supplier\app\Services\SupplierService;

class DashboardController extends Controller
{

    public function __construct(private SupplierService $supplierService, private PurchaseService $purchaseService)
    {
        $this->middleware('auth:admin');
    }
    public function dashboard()
    {
        // Redirect waiters to their dedicated dashboard
        if (auth('admin')->user()->hasRole('Waiter')) {
            return redirect()->route('admin.waiter.dashboard');
        }

        $data['customerDues'] = CustomerDue::where('status', 1)->sum('due_amount');
        $data['todaySales'] = Sale::whereDate('order_date', date('Y-m-d'))->sum('grand_total');
        $data['totalIngredients'] = Ingredient::count();

        // Today's Purchase
        $data['todayPurchase'] = Purchase::whereDate('purchase_date', date('Y-m-d'))->sum('total_amount');

        // Today's Expense
        $data['todayExpense'] = Expense::whereDate('date', date('Y-m-d'))->sum('amount');

        // Total Sales (All Time)
        $data['totalSales'] = Sale::sum('grand_total');

        // Total Purchase (All Time)
        $data['totalPurchases'] = Purchase::sum('total_amount');

        $suppliers = $this->supplierService->allSupplier();

        $data['total_supplier_due'] = 0;
        foreach ($suppliers->get() as $supplier) {
            $totalReturn = $supplier->purchaseReturn->sum('return_amount');
            $data['total_supplier_due'] += $supplier->total_due - $totalReturn;
        }

        $data['suppliersDues'] = 0;



        $purchases = $this->purchaseService->all()
            ->selectRaw('DATE_FORMAT(purchase_date, "%Y-%m") as month, SUM(total_amount) as total')
            ->where('purchase_date', '>=', now()->subMonths(12))
            ->whereYear('purchase_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $purchasesData = $purchases->mapWithKeys(function ($purchase) {
            return [$purchase->month => $purchase->total];
        });



        $purchaseData = collect(Carbon::today()->startOfYear()->monthsUntil(Carbon::today()->endOfYear()))

            ->mapWithKeys(fn($date) => [$date->format('Y-m') => 0])
            ->merge($purchasesData)
            ->sortKeys();


        $sales = Sale::selectRaw('DATE_FORMAT(order_date, "%Y-%m") as month, SUM(grand_total) as total')
            ->where('order_date', '>=', now()->subMonths(12))
            ->whereYear('order_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();


        $salesData = $sales->mapWithKeys(function ($sale) {
            return [$sale->month => $sale->total];
        });

        $saleData = collect(Carbon::today()->startOfYear()->monthsUntil(Carbon::today()->endOfYear()))
            ->mapWithKeys(fn($date) => [$date->format('Y-m') => 0])
            ->merge($salesData)
            ->sortKeys();

        // current month sales with dates
        $currentMonthSales = Sale::selectRaw('DATE_FORMAT(order_date, "%Y-%m-%d") as date, SUM(grand_total) as total')
            ->where('order_date', '>=', now()->startOfMonth())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $currentMonthSalesData = $currentMonthSales->mapWithKeys(function ($sale) {
            return [$sale->date => $sale->total];
        });

        $currentMonthDates = collect(Carbon::now()->startOfMonth()->daysUntil(Carbon::now()->endOfMonth()))
            ->mapWithKeys(fn($date) => [$date->format('Y-m-d') => 0]);
        $chart['currentMonthSaleData'] = $currentMonthDates
            ->merge($currentMonthSalesData)
            ->sortKeys();

        // current month expense
        $chart['currentMonthExpense'] = Expense::where('date', '>=', now()->startOfMonth())->sum('amount');

        // last month expense
        $chart['lastMonthExpense'] = Expense::whereBetween('date', [
            now()->subMonthsNoOverflow()->startOfMonth(),
            now()->subMonthsNoOverflow()->endOfMonth(),
        ])->sum('amount');


        // calculate if current month expense is greater/smaller than last month expense and calculate percentage
        // if ($chart['currentMonthExpense'] > $chart['lastMonthExpense']) {
        //     $chart['expensePercentage'] = ($chart['currentMonthExpense'] - $chart['lastMonthExpense']) / $chart['lastMonthExpense'] * 100;
        // } elseif ($chart['currentMonthExpense'] < $chart['lastMonthExpense']) {
        //     $chart['expensePercentage'] = - ($chart['lastMonthExpense'] - $chart['currentMonthExpense']) / $chart['lastMonthExpense'] * 100;
        // } else {
        //     $chart['expensePercentage'] = 0;
        // }

        if ($chart['lastMonthExpense'] > 0) {
            if ($chart['currentMonthExpense'] > $chart['lastMonthExpense']) {
                $chart['expensePercentage'] = ($chart['currentMonthExpense'] - $chart['lastMonthExpense']) / $chart['lastMonthExpense'] * 100;
            } elseif ($chart['currentMonthExpense'] < $chart['lastMonthExpense']) {
                $chart['expensePercentage'] = - ($chart['lastMonthExpense'] - $chart['currentMonthExpense']) / $chart['lastMonthExpense'] * 100;
            } else {
                $chart['expensePercentage'] = 0;
            }
        } else {
            // Handle case where last month's expense is zero
            if ($chart['currentMonthExpense'] > 0) {
                $chart['expensePercentage'] = 100;
            } elseif ($chart['currentMonthExpense'] < 0) {
                $chart['expensePercentage'] = -100;
            } else {
                $chart['expensePercentage'] = 0;
            }
        }

        $chart['expensePercentage'] = number_format($chart['expensePercentage'], 2);



        // current month sales
        $chart['currentSales'] = Sale::where('order_date', '>=', now()->startOfMonth())->sum('grand_total');
        $lastSales = Sale::whereBetween('order_date', [
            now()->subMonthsNoOverflow()->startOfMonth(),
            now()->subMonthsNoOverflow()->endOfMonth(),
        ])->sum('grand_total');


        // if ($chart['currentSales'] > $lastSales) {
        //     $chart['salePercentage'] = ($chart['currentSales'] - $lastSales) / $lastSales * 100;
        // } elseif ($chart['currentSales'] < $lastSales) {

        //     $chart['salePercentage'] = - ($lastSales - $chart['currentSales']) / $lastSales * 100;
        // } else {
        //     $chart['salePercentage'] = 0;
        // }
        // $chart['salePercentage'] = number_format($chart['salePercentage'], 2);

        if ($lastSales > 0) {
            if ($chart['currentSales'] > $lastSales) {
                $chart['salePercentage'] = ($chart['currentSales'] - $lastSales) / $lastSales * 100;
            } elseif ($chart['currentSales'] < $lastSales) {
                $chart['salePercentage'] = - ($lastSales - $chart['currentSales']) / $lastSales * 100;
            } else {
                $chart['salePercentage'] = 0;
            }
        } else {
            // Handle case when last sales is zero
            $chart['salePercentage'] = $chart['currentSales'] > 0 ? 100 : 0;
        }

        $chart['salePercentage'] = number_format($chart['salePercentage'], 2);


        // current month purchase
        $chart['currentPurchases'] = Purchase::where('purchase_date', '>=', now()->startOfMonth())->sum('total_amount');
        $lastPurchases = Purchase::whereBetween('purchase_date', [
            now()->subMonthsNoOverflow()->startOfMonth(),
            now()->subMonthsNoOverflow()->endOfMonth(),
        ])->sum('total_amount');

        // if ($chart['currentPurchases'] > $lastPurchases) {
        //     $chart['purchasePercentage'] = ($chart['currentPurchases'] - $lastPurchases) / $lastPurchases * 100;
        // } elseif ($chart['currentPurchases'] < $lastPurchases) {
        //     $chart['purchasePercentage'] = - ($lastPurchases - $chart['currentPurchases']) / $lastPurchases * 100;
        // } else {
        //     $chart['purchasePercentage'] = 0;
        // }

        if ($lastPurchases > 0) {
            if ($chart['currentPurchases'] > $lastPurchases) {
                $chart['purchasePercentage'] = ($chart['currentPurchases'] - $lastPurchases) / $lastPurchases * 100;
            } elseif ($chart['currentPurchases'] < $lastPurchases) {
                $chart['purchasePercentage'] = - ($lastPurchases - $chart['currentPurchases']) / $lastPurchases * 100;
            } else {
                $chart['purchasePercentage'] = 0;
            }
        } else {
            // Handle the zero case: if there were no previous purchases
            $chart['purchasePercentage'] = $chart['currentPurchases'] > 0 ? 100 : 0;
        }


        $chart['purchasePercentage'] = number_format($chart['purchasePercentage'], 2);

        // low stock ingredients
        $data['low_stock_ingredients'] = Ingredient::where(function ($q) {
            $q->where('stock_alert', '!=', 0)
                ->whereColumn('stock', '<=', 'stock_alert');
        })->orderByDesc('stock_alert')
            ->get();

        $customers = User::with(['sales', 'payment', 'saleReturn'])->get();

        $customers = $customers->filter(function ($customer) {
            return $customer->getTotalDueAttribute() > 0;
        });

        $customers = $customers->sortByDesc(function ($customer) {
            $totalPurchase = $customer->sales->sum('grand_total');
            $totalPaid = $customer->payment->sum('amount');
            $totalReturn = $customer->saleReturn->sum('return_amount');
            $totalDue = $totalPurchase - $totalPaid - $totalReturn;
            return $totalDue;
        });

        $data['customers'] = $customers;

        $suppliers = $this->supplierService->allSupplier()->get();

        $suppliers = $suppliers->filter(function ($supplier) {
            // Check if the supplier has any purchases with due amount greater than 0
            return $supplier->purchases->where('due_amount', '>', 0)->isNotEmpty();
        })->sortByDesc(function ($supplier) {
            $totalPurchase = $supplier->purchases->sum('total_amount');
            $totalPaid = $supplier->payments->sum('amount');
            $totalReturn = $supplier->purchaseReturn->sum('return_amount');
            $totalDue = $totalPurchase - $totalPaid - $totalReturn;
            return $totalDue;
        });

        $data['suppliers'] = $suppliers;

        // Expense by Type for Pie Chart (Current Month)
        $expenseByType = Expense::where('date', '>=', now()->startOfMonth())
            ->selectRaw('expense_type_id, SUM(amount) as total')
            ->groupBy('expense_type_id')
            ->with('expenseType')
            ->get();

        $chart['expenseByType'] = $expenseByType->map(function ($expense) {
            return [
                'name' => $expense->expenseType->name ?? 'Unknown',
                'value' => (float) $expense->total
            ];
        })->values()->toArray();

        // Income vs Expense Pie Chart (Current Month)
        $currentMonthIncome = Sale::where('order_date', '>=', now()->startOfMonth())->sum('grand_total');
        $currentMonthPurchaseReturn = PurchaseReturn::where('created_at', '>=', now()->startOfMonth())->sum('return_amount');
        $totalIncome = $currentMonthIncome + $currentMonthPurchaseReturn;

        $currentMonthPurchase = Purchase::where('purchase_date', '>=', now()->startOfMonth())->sum('total_amount');
        $currentMonthSalary = EmployeeSalary::where('date', '>=', now()->startOfMonth())->sum('amount');
        $totalExpenseAmount = $chart['currentMonthExpense'] + $currentMonthPurchase + $currentMonthSalary;

        $chart['incomeVsExpense'] = [
            ['name' => __('Income'), 'value' => (float) $totalIncome],
            ['name' => __('Expenses'), 'value' => (float) $totalExpenseAmount]
        ];

        // Weekly Sales Data (Last 7 days)
        $weeklySales = Sale::where('order_date', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(order_date) as date, SUM(grand_total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $weeklyLabels = [];
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $weeklyLabels[] = now()->subDays($i)->format('D');
            $weeklyData[] = (float) ($weeklySales[$date] ?? 0);
        }
        $chart['weeklySalesLabels'] = $weeklyLabels;
        $chart['weeklySalesData'] = $weeklyData;

        // Profit/Loss for current month
        $salesReturns = SalesReturn::where('created_at', '>=', now()->startOfMonth())->sum('return_amount');
        $chart['currentMonthProfit'] = $totalIncome - $salesReturns - $totalExpenseAmount;

        return view('admin.dashboard', compact('data', 'purchaseData', 'saleData', 'chart'));
    }

    public function setLanguage()
    {
        $lang = Language::whereCode(request('code'))->first();

        if (session()->has('lang')) {
            session()->forget('lang');
            session()->forget('text_direction');
        }
        if ($lang) {
            session()->put('lang', $lang->code);
            session()->put('text_direction', $lang->direction);

            $notification = __('Language Changed Successfully');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];

            return redirect()->back()->with($notification);
        }

        session()->put('lang', config('app.locale'));

        $notification = __('Language Changed Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }
}
