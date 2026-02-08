<?php

namespace Modules\Accounts\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Http\Controllers\Controller;
use App\Models\Balance;
use App\Traits\RedirectHelperTrait;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Modules\Accounts\app\Http\Requests\AccountRequest;
use Modules\Accounts\app\Models\BalanceTransfer;
use Modules\Accounts\app\Services\AccountsService;
use Modules\Accounts\app\Services\BankService;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseSupplierPayment;
use Modules\Sales\app\Models\ProductSale;
use Modules\Supplier\app\Models\SupplierPayment;

class AccountsController extends Controller
{
    use RedirectHelperTrait;
    public function __construct(private BankService $bankService, private AccountsService $accountsService)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('account.view');
        $bankAccounts = $this->accountsService->all()->where('account_type', 'bank')->with('payments')->get();
        $cashAccount = $this->accountsService->all()->where('account_type', 'cash')->with('payments')->first();
        $mobileAccounts = $this->accountsService->all()->where('account_type', 'mobile_banking')->with('payments')->get();
        $cardAccounts = $this->accountsService->all()->where('account_type', 'card')->with('payments')->get();

        $totalAccounts = $this->accountsService->all()->get();

        $accountBalance = 0;
        $totalAccounts->map(function ($account) use (&$accountBalance) {
            $accountBalance += $account->getBalanceBetween();
        });

        return view('accounts::index', compact('bankAccounts', 'cashAccount', 'mobileAccounts', 'cardAccounts', 'accountBalance'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        checkAdminHasPermissionAndThrowException('account.create');
        $accounts = $this->bankService->all()->get();
        return view('accounts::create', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AccountRequest $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('account.create');
        try {
            $this->accountsService->create($request->except('_token'));
            return redirect()->route('admin.accounts.index')->with(['messege' => __('Account created successfully'), 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->withInput()->with(['messege' => __('Something went wrong'), 'alert-type' => 'error']);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('accounts::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        checkAdminHasPermissionAndThrowException('account.edit');
        $account = $this->accountsService->find($id);
        $accounts = $this->bankService->all()->get();
        return view('accounts::edit', compact('account', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AccountRequest $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('account.edit');
        try {
            $account = $this->accountsService->find($id);
            $this->accountsService->update($account, $request->except('_token'));
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.accounts.edit', ['account' => $id], ['messege' => __('Account updated successfully'), 'alert-type' => 'success']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.accounts.edit', ['account' => $id], ['messege' => __('Something went wrong'), 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('account.delete');
        $account = $this->accountsService->find($id);
        $this->accountsService->delete($account);
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.accounts.index', [], ['messege' => __('Account deleted successfully'), 'alert-type' => 'success']);
    }

    public function cashflow()
    {
        checkAdminHasPermissionAndThrowException('cash.flow.view');

        // Check if date filter is applied - show all-time data by default
        $hasDateFilter = request('from_date') || request('to_date');
        $fromDate = request('from_date') ? now()->parse(request('from_date')) : null;
        $toDate = request('to_date') ? now()->parse(request('to_date')) : null;

        $data = [];

        // Helper function to apply date filter
        $applyDateFilter = function ($query, $dateColumn) use ($hasDateFilter, $fromDate, $toDate) {
            if ($hasDateFilter) {
                if ($fromDate && $toDate) {
                    $query->whereBetween($dateColumn, [$fromDate, $toDate]);
                } elseif ($fromDate) {
                    $query->where($dateColumn, '>=', $fromDate);
                } elseif ($toDate) {
                    $query->where($dateColumn, '<=', $toDate);
                }
            }
            return $query;
        };

        // Service Sale
        $serviceSaleQuery = ProductSale::whereNotNull('service_id');
        if ($hasDateFilter) {
            $serviceSaleQuery->whereHas('sale', function ($q) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $q->whereBetween('order_date', [$fromDate, $toDate]);
                } elseif ($fromDate) {
                    $q->where('order_date', '>=', $fromDate);
                } elseif ($toDate) {
                    $q->where('order_date', '<=', $toDate);
                }
            });
        }
        $data['serviceSale'] = $serviceSaleQuery->sum('sub_total');

        // Product Sale
        $productSaleQuery = CustomerPayment::where('payment_type', 'sale');
        $applyDateFilter($productSaleQuery, 'payment_date');
        $data['productSale'] = $productSaleQuery->sum('amount') - $data['serviceSale'];

        // Customer Due
        $customerDueQuery = CustomerPayment::where('payment_type', 'due_receive');
        $applyDateFilter($customerDueQuery, 'payment_date');
        $data['customer_due'] = $customerDueQuery->sum('amount');

        // Sale Return (actual cash refunded to customer)
        $saleReturnQuery = CustomerPayment::where('payment_type', 'sale return');
        $applyDateFilter($saleReturnQuery, 'payment_date');
        $data['sale_return'] = $saleReturnQuery->sum('amount');

        // Balance Deposit
        $balanceDepositQuery = Balance::where('balance_type', 'deposit');
        $applyDateFilter($balanceDepositQuery, 'date');
        $data['balance_deposit'] = $balanceDepositQuery->sum('amount');

        // Balance Withdraw
        $balanceWithdrawQuery = Balance::where('balance_type', 'withdraw');
        $applyDateFilter($balanceWithdrawQuery, 'date');
        $data['balance_withdraw'] = $balanceWithdrawQuery->sum('amount');

        // Customer Advance
        $customerAdvanceQuery = CustomerPayment::where('payment_type', 'advance_receive');
        $applyDateFilter($customerAdvanceQuery, 'payment_date');
        $data['customer_advance'] = $customerAdvanceQuery->sum('amount');

        // Customer Advance Refund
        $customerAdvanceRefundQuery = CustomerPayment::where('payment_type', 'advance_refund');
        $applyDateFilter($customerAdvanceRefundQuery, 'payment_date');
        $data['customer_advance_refund'] = $customerAdvanceRefundQuery->sum('amount');

        // Salary
        $salaryQuery = EmployeeSalary::query();
        $applyDateFilter($salaryQuery, 'date');
        $data['salary'] = $salaryQuery->sum('amount');

        // Expenses (legacy expenses WITHOUT supplier AND without payment records)
        $legacyExpensesQuery = Expense::whereNull('expense_supplier_id')
            ->whereDoesntHave('payments');
        $applyDateFilter($legacyExpensesQuery, 'date');
        $legacyExpenses = $legacyExpensesQuery->sum('amount');

        // Direct expenses (non-supplier expenses with payment records)
        $directExpenseQuery = ExpenseSupplierPayment::where('payment_type', 'direct_expense');
        $applyDateFilter($directExpenseQuery, 'payment_date');
        $directExpenses = $directExpenseQuery->sum('amount');

        $data['expenses'] = $legacyExpenses + $directExpenses;

        // Supplier Due Pay
        $supplierDuePayQuery = SupplierPayment::where('payment_type', 'due_pay');
        $applyDateFilter($supplierDuePayQuery, 'payment_date');
        $data['supplierDuePay'] = $supplierDuePayQuery->sum('amount');

        // Supplier Advance Pay
        $supplierAdvancePayQuery = SupplierPayment::where('payment_type', 'advance_pay');
        $applyDateFilter($supplierAdvancePayQuery, 'payment_date');
        $data['supplierAdvancePay'] = $supplierAdvancePayQuery->sum('amount');

        // Supplier Advance Refund
        $supplierAdvanceRefundQuery = SupplierPayment::where('payment_type', 'advance_refund');
        $applyDateFilter($supplierAdvanceRefundQuery, 'payment_date');
        $data['supplierAdvanceRefund'] = $supplierAdvanceRefundQuery->sum('amount');

        // Purchase
        $purchaseQuery = SupplierPayment::where('payment_type', 'purchase');
        $applyDateFilter($purchaseQuery, 'payment_date');
        $data['purchase'] = $purchaseQuery->sum('amount');

        // Purchase Return (money received from supplier)
        $purchaseReturnQuery = SupplierPayment::where('payment_type', 'purchase_receive');
        $applyDateFilter($purchaseReturnQuery, 'payment_date');
        $data['purchaseReturn'] = $purchaseReturnQuery->sum('amount');

        // Expense Supplier Due Pay
        $expenseSupplierDuePayQuery = ExpenseSupplierPayment::where('payment_type', 'due_pay');
        $applyDateFilter($expenseSupplierDuePayQuery, 'payment_date');
        $data['expenseSupplierDuePay'] = $expenseSupplierDuePayQuery->sum('amount');

        // Expense Supplier Advance Pay
        $expenseSupplierAdvancePayQuery = ExpenseSupplierPayment::where('payment_type', 'advance_pay');
        $applyDateFilter($expenseSupplierAdvancePayQuery, 'payment_date');
        $data['expenseSupplierAdvancePay'] = $expenseSupplierAdvancePayQuery->sum('amount');

        // Expense Supplier Advance Refund
        $expenseSupplierAdvanceRefundQuery = ExpenseSupplierPayment::where('payment_type', 'advance_refund');
        $applyDateFilter($expenseSupplierAdvanceRefundQuery, 'payment_date');
        $data['expenseSupplierAdvanceRefund'] = $expenseSupplierAdvanceRefundQuery->sum('amount');

        // Expense Payment (paid amount at time of expense creation)
        $expensePaymentQuery = ExpenseSupplierPayment::where('payment_type', 'expense');
        $applyDateFilter($expensePaymentQuery, 'payment_date');
        $data['expenseSupplierPayment'] = $expensePaymentQuery->sum('amount');

        // Balance Transfers (for visibility - these are internal movements)
        $balanceTransferQuery = BalanceTransfer::query();
        $applyDateFilter($balanceTransferQuery, 'date');
        $data['balance_transfer'] = $balanceTransferQuery->sum('amount');

        $data['totalPay'] = $data['sale_return'] + $data['balance_withdraw'] + $data['customer_advance_refund'] + $data['supplierDuePay'] + $data['supplierAdvancePay'] + $data['purchase'] + $data['expenses'] + $data['salary'] + $data['expenseSupplierDuePay'] + $data['expenseSupplierAdvancePay'] + $data['expenseSupplierPayment'];

        $data['totalReceive'] = $data['productSale']  + $data['balance_deposit'] + $data['customer_advance'] + $data['customer_due'] + $data['supplierAdvanceRefund'] + $data['serviceSale'] + $data['purchaseReturn'] + $data['expenseSupplierAdvanceRefund'];

        // Opening balance is 0 for all-time view, or calculated from the start date when filtered
        $openingBalance = $hasDateFilter && $fromDate ? $this->accountsService->getOpeningBalance($fromDate) : 0;

        $currentBalance = $openingBalance + $data['totalReceive'] - $data['totalPay'];
        return view('accounts::cash-flow', compact('data', 'openingBalance', 'currentBalance', 'hasDateFilter'));
    }
}
