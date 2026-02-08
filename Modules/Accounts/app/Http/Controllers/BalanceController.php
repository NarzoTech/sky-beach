<?php

namespace Modules\Accounts\app\Http\Controllers;

use App\Exports\BalanceTransferExport;
use App\Http\Controllers\Controller;
use App\Models\Balance;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Models\Account;
use Modules\Accounts\app\Models\BalanceTransfer;
use Modules\Accounts\app\Services\AccountsService;

class BalanceController extends Controller
{
    public function __construct(private AccountsService $account)
    {
        $this->middleware('auth:admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function openingBalance()
    {
        checkAdminHasPermissionAndThrowException('deposit.withdraw.view');
        $accounts = $this->account->all()->get();
        $deposits = Balance::with('account')->where('balance_type', 'deposit')->paginate(20);
        $deposits->appends(request()->query());

        $withdraws = Balance::with('account')->where('balance_type', 'withdraw')->paginate(20);
        $withdraws->appends(request()->query());

        $totalDeposits = Balance::where('balance_type', 'deposit')->sum('amount');
        $totalWithdraws = Balance::where('balance_type', 'withdraw')->sum('amount');

        $accountBalance = 0;
        $accounts->map(function ($account) use (&$accountBalance) {
            $accountBalance += $account->balance();
        });

        return view('accounts::balance', compact('accounts', 'deposits', 'withdraws', 'totalDeposits', 'totalWithdraws', 'accountBalance'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('deposit.withdraw.create');

        $request->validate([
            'balance_type' => 'required|in:deposit,withdraw',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required',
            'account_id' => 'required_unless:payment_type,cash,advance',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
                $account = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $account = $this->account->find($request->account_id);
                if (!$account) {
                    return back()->withInput()->with(['messege' => __('Selected account not found.'), 'alert-type' => 'error']);
                }
            }

            Balance::create([
                'balance_type' => $request->balance_type,
                'date' => now()->parse($request->date),
                'amount' => $request->amount,
                'account_id' => $account->id,
                'note' => $request->note,
                'payment_type' => $request->payment_type,
                'created_by' => auth('admin')->id(),
            ]);

            $message = $request->balance_type == 'deposit' ? __('Deposit created successfully.') : __('Withdraw created successfully.');
            return back()->with(['messege' => $message, 'alert-type' => 'success']);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return back()->withInput()->with(['messege' => __('Something went wrong.'), 'alert-type' => 'error']);
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
        checkAdminHasPermissionAndThrowException('deposit.withdraw.edit');
        $accounts = $this->account->all()->orderBy('id', 'desc')->get();
        $deposits = Balance::with('account')->where('balance_type', 'deposit')->paginate(20);
        $deposits->appends(request()->query());

        $withdraws = Balance::with('account')->where('balance_type', 'withdraw')->paginate(20);
        $withdraws->appends(request()->query());

        $totalDeposits = Balance::where('balance_type', 'deposit')->sum('amount');
        $totalWithdraws = Balance::where('balance_type', 'withdraw')->sum('amount');
        $balance = Balance::findOrFail($id);

        $accountBalance = 0;
        $accounts->map(function ($account) use (&$accountBalance) {

            $accountBalance += $account->balance();
        });
        return view('accounts::balance-edit', compact('accounts', 'deposits', 'withdraws', 'balance', 'totalDeposits', 'totalWithdraws', 'accountBalance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        checkAdminHasPermissionAndThrowException('deposit.withdraw.edit');

        $request->validate([
            'balance_type' => 'required|in:deposit,withdraw',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required',
            'account_id' => 'required_unless:payment_type,cash,advance',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            if ($request->payment_type == 'cash' || $request->payment_type == 'advance') {
                $account = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $account = $this->account->find($request->account_id);
                if (!$account) {
                    return back()->withInput()->with(['messege' => __('Selected account not found.'), 'alert-type' => 'error']);
                }
            }

            $balance = Balance::findOrFail($id);

            $data = $request->except('_token', '_method');
            $data['updated_by'] = auth('admin')->id();
            $data['account_id'] = $account->id;
            $data['date'] = now()->parse($request->date);
            $balance->update($data);
            return to_route('admin.opening-balance')->with(['messege' => __('Balance updated successfully.'), 'alert-type' => 'success']);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return back()->withInput()->with(['messege' => __('Something went wrong.'), 'alert-type' => 'error']);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        checkAdminHasPermissionAndThrowException('deposit.withdraw.delete');
        $balance = Balance::findOrFail($id);
        $balance->delete();
        return back()->with(['messege' => __('Balance deleted successfully.'), 'alert-type' => 'success']);
    }

    public function transfer()
    {
        checkAdminHasPermissionAndThrowException('balance.transfer.view');

        $accounts = $this->account->all()->get();

        $transfers = BalanceTransfer::with(['fromAccount.bank', 'toAccount.bank', 'createdBy']);

        // Keyword search
        if (request('keyword')) {
            $keyword = request('keyword');
            $transfers->where(function ($q) use ($keyword) {
                $q->where('amount', 'like', "%{$keyword}%")
                    ->orWhere('note', 'like', "%{$keyword}%")
                    ->orWhereHas('fromAccount', function ($q) use ($keyword) {
                        $q->where('bank_account_name', 'like', "%{$keyword}%")
                            ->orWhere('bank_account_number', 'like', "%{$keyword}%")
                            ->orWhere('mobile_number', 'like', "%{$keyword}%")
                            ->orWhere('mobile_bank_name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('toAccount', function ($q) use ($keyword) {
                        $q->where('bank_account_name', 'like', "%{$keyword}%")
                            ->orWhere('bank_account_number', 'like', "%{$keyword}%")
                            ->orWhere('mobile_number', 'like', "%{$keyword}%")
                            ->orWhere('mobile_bank_name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('createdBy', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        // Ordering
        $orderDirection = request('order_by', 'desc');
        $transfers->orderBy('id', $orderDirection);

        // Export (before pagination so all records are included)
        if (checkAdminHasPermission('balance.transfer.excel.download')) {
            if (request('export')) {
                $allTransfers = $transfers->get();
                $fileName = 'transfer-' . date('Y-m-d') . '_' . date('h-i-s') . '.xlsx';
                return Excel::download(new BalanceTransferExport($allTransfers), $fileName);
            }
        }

        if (checkAdminHasPermission('balance.transfer.pdf.download')) {
            if (request('export_pdf')) {
                $allTransfers = $transfers->get();
                return view('accounts::pdf.transfer', [
                    'transfers' => $allTransfers,
                ]);
            }
        }

        // Pagination
        if (request('par-page')) {
            if (request('par-page') == 'all') {
                $transfers = $transfers->get();
            } else {
                $transfers = $transfers->paginate(request('par-page'));
                $transfers->appends(request()->query());
            }
        } else {
            $transfers = $transfers->paginate(20);
            $transfers->appends(request()->query());
        }

        return view('accounts::balance-transfer', compact('accounts', 'transfers'));
    }


    public function transferStore(Request $request)
    {
        checkAdminHasPermissionAndThrowException('balance.transfer.create');

        // Validate required fields
        $request->validate([
            'date' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'from_account_type' => 'required',
            'to_account_type' => 'required',
            'from_account' => 'required_unless:from_account_type,cash',
            'to_account' => 'required_unless:to_account_type,cash',
        ]);

        try {
            $data = $request->except('_token');
            $data['created_by'] = auth('admin')->id();
            $data['date'] = now()->parse($request->date);

            // from account
            if ($request->from_account_type == 'cash') {
                $fromAccount = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $fromAccount = Account::where('account_type', $request->from_account_type)
                    ->where('id', $request->from_account)->first();
            }

            if (!$fromAccount) {
                return back()->with(['messege' => __('From account not found.'), 'alert-type' => 'error']);
            }

            $data['from_account_id'] = $fromAccount->id;

            // to account
            if ($request->to_account_type == 'cash') {
                $toAccount = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $toAccount = Account::where('account_type', $request->to_account_type)
                    ->where('id', $request->to_account)->first();
            }

            if (!$toAccount) {
                return back()->with(['messege' => __('To account not found. Please make sure the account exists.'), 'alert-type' => 'error']);
            }

            $data['to_account_id'] = $toAccount->id;

            // Prevent transfer to the same account
            if ($fromAccount->id === $toAccount->id) {
                return back()->with(['messege' => __('Cannot transfer to the same account.'), 'alert-type' => 'error']);
            }

            BalanceTransfer::create($data);
            return back()->with(['messege' => __('Balance transfer created successfully.'), 'alert-type' => 'success']);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return back()->withInput()->with(['messege' => __('Something went wrong.'), 'alert-type' => 'error']);
        }
    }

    public function transferUpdate(Request $request, $id)
    {
        checkAdminHasPermissionAndThrowException('balance.transfer.edit');

        // Validate required fields
        $request->validate([
            'date' => 'required',
            'amount' => 'required|numeric|min:0.01',
            'from_account_type' => 'required',
            'to_account_type' => 'required',
            'from_account' => 'required_unless:from_account_type,cash',
            'to_account' => 'required_unless:to_account_type,cash',
        ]);

        try {
            $data = $request->except('_token', '_method');
            $data['date'] = now()->parse($request->date);
            $balance = BalanceTransfer::findOrFail($id);

            // from account
            if ($request->from_account_type == 'cash') {
                $fromAccount = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $fromAccount = Account::where('account_type', $request->from_account_type)
                    ->where('id', $request->from_account)->first();
            }

            if (!$fromAccount) {
                return back()->with(['messege' => __('From account not found.'), 'alert-type' => 'error']);
            }

            $data['from_account_id'] = $fromAccount->id;

            if ($request->to_account_type == 'cash') {
                $toAccount = Account::firstOrCreate(
                    ['account_type' => 'cash'],
                    ['bank_account_name' => 'Cash Register']
                );
            } else {
                $toAccount = Account::where('account_type', $request->to_account_type)
                    ->where('id', $request->to_account)->first();
            }

            if (!$toAccount) {
                return back()->with(['messege' => __('To account not found. Please make sure the account exists.'), 'alert-type' => 'error']);
            }

            $data['to_account_id'] = $toAccount->id;

            // Prevent transfer to the same account
            if ($fromAccount->id === $toAccount->id) {
                return back()->with(['messege' => __('Cannot transfer to the same account.'), 'alert-type' => 'error']);
            }

            $balance->update($data);
            return back()->with(['messege' => __('Balance transfer updated successfully.'), 'alert-type' => 'success']);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return back()->withInput()->with(['messege' => __('Something went wrong.'), 'alert-type' => 'error']);
        }
    }


    public function transferDestroy($id)
    {
        checkAdminHasPermissionAndThrowException('balance.transfer.delete');
        $balance = BalanceTransfer::findOrFail($id);
        $balance->delete();
        return back()->with(['messege' => __('Balance transfer deleted successfully.'), 'alert-type' => 'success']);
    }
}
