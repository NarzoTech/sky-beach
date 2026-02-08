<?php

namespace Modules\Expense\app\Services;

use App\Models\Ledger;
use Illuminate\Http\Request;
use Modules\Accounts\app\Models\Account;
use Modules\Expense\app\Models\Expense;
use Modules\Expense\app\Models\ExpenseSupplier;
use Modules\Expense\app\Models\ExpenseSupplierPayment;

class ExpenseSupplierService
{
    public function __construct(private ExpenseSupplier $expenseSupplier) {}

    public function all()
    {
        return $this->expenseSupplier->where('status', 1);
    }

    public function allSuppliers()
    {
        $suppliers = $this->expenseSupplier->query();
        $suppliers = $suppliers->with(['expenses' => function ($query) {
            if (request()->from_date || request()->to_date) {
                [$from_date, $to_date] = $this->getDateRangeFromRequest();
                if ($from_date) {
                    $query->where('date', '>=', $from_date);
                }
                if ($to_date) {
                    $query->where('date', '<=', $to_date);
                }
            }
        }, 'payments' => function ($query) {
            $query->where('is_paid', 1);

            [$from_date, $to_date] = $this->getDateRangeFromRequest();

            if ($from_date) {
                $query->where('payment_date', '>=', $from_date);
            }

            if ($to_date) {
                $query->where('payment_date', '<=', $to_date);
            }
        }]);

        if (request()->keyword) {
            $suppliers = $suppliers->where(function ($q) {
                $q->where('name', 'like', '%' . request()->keyword . '%')
                    ->orWhere('company', 'like', '%' . request()->keyword . '%')
                    ->orWhere('phone', 'like', '%' . request()->keyword . '%')
                    ->orWhere('address', 'like', '%' . request()->keyword . '%')
                    ->orWhere('email', 'like', '%' . request()->keyword . '%')
                    ->orWhereHas('area', function ($q) {
                        $q->where('name', 'like', '%' . request()->keyword . '%');
                    });
            });
        }

        if (request()->order_by) {
            $suppliers = $suppliers->orderBy('name', request()->order_by);
        } else {
            $suppliers = $suppliers->orderBy('name', 'asc');
        }

        if (request()->order_type) {
            $orderBy = request()->order_by;
            $orderBy = $orderBy == 'asc' ? 'sortBy' : 'sortByDesc';
            switch (request()->order_type) {
                case 'due':
                    $suppliers = $suppliers->with(['expenses', 'payments'])
                        ->where(function ($q) {
                            $q->whereHas('expenses', function ($query) {
                                $query->where('due_amount', '>', 0);
                            });
                        })
                        ->get()
                        ->$orderBy(function ($supplier) {
                            return $supplier->total_due;
                        });
                    break;

                case 'paid':
                    $suppliers = $suppliers->with(['payments', 'expenses'])
                        ->whereHas('expenses')
                        ->get();
                    $suppliers = $suppliers->filter(function ($supplier) {
                        return $supplier->total_due <= 0;
                    });
                    $suppliers = $suppliers->$orderBy(function ($supplier) {
                        return $supplier->total_paid;
                    });
                    break;

                case 'total':
                    $suppliers = $suppliers->with(['expenses'])
                        ->get()
                        ->$orderBy(function ($supplier) {
                            return $supplier->total_expense;
                        });
                    break;

                default:
                    break;
            }
        }

        return $suppliers;
    }

    private function getDateRangeFromRequest()
    {
        $from_date = request()->from_date ? now()->parse(request()->from_date) : null;
        $to_date = request()->to_date ? now()->parse(request()->to_date) : null;

        return [$from_date, $to_date];
    }

    public function find($id)
    {
        return $this->expenseSupplier->with('dueExpenses')->find($id);
    }

    public function store(Request $request)
    {
        $data = $request->except('_token');
        $data['created_by'] = auth('admin')->id();
        return $this->expenseSupplier->create($data);
    }

    public function update(Request $request, $id)
    {
        $data = $request->except(['_token', '_method']);
        $data['updated_by'] = auth('admin')->id();
        return $this->expenseSupplier->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->expenseSupplier->where('id', $id)->delete();
    }

    public function duePay(Request $request, $id)
    {
        $supplier = $this->expenseSupplier->find($id);

        $supplier->balance = $supplier->balance - $request->paying_amount;
        $supplier->save();

        $account = $request->account_id;

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::firstOrCreate(
                ['account_type' => 'cash'],
                ['bank_account_name' => 'Cash Register']
            );
        } else {
            $account = Account::find($account);
        }

        // Create Ledger
        $ledger = new Ledger();
        $ledger->expense_supplier_id = $id;
        $ledger->amount = $request->paying_amount;
        $ledger->invoice_type = 'Expense Due Payment';
        $ledger->is_paid = 1;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber();
        $ledger->note = $request->note;
        $ledger->due_amount = -$request->paying_amount;
        $ledger->total_amount = 0;
        $ledger->date = now()->parse($request->payment_date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();

        $ledger->invoice_url = route('admin.expense-suppliers.ledger-details', $ledger->id);
        $ledger->save();

        // Create payment for each expense
        foreach ($request->expense_id as $index => $expenseId) {
            if (!isset($request->amount[$index]) || $request->amount[$index] == 0) {
                continue;
            }

            $expense = Expense::find($expenseId);

            $expense->paid_amount = $expense->paid_amount + $request->amount[$index];
            $expense->due_amount = $expense->due_amount - $request->amount[$index];
            $expense->save();

            // Create payment data
            ExpenseSupplierPayment::create([
                'expense_id' => $expense->id,
                'expense_supplier_id' => $id,
                'account_id' => $account->id,
                'payment_type' => 'due_pay',
                'is_paid' => 1,
                'amount' => $request->amount[$index],
                'payment_date' => now()->parse($request->payment_date),
                'note' => $request->note,
                'memo' => $request->memo,
                'invoice' => $this->genInvoiceNumber(),
                'ledger_id' => $ledger->id,
                'created_by' => auth('admin')->user()->id,
            ]);

            // Create ledger details
            $ledger->details()->create([
                'invoice' => 'EXP-' . $expense->id,
                'amount' => $request->amount[$index],
            ]);
        }
    }

    public function duePayHistory()
    {
        $list = ExpenseSupplierPayment::query();

        $list = $list->with('expense', 'expenseSupplier', 'createdBy')
            ->whereNotNull('expense_id')
            ->where('payment_type', 'due_pay');

        if (request()->from_date && request()->to_date) {
            $fromDate = \Carbon\Carbon::parse(request()->from_date)->startOfDay();
            $toDate = \Carbon\Carbon::parse(request()->to_date)->endOfDay();
            $list = $list->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        if (request()->keyword) {
            $keyword = '%' . request()->keyword . '%';
            $list = $list->where(function ($q) use ($keyword) {
                $q->where('note', 'like', $keyword)
                    ->orWhere('amount', 'like', $keyword)
                    ->orWhereHas('expenseSupplier', function ($query) use ($keyword) {
                        $query->where('name', 'like', $keyword)
                            ->orWhere('phone', 'like', $keyword)
                            ->orWhere('address', 'like', $keyword)
                            ->orWhere('email', 'like', $keyword);
                    });
            })
                ->orWhere('invoice', 'like', $keyword)
                ->orWhere('account_type', 'like', $keyword);
        }

        if (request()->order_by) {
            $list = $list->orderBy('payment_date', request()->order_by);
        } else {
            $list = $list->orderBy('payment_date', 'desc');
        }

        return $list;
    }

    public function duePayDelete($id)
    {
        $payment = ExpenseSupplierPayment::find($id);

        // Update expense paid/due amounts
        if ($payment->expense && $payment->expense->id) {
            $payment->expense->paid_amount = $payment->expense->paid_amount - $payment->amount;
            $payment->expense->due_amount = $payment->expense->due_amount + $payment->amount;
            $payment->expense->save();
        }

        $ledger = $payment->ledger;

        if ($ledger && $ledger->id) {
            $otherPaymentsCount = ExpenseSupplierPayment::where('ledger_id', $ledger->id)
                ->where('id', '!=', $id)
                ->count();

            if ($otherPaymentsCount == 0) {
                $ledger->details()->delete();
                $ledger->delete();
            } else {
                $ledger->details()->where('invoice', 'EXP-' . $payment->expense_id)->delete();
                $ledger->amount = $ledger->amount - $payment->amount;
                $ledger->due_amount = $ledger->due_amount + $payment->amount;
                $ledger->save();
            }
        }

        return $payment->delete();
    }

    public function genInvoiceNumber()
    {
        $number = 001;
        $prefix = 'ESP-';
        $invoice_number = $prefix . $number;

        $payment = ExpenseSupplierPayment::latest()->first();

        if ($payment) {
            $paymentInvoice = $payment->invoice;

            if ($paymentInvoice) {
                $split_invoice = explode('-', $paymentInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }

    public function advancePay(Request $request, $id)
    {
        $account = $request->account_id;

        $ledger = new Ledger();
        $ledger->expense_supplier_id = $id;
        $ledger->amount = $request->paying_amount ?? $request->refund_amount;
        $ledger->invoice_type = $request->refund_amount == null ? 'Expense Advance Payment' : 'Expense Payment Return';
        $ledger->is_paid = $request->refund_amount != null ? 0 : 1;
        $ledger->is_received = $request->refund_amount != null ? 1 : 0;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber();
        $ledger->note = $request->note;

        if ($request->refund_amount != null) {
            $ledger->due_amount += $request->refund_amount;
            $ledger->amount = -$request->refund_amount;
        } else {
            $ledger->due_amount = -$request->paying_amount;
            $ledger->amount = $request->paying_amount;
        }
        $ledger->date = now()->parse($request->date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();
        $ledger->invoice_url = route('admin.expense-suppliers.ledger-details', $ledger->id);
        $ledger->save();

        $ledger->details()->create([
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount
        ]);

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::firstOrCreate(
                ['account_type' => 'cash'],
                ['bank_account_name' => 'Cash Register']
            );
        } else {
            $account = Account::find($account);
        }

        ExpenseSupplierPayment::create([
            'expense_supplier_id' => $id,
            'account_id' => $account->id,
            'payment_type' => $request->refund_amount != null ? 'advance_refund' : 'advance_pay',
            'is_paid' => $request->refund_amount != null ? 0 : 1,
            'is_received' => $request->refund_amount != null ? 1 : 0,
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount,
            'account_type' => accountList()[$account->account_type],
            'note' => $request->note,
            'memo' => $request->memo,
            'created_by' => auth('admin')->user()->id,
            'payment_date' => now()->parse($request->date),
            'invoice' => $this->genInvoiceNumber(),
            'ledger_id' => $ledger->id
        ]);
    }

    public function genLedgerInvoiceNumber()
    {
        $number = 001;
        $prefix = 'ESPL-';
        $invoice_number = $prefix . $number;

        $ledger = Ledger::where('invoice_type', 'Expense Due Payment')->latest()->first();
        if ($ledger) {
            $ledgerInvoice = $ledger->invoice_no;

            if ($ledgerInvoice) {
                $split_invoice = explode('-', $ledgerInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }
}
