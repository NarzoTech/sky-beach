<?php

namespace Modules\Accounts\app\Models;

use App\Models\Asset;
use App\Models\Balance;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Accounts\Database\factories\AccountFactory;
use Modules\Customer\app\Models\CustomerPayment;
use Modules\Employee\app\Models\EmployeeSalary;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseSupplierPayment;
use Modules\Supplier\app\Models\SupplierPayment;
use Modules\Accounts\app\Models\BalanceTransfer;

class Account extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'account_type',
        'mobile_bank_name',
        'mobile_number',
        'bank_id',
        'service_charge',
        'card_type',
        'card_holder_name',
        'card_number',
        'bank_account_type',
        'bank_account_name',
        'bank_account_number',
        'bank_account_branch',
    ];

    /**
     * The attributes that should be cast to native types.
     */

    public function bank()
    {
        return $this->belongsTo(Bank::class)->withDefault();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function balance()
    {
        $receive =  $this->payments()->where('is_received', 1)->sum('amount');
        $paid = $this->payments()->where('is_paid', 1)->sum('amount');

        // Supplier Payments
        $supplierPaymentsReceived = $this->supplierPayments()->where('is_received', 1)->sum('amount');
        $supplierPaymentsPaid = $this->supplierPayments()->where('is_paid', 1)->sum('amount');

        // Customer Payments (from sales)
        $customerPaymentsReceived = $this->customerPayments()->where('is_received', 1)->sum('amount');
        $customerPaymentsPaid = $this->customerPayments()->where('is_paid', 1)->sum('amount');

        // Expense Supplier Payments
        $expenseSupplierPaymentsReceived = $this->expenseSupplierPayments()->where('is_received', 1)->sum('amount');
        $expenseSupplierPaymentsPaid = $this->expenseSupplierPayments()->where('is_paid', 1)->sum('amount');

        $deposit = $this->deposits()->sum('amount');
        $withdraw = $this->withdraws()->sum('amount');
        $asset = $this->assets()->sum('amount');
        $expenses = $this->expenses->sum('amount');

        // Balance Transfers
        $transfersIn = $this->transfersIn()->sum('amount');
        $transfersOut = $this->transfersOut()->sum('amount');

        $balance = ($receive + $deposit + $supplierPaymentsReceived + $customerPaymentsReceived + $expenseSupplierPaymentsReceived + $transfersIn)
            - ($paid + $withdraw + $asset + $expenses + $supplierPaymentsPaid + $customerPaymentsPaid + $expenseSupplierPaymentsPaid + $transfersOut);
        return $balance;
    }

    public function expenses()
    {
        // Expenses are now tracked via ExpenseSupplierPayment for proper multi-account support
        // Only return expenses that have NO payment records (legacy data only)
        return $this->hasMany(Expense::class, 'account_id')
            ->whereNull('expense_supplier_id')
            ->whereDoesntHave('payments');
    }
    public function salary()
    {
        return $this->hasMany(EmployeeSalary::class, 'account_id', 'id');
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class, 'account_id', 'id');
    }

    public function customerPayments()
    {
        return $this->hasMany(CustomerPayment::class, 'account_id', 'id');
    }

    public function expenseSupplierPayments()
    {
        return $this->hasMany(ExpenseSupplierPayment::class, 'account_id', 'id');
    }

    public function deposits()
    {
        return $this->hasMany(Balance::class, 'account_id')->where('balance_type', 'deposit');
    }

    public function withdraws()
    {
        return $this->hasMany(Balance::class, 'account_id')->where('balance_type', 'withdraw');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'account_id');
    }

    /**
     * Transfers where this account is the source (money going out)
     */
    public function transfersOut()
    {
        return $this->hasMany(BalanceTransfer::class, 'from_account_id');
    }

    /**
     * Transfers where this account is the destination (money coming in)
     */
    public function transfersIn()
    {
        return $this->hasMany(BalanceTransfer::class, 'to_account_id');
    }

    public function getOpeningBalance($startDate)
    {
        // Payments Received and Paid before the start date
        $receivedPayments = $this->payments()
            ->where('is_received', 1)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        $paidPayments = $this->payments()
            ->where('is_paid', 1)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        // Supplier Payments Received and Paid before the start date
        $supplierPaymentsReceived = $this->supplierPayments()
            ->where('is_received', 1)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        $supplierPaymentsPaid = $this->supplierPayments()
            ->where('is_paid', 1)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        // Customer Payments Received and Paid before the start date
        $customerPaymentsReceived = $this->customerPayments()
            ->where('is_received', 1)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        $customerPaymentsPaid = $this->customerPayments()
            ->where('is_paid', 1)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        // Expense Supplier Payments Received and Paid before the start date
        $expenseSupplierPaymentsReceived = $this->expenseSupplierPayments()
            ->where('is_received', 1)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        $expenseSupplierPaymentsPaid = $this->expenseSupplierPayments()
            ->where('is_paid', 1)
            ->where('payment_date', '<', $startDate)
            ->sum('amount');

        // Deposits, Withdrawals, Assets, and Expenses before the start date
        $deposit = $this->deposits()
            ->where('date', '<', $startDate)
            ->sum('amount');

        $withdraw = $this->withdraws()
            ->where('date', '<', $startDate)
            ->sum('amount');

        $asset = $this->assets()
            ->where('date', '<', $startDate)
            ->sum('amount');

        $expenses = $this->expenses()
            ->where('date', '<', $startDate)
            ->sum('amount');

        $salary = $this->salary()
            ->where('date', '<', $startDate)
            ->sum('amount');

        // Balance Transfers before the start date
        $transfersIn = $this->transfersIn()
            ->where('date', '<', $startDate)
            ->sum('amount');

        $transfersOut = $this->transfersOut()
            ->where('date', '<', $startDate)
            ->sum('amount');

        // Calculate Opening Balance
        $openingBalance = ($receivedPayments + $deposit + $supplierPaymentsReceived + $customerPaymentsReceived + $expenseSupplierPaymentsReceived + $transfersIn)
            - ($paidPayments + $withdraw + $asset + $expenses + $supplierPaymentsPaid + $customerPaymentsPaid + $salary + $expenseSupplierPaymentsPaid + $transfersOut);
        return $openingBalance;
    }

    public function getBalanceBetween($startDate = null, $endDate = null)
    {
        // Check if date filters are provided via request or parameters
        $hasDateFilter = $startDate || $endDate || request('from_date') || request('to_date');

        if ($hasDateFilter) {
            $startDate = $startDate ? $startDate : (request('from_date') ? now()->parse(request('from_date')) : null);
            $endDate = $endDate ? $endDate : (request('to_date') ? now()->parse(request('to_date')) : now());
        }

        // Payments Received and Paid
        $receivedPaymentsQuery = $this->payments()->where('is_received', 1);
        $paidPaymentsQuery = $this->payments()->where('is_paid', 1);

        if ($hasDateFilter && $startDate && $endDate) {
            $receivedPaymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
            $paidPaymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $receivedPayments = $receivedPaymentsQuery->sum('amount');
        $paidPayments = $paidPaymentsQuery->sum('amount');

        // Supplier Payments Received and Paid
        $supplierPaymentsReceivedQuery = $this->supplierPayments()->where('is_received', 1);
        $supplierPaymentsPaidQuery = $this->supplierPayments()->where('is_paid', 1);

        if ($hasDateFilter && $startDate && $endDate) {
            $supplierPaymentsReceivedQuery->whereBetween('payment_date', [$startDate, $endDate]);
            $supplierPaymentsPaidQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $supplierPaymentsReceived = $supplierPaymentsReceivedQuery->sum('amount');
        $supplierPaymentsPaid = $supplierPaymentsPaidQuery->sum('amount');

        // Customer Payments Received and Paid
        $customerPaymentsReceivedQuery = $this->customerPayments()->where('is_received', 1);
        $customerPaymentsPaidQuery = $this->customerPayments()->where('is_paid', 1);

        if ($hasDateFilter && $startDate && $endDate) {
            $customerPaymentsReceivedQuery->whereBetween('payment_date', [$startDate, $endDate]);
            $customerPaymentsPaidQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $customerPaymentsReceived = $customerPaymentsReceivedQuery->sum('amount');
        $customerPaymentsPaid = $customerPaymentsPaidQuery->sum('amount');

        // Expense Supplier Payments Received and Paid
        $expenseSupplierPaymentsReceivedQuery = $this->expenseSupplierPayments()->where('is_received', 1);
        $expenseSupplierPaymentsPaidQuery = $this->expenseSupplierPayments()->where('is_paid', 1);

        if ($hasDateFilter && $startDate && $endDate) {
            $expenseSupplierPaymentsReceivedQuery->whereBetween('payment_date', [$startDate, $endDate]);
            $expenseSupplierPaymentsPaidQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }

        $expenseSupplierPaymentsReceived = $expenseSupplierPaymentsReceivedQuery->sum('amount');
        $expenseSupplierPaymentsPaid = $expenseSupplierPaymentsPaidQuery->sum('amount');

        // Deposits, Withdraws, Assets, Expenses and Salary
        $depositQuery = $this->deposits();
        $withdrawQuery = $this->withdraws();
        $assetQuery = $this->assets();
        $expensesQuery = $this->expenses();
        $salaryQuery = $this->salary();

        if ($hasDateFilter && $startDate && $endDate) {
            $depositQuery->whereBetween('date', [$startDate, $endDate]);
            $withdrawQuery->whereBetween('date', [$startDate, $endDate]);
            $assetQuery->whereBetween('date', [$startDate, $endDate]);
            $expensesQuery->whereBetween('date', [$startDate, $endDate]);
            $salaryQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $deposit = $depositQuery->sum('amount');
        $withdraw = $withdrawQuery->sum('amount');
        $asset = $assetQuery->sum('amount');
        $expenses = $expensesQuery->sum('amount');
        $salary = $salaryQuery->sum('amount');

        // Balance Transfers
        $transfersInQuery = $this->transfersIn();
        $transfersOutQuery = $this->transfersOut();

        if ($hasDateFilter && $startDate && $endDate) {
            $transfersInQuery->whereBetween('date', [$startDate, $endDate]);
            $transfersOutQuery->whereBetween('date', [$startDate, $endDate]);
        }

        $transfersIn = $transfersInQuery->sum('amount');
        $transfersOut = $transfersOutQuery->sum('amount');

        // Calculate Balance
        $balance = ($receivedPayments + $deposit + $supplierPaymentsReceived + $customerPaymentsReceived + $expenseSupplierPaymentsReceived + $transfersIn)
            - ($paidPayments + $withdraw + $asset + $expenses + $supplierPaymentsPaid + $customerPaymentsPaid + $salary + $expenseSupplierPaymentsPaid + $transfersOut);

        return $balance;
    }
}
