<?php
namespace Modules\Expense\app\Http\Controllers;

use App\Enums\RedirectType;
use App\Exports\ExpensesExport;
use App\Http\Controllers\Controller;
use App\Traits\RedirectHelperTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Models\Account;
use Modules\Expense\app\Http\Requests\ExpenseRequest;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseSupplier;
use Modules\Expense\app\Models\ExpenseType;
use Modules\Expense\app\Services\ExpenseService;

class ExpenseController extends Controller
{

    use RedirectHelperTrait;
    public function __construct(private ExpenseService $expense)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        checkAdminHasPermissionAndThrowException('expense.view');
        $expenses = Expense::query()->with('expenseSupplier');

        if (request('keyword')) {
            $keyword  = request('keyword');
            $expenses = $expenses->where(function ($query) use ($keyword) {
                $query->where('amount', 'like', "%{$keyword}%")
                    ->orWhere('note', 'like', "%{$keyword}%")
                    ->orWhereHas('expenseType', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('subExpenseType', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('account', function ($q) use ($keyword) {
                        $q->where('account_type', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('createdBy', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('expenseSupplier', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        // Filter by payment status
        if (request('payment_status')) {
            $status = request('payment_status');
            if ($status == 'paid') {
                $expenses = $expenses->where(function ($q) {
                    $q->where('due_amount', 0)
                        ->orWhereNull('expense_supplier_id');
                });
            } elseif ($status == 'partial') {
                $expenses = $expenses->whereNotNull('expense_supplier_id')
                    ->where('due_amount', '>', 0)
                    ->where('paid_amount', '>', 0);
            } elseif ($status == 'due') {
                $expenses = $expenses->whereNotNull('expense_supplier_id')
                    ->where('due_amount', '>', 0)
                    ->where('paid_amount', 0);
            }
        }

        // Filter by supplier
        if (request('expense_supplier_id')) {
            $expenses = $expenses->where('expense_supplier_id', request('expense_supplier_id'));
        }

        $sort = request()->order_by ? request()->order_by : 'desc';

        if (request('order_type')) {
            $expenses = $expenses->orderBy(request('order_type'), $sort);
        } else {
            $expenses = $expenses->orderBy('date', $sort);
        }
        if (request('from_date') && request('to_date')) {
            $from     = now()->parse(request('from_date'));
            $to       = now()->parse(request('to_date'));
            $expenses = $expenses->whereBetween('date', [$from, $to]);
        }

        if (request('export')) {
            $fileName = 'expenses-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
            return Excel::download(new ExpensesExport($expenses->get()), $fileName);
        }
        $totalAmount = $expenses->sum('amount');
        $totalPaid = $expenses->sum('paid_amount');
        $totalDue = $expenses->sum('due_amount');

        if (request('par-page')) {
            $parpage = request('par-page') == 'all' ? null : request('par-page');
        } else {
            $parpage = 20;
        }
        if ($parpage === null) {
            $expenses = $expenses->get();
        } else {
            $expenses = $expenses->paginate($parpage);
            $expenses->appends(request()->query());
        }

        if (checkAdminHasPermission('expense.excel.download')) {
            if (request('export')) {
                $fileName = 'expense-report-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new ExpensesExport($expenses), $fileName);
            }
        }
        if (checkAdminHasPermission('expense.pdf.download')) {
            if (request('export_pdf')) {

                return view('expense::pdf.expense', [
                    'expenses' => $expenses,
                ]);
            }
        }

        $types    = ExpenseType::all();
        $accounts = Account::with('bank')->get();
        $expenseSuppliers = ExpenseSupplier::where('status', 1)->get();
        return view('expense::index', compact('expenses', 'types', 'accounts', 'totalAmount', 'totalPaid', 'totalDue', 'expenseSuppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseRequest $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('expense.create');
        try {
            $this->expense->store($request);
            return $this->redirectWithMessage(RedirectType::CREATE->value, 'admin.expense.index', [], ['messege' => 'Expense created successfully', 'alert-type' => 'success']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->redirectWithMessage(RedirectType::CREATE->value, null, [], ['messege' => $exception->getMessage(), 'alert-type' => 'danger']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('expense.edit');
        try {
            $this->expense->update($request, $id);
            return $this->redirectWithMessage(RedirectType::UPDATE->value, 'admin.expense.index', [], ['messege' => 'Expense updated successfully', 'alert-type' => 'success']);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->redirectWithMessage(RedirectType::UPDATE->value, null, [], ['messege' => $exception->getMessage(), 'alert-type' => 'danger']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('expense.delete');
        $this->expense->destroy($id);
        return $this->redirectWithMessage(RedirectType::DELETE->value, 'admin.expense.index', [], ['messege' => 'Expense deleted successfully', 'alert-type' => 'success']);
    }
}
