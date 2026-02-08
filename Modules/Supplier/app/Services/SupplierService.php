<?php

namespace Modules\Supplier\app\Services;

use App\Imports\SuppliersImport;
use App\Models\Ledger;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Accounts\app\Models\Account;
use Modules\Purchase\app\Models\Purchase;
use Modules\Supplier\app\Models\Supplier;
use Modules\Supplier\app\Models\SupplierPayment;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    public function __construct(private Supplier $supplier) {}

    public function all()
    {
        return $this->supplier->where('status', 1)->with('purchaseReturn');
    }

    public function allSupplier()
    {
        $suppliers = $this->supplier->query();
        $suppliers = $suppliers->with(['purchaseReturn', 'purchases' => function ($query) {
            if (request()->from_date || request()->to_date) {
                [$from_date, $to_date] = $this->getDateRangeFromRequest();
                if ($from_date) {
                    $query->where('purchase_date', '>=', $from_date);
                }
                if ($to_date) {
                    $query->where('purchase_date', '<=', $to_date);
                }
            }
        }, 'payments' => function ($query) {
            // Include paid payments AND advance refunds (which have is_paid = 0)
            $query->where(function($q) {
                $q->where('is_paid', 1)
                  ->orWhere('payment_type', 'advance_refund');
            });

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
                    })
                ;
            });
        }

        if (request()->order_by) {
            $suppliers = $suppliers->orderBy('company', request()->order_by);
        } else {
            $suppliers = $suppliers->orderBy('company', 'asc');
        }

        if (request()->from_date && request()->to_date) {

            $suppliers = $suppliers->whereBetween('date', [now()->parse(request()->from_date), now()->parse(request()->to_date)]);
        }

        if (request()->order_type) {
            $orderBy = request()->order_by;
            $orderBy = $orderBy == 'asc' ? 'sortBy' : 'sortByDesc';
            switch (request()->order_type) {
                case 'due':
                    $suppliers = $suppliers->with(['purchases', 'payments', 'purchaseReturn'])
                        ->where(function ($q) {
                            // check if supplier has due
                            $q->whereHas('purchases', function ($query) {
                                $query->where('due_amount', '>', 0);
                            });
                        })
                        ->get()
                        ->$orderBy(function ($supplier) {
                            $totalPurchase = $supplier->purchases->sum('total_amount');
                            $totalPaid = $supplier->payments->sum('amount');
                            $totalReturn = $supplier->purchaseReturn->sum('return_amount');
                            $totalDue = $totalPurchase - $totalPaid - $totalReturn;
                            return $totalDue;
                        });
                    break;

                case 'paid':
                    $suppliers = $suppliers->with(['payments', 'purchases'])
                        ->whereHas('purchases')
                        ->get();
                    $suppliers = $suppliers->filter(function ($supplier) {
                        return $supplier->purchases->sum('total_amount') == $supplier->payments->sum('amount');
                    });

                    $suppliers = $suppliers->$orderBy(function ($supplier) {
                        return $supplier->payments->sum('amount');
                    });
                    break;

                case 'total':
                    $suppliers = $suppliers->with(['purchases'])

                        ->get()
                        ->$orderBy(function ($supplier) {
                            return $supplier->purchases->sum('total_amount');
                        });
                    break;

                default:
                    // Default sorting logic
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
        $supplier = $this->supplier->with('duePurchase')->find($id);

        return $supplier;
    }

    public function storeSupplier(Request $request)
    {
        $data = $request->except('_token');
        $data['created_by'] = auth()->id();
        $data['date'] = now()->parse($request->date);
        return $this->supplier->create($data);
    }

    public function updateSupplier(Request $request, $id)
    {
        $data = $request->except(['_token', '_method']);
        $data['updated_by'] = auth()->id();
        $data['date'] = now()->parse($request->date);
        return $this->supplier->where('id', $id)->update($data);
    }

    public function deleteSupplier($id)
    {
        return $this->supplier->where('id', $id)->delete();
    }

    public function duePay(Request $request, $id)
    {
        $supplier = $this->supplier->find($id);

        $supplier->balance = $supplier->balance - $request->paying_amount;
        $supplier->save();

        // account information

        $account = $request->account_id;

        if ($account == 'cash' || $account == 'advance') {
            $account = Account::firstOrCreate(
                ['account_type' => 'cash'],
                ['bank_account_name' => 'Cash Register']
            );
        } else {
            $account = Account::find($account);
        }


        // create Ledger
        $ledger = new Ledger();
        $ledger->supplier_id = $id;
        $ledger->amount = $request->paying_amount;
        $ledger->invoice_type = 'Due Payment';
        $ledger->is_paid = 1;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber();
        $ledger->note = $request->note;
        $ledger->due_amount = -$request->paying_amount;
        $ledger->total_amount = 0;
        $ledger->date = now()->parse($request->payment_date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();

        $ledger->invoice_url = route('admin.suppliers.ledger-details', $ledger->id);
        $ledger->save();

        // create payment
        foreach ($request->invoice_no as $index => $invo) {

            if (isset($request->amount[$index]) && $request->amount[$index] == 0) {
                continue;
            }
            $purchase = Purchase::where('invoice_number', $invo)->first();

            $purchase->paid_amount = $purchase->paid_amount + $request->amount[$index];
            $purchase->due_amount = $purchase->due_amount - $request->amount[$index];
            $purchase->payment_status = $purchase->due_amount == 0 ? 'paid' : 'due';
            $purchase->save();

            // create payment data
            SupplierPayment::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $id,
                'account_id' => $account->id,
                'payment_type' => 'due_pay',
                'is_paid' => 1,
                'amount' => $request->amount[$index],
                'payment_date' => now()->parse($request->payment_date),
                'note' => $request->note,
                'ledger_id' => $ledger->id,
                'created_by' => auth('admin')->user()->id,
            ]);

            // create ledger details
            $ledger->details()->create([
                'invoice' => $invo,
                'amount' => $request->amount[$index],
            ]);
        }
    }

    public function duePayHistory()
    {
        $list = SupplierPayment::query();

        $list = $list->with('purchase', 'supplier', 'createdBy')
            ->whereNotNull('purchase_id')
            ->where('payment_type', 'due_pay');

        // Date filtering
        if (request()->from_date && request()->to_date) {
            $fromDate = \Carbon\Carbon::parse(request()->from_date)->startOfDay();
            $toDate = \Carbon\Carbon::parse(request()->to_date)->endOfDay();
            $list = $list->whereBetween('payment_date', [$fromDate, $toDate]);
        }

        // Keyword search
        if (request()->keyword) {
            $keyword = '%' . request()->keyword . '%';
            $list = $list->where(function ($q) use ($keyword) {
                $q->where('note', 'like', $keyword)
                    ->orWhere('amount', 'like', $keyword)

                    ->orWhereHas('supplier', function ($query) use ($keyword) {
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

    public function dueReceiveDelete($id)
    {
        $payment = SupplierPayment::find($id);

        // Update purchase paid/due amounts
        if ($payment->purchase && $payment->purchase->id) {
            $payment->purchase->paid_amount = $payment->purchase->paid_amount - $payment->amount;
            $payment->purchase->due_amount = $payment->purchase->due_amount + $payment->amount;
            $payment->purchase->payment_status = $payment->purchase->due_amount == 0 ? 'paid' : 'due';
            $payment->purchase->save();
        }

        $ledger = $payment->ledger;

        if ($ledger && $ledger->id) {
            // Check if other payments reference this ledger
            $otherPaymentsCount = SupplierPayment::where('ledger_id', $ledger->id)
                ->where('id', '!=', $id)
                ->count();

            if ($otherPaymentsCount == 0) {
                // No other payments use this ledger, safe to delete
                $ledger->details()->delete();
                $ledger->delete();
            } else {
                // Other payments exist, only delete the specific ledger detail for this payment
                $ledger->details()->where('invoice', $payment->purchase?->invoice_number)->delete();

                // Update ledger amount
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
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = SupplierPayment::latest()->first();

        if ($purchase) {
            $purchaseInvoice = $purchase->invoice;

            if ($purchaseInvoice) {
                // split the invoice number
                $split_invoice = explode('-', $purchaseInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }


    public function advancePay(Request $request, $id)
    {
        $account = $request->account_id;

        // create ledger

        $ledger = new Ledger();
        $ledger->supplier_id = $id;
        $ledger->amount = $request->paying_amount ?? $request->refund_amount;
        $ledger->invoice_type = $request->refund_amount == null ? 'Advance Payment' : 'Payment Return';
        $ledger->is_paid = $request->refund_amount != null ? 0 : 1;
        $ledger->is_received = $request->refund_amount != null ? 1 : 0;
        $ledger->invoice_no = $this->genLedgerInvoiceNumber();
        $ledger->note = $request->note;

        if ($request->refund_amount != null) {
            $ledger->due_amount = $request->refund_amount;
            $ledger->amount = -$request->refund_amount;
        } else {
            $ledger->due_amount = -$request->paying_amount;
            $ledger->amount = $request->paying_amount;
        }
        $ledger->date = now()->parse($request->date);
        $ledger->created_by = auth('admin')->user()->id;
        $ledger->save();
        $ledger->invoice_url = route('admin.suppliers.ledger-details', $ledger->id);
        $ledger->save();

        // create ledger details
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
        // create payment data
        SupplierPayment::create([
            'supplier_id' => $id,
            'account_id' => $account->id,
            'payment_type' => $request->refund_amount != null ? 'advance_refund' : 'advance_pay',
            'is_paid' => $request->refund_amount != null ? 0 : 1,
            'is_received' => $request->refund_amount != null ? 1 : 0,
            'amount' => $request->refund_amount != null ? $request->refund_amount : $request->paying_amount,
            'account_type' => accountList()[$account->account_type],
            'note' => $request->note,
            'created_by' => auth('admin')->user()->id,
            'payment_date' => now()->parse($request->date),
            'invoice' => $this->genInvoiceNumber(),
            'ledger_id' => $ledger->id
        ]);
    }


    public function genLedgerInvoiceNumber()
    {
        $number = 001;
        $prefix = 'INV-';
        $invoice_number = $prefix . $number;

        $purchase = Ledger::where('invoice_type', 'Due Payment')->latest()->first();
        if ($purchase) {
            $purchaseInvoice = $purchase->invoice_no;

            if ($purchaseInvoice) {
                // split the invoice number
                $split_invoice = explode('-', $purchaseInvoice);
                $invoice_number = (int) $split_invoice[1] + 1;
                $invoice_number = $prefix . $invoice_number;
            }
        }

        return $invoice_number;
    }


    public function bulkImport(Request $request)
    {
        $file = $request->file('file');
        Excel::import(new SuppliersImport, $file);
    }
}
