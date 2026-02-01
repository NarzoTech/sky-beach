@extends('admin.layouts.master')
@section('title', __('Expenses List'))


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="payment_status" id="payment_status" class="form-control">
                                        <option value="">{{ __('Payment Status') }}</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>
                                            {{ __('Paid') }}</option>
                                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>
                                            {{ __('Partial Paid') }}</option>
                                        <option value="due" {{ request('payment_status') == 'due' ? 'selected' : '' }}>
                                            {{ __('Due') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="expense_supplier_id" id="expense_supplier_id" class="form-control">
                                        <option value="">{{ __('All Suppliers') }}</option>
                                        @foreach ($expenseSuppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ request('expense_supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="order_type" id="order_type" class="form-control">
                                        <option value="id" {{ request('order_type') == 'id' ? 'selected' : '' }}>
                                            {{ __('Serial') }}</option>

                                        <option value="date" {{ request('order_type') == 'date' ? 'selected' : '' }}>
                                            {{ __('Date') }}</option>

                                        <option value="amount" {{ request('order_type') == 'amount' ? 'selected' : '' }}>
                                            {{ __('Amount') }}</option>

                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="order_by" id="order_by" class="form-control">
                                        <option value="">{{ __('Order By') }}</option>
                                        <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                            {{ __('ASC') }}
                                        </option>
                                        <option value="desc" {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                            {{ __('DESC') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select name="par-page" id="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('10') }}
                                        </option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('50') }}
                                        </option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('100') }}
                                        </option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('All') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-label-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-5">
                <div class="card-header">
                    <div class="card-header-title">
                        <h4 class="section_title"> Expenses List</h4>
                    </div>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        @adminCan('expense.create')
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addExpense"
                                class="btn btn-primary"><i class="fa fa-plus"></i>
                                {{ __('Add Expense') }}</a>
                        @endadminCan
                        @adminCan('expense.excel.download')
                            <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                                Excel</button>
                        @endadminCan
                        @adminCan('expense.pdf.download')
                            <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                                PDF</button>
                        @endadminCan
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table">
                            <thead>
                                <tr>
                                    <th style="width: 4%">{{ __('Sl') }}</th>
                                    <th style="width: 8%">{{ __('Invoice') }}</th>
                                    <th style="width: 8%">{{ __('Date') }}</th>
                                    <th style="width: 10%">{{ __('Supplier') }}</th>
                                    <th style="width: 10%">{{ __('Type') }}</th>
                                    <th style="width: 8%">{{ __('Amount') }}</th>
                                    <th style="width: 8%">{{ __('Paid') }}</th>
                                    <th style="width: 8%">{{ __('Due') }}</th>
                                    <th style="width: 6%">{{ __('Status') }}</th>
                                    <th style="width: 10%">{{ __('Note') }}</th>
                                    <th style="width: 10%">{{ __('Memo') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $start =
                                        $expenses instanceof \Illuminate\Pagination\LengthAwarePaginator
                                            ? $expenses->firstItem()
                                            : 1;
                                @endphp
                                @forelse ($expenses as $index => $expense)
                                    <tr>
                                        <td>{{ $start + $index }}</td>
                                        <td>{{ $expense->invoice ?? '-' }}</td>
                                        <td>{{ formatDate($expense->date) }}</td>
                                        <td>{{ $expense->expenseSupplier->name ?? '-' }}</td>
                                        <td>{{ $expense->expenseType->name }}</td>
                                        <td>{{ currency($expense->amount) }}</td>
                                        <td>{{ currency($expense->paid_amount) }}</td>
                                        <td>{{ currency($expense->due_amount) }}</td>
                                        <td>
                                            @php $status = $expense->payment_status_label; @endphp
                                            @if($status == 'paid')
                                                <span class="badge bg-success">{{ __('Paid') }}</span>
                                            @elseif($status == 'partial')
                                                <span class="badge bg-warning">{{ __('Partial') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Due') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $expense->note }}</td>
                                        <td>{{ $expense->memo }}</td>
                                        <td>
                                            @if (checkAdminHasPermission('expense.edit') || checkAdminHasPermission('expense.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $expense->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true"
                                                        aria-expanded="false">{{ __('Action') }}</button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $expense->id }}">
                                                        @adminCan('expense.edit')
                                                            <a class="dropdown-item" href="javascript:;"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editExpense{{ $expense->id }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('expense.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $expense->id }})">{{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-table :name="__('Expense')" route="" create="no" :message="__('No data found!')"
                                        colspan="12"></x-empty-table>
                                @endforelse

                                @if ($expenses->count() > 0)
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <b>{{ __('Total') }}</b>
                                        </td>
                                        <td>
                                            <b>{{ currency($totalAmount) }}</b>
                                        </td>
                                        <td>
                                            <b>{{ currency($totalPaid) }}</b>
                                        </td>
                                        <td>
                                            <b>{{ currency($totalDue) }}</b>
                                        </td>
                                        <td colspan="4"></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @if (request()->get('par-page') !== 'all')
                        <div class="float-right">
                            {{ $expenses->onEachSide(0)->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- add Expense type --}}
    <div class="modal fade" id="addExpense">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Expense') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body py-0">
                    <form action="{{ route('admin.expense.store') }}" method="POST" id="add-bank-form" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">{{ __('Date') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control datepicker" id="date" name="date"
                                        value="{{ formatDate(now()) }}" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expense_supplier_id_add">{{ __('Expense Supplier') }}</label>
                                    <select name="expense_supplier_id" id="expense_supplier_id_add" class="form-control select2"
                                        data-dropdown-parent="#addExpense">
                                        <option value="">{{ __('Select Supplier (Optional)') }}</option>
                                        @foreach ($expenseSuppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expense_type_id">{{ __('Expense Type') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="expense_type_id" id="expense_type_id" class="form-control select2"
                                        data-dropdown-parent="#addExpense" required>
                                        <option value="">{{ __('Select Expense Type') }}</option>
                                        @foreach ($types as $type)
                                            @if ($type->parent_id)
                                                @continue
                                            @endif
                                            <option value="{{ $type->id }}"
                                                data-has-children="{{ $type->children->count() > 0 ? '1' : '0' }}">
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 sub-expense-wrapper" style="display: none;">
                                <div class="form-group">
                                    <label for="sub_expense_type_id">{{ __('Sub Expense Type') }}</label>
                                    <select name="sub_expense_type_id" id="sub_expense_type_id"
                                        class="form-control select2" data-dropdown-parent="#addExpense">
                                        <option value="">{{ __('Select Sub Expense Type') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount">{{ __('Total Amount') }}<span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="total_amount" name="amount"
                                        value="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="paid_amount_display">{{ __('Paid Amount') }}</label>
                                    <input type="number" step="0.01" class="form-control" id="paid_amount_display"
                                        value="0" readonly>
                                    <small class="text-muted">{{ __('Auto-calculated from payments below') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="due_amount_display">{{ __('Due Amount') }}</label>
                                    <input type="number" step="0.01" class="form-control" id="due_amount_display"
                                        value="0" readonly>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>{{ __('Payment Details') }}<span class="text-danger">*</span></label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered expense-payment-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%">{{ __('Payment Type') }}</th>
                                                    <th style="width: 30%">{{ __('Account') }}</th>
                                                    <th style="width: 30%">{{ __('Amount') }}</th>
                                                    <th style="width: 10%">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="expensePaymentRows">
                                                @include('expense::partials.payment-row', ['counter' => 1])
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="note">{{ __('Note') }}</label>
                                    <textarea name="note" id="note" cols="30" rows="2" class="form-control" placeholder="{{ __('Enter note (optional)') }}"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="memo">{{ __('Memo') }}</label>
                                    <textarea name="memo" id="memo" cols="30" rows="2" class="form-control" placeholder="{{ __('Enter memo (optional)') }}"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="document">{{ __('Document') }}</label>
                                    <input type="file" class="form-control" id="document" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="text-muted">{{ __('Accepted: PDF, JPG, PNG, DOC, DOCX') }}</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary" form="add-bank-form">{{ __('Save') }}</button>
                </div>

            </div>
        </div>
    </div>

    {{-- edit expense --}}
    @foreach ($expenses as $index => $expense)
        <div class="modal fade" id="editExpense{{ $expense->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Expense') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.expense.update', $expense->id) }}" method="POST"
                            id="edit-type-form{{ $expense->id }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_edit_{{ $expense->id }}">{{ __('Date') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control datepicker" id="date_edit_{{ $expense->id }}"
                                            name="date" value="{{ formatDate($expense->date) }}"
                                            autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expense_supplier_id_edit_{{ $expense->id }}">{{ __('Expense Supplier') }}</label>
                                        <select name="expense_supplier_id" id="expense_supplier_id_edit_{{ $expense->id }}" class="form-control select2"
                                            data-dropdown-parent="#editExpense{{ $expense->id }}">
                                            <option value="">{{ __('Select Supplier (Optional)') }}</option>
                                            @foreach ($expenseSuppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ $expense->expense_supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label
                                            for="expense_type_id_edit_{{ $expense->id }}">{{ __('Expense Type') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="expense_type_id" id="expense_type_id_edit_{{ $expense->id }}"
                                            class="form-control select2 expense-type-edit"
                                            data-dropdown-parent="#editExpense{{ $expense->id }}"
                                            data-expense-id="{{ $expense->id }}" required>
                                            <option value="">{{ __('Select Expense Type') }}</option>
                                            @foreach ($types as $type)
                                                @if ($type->parent_id)
                                                    @continue
                                                @endif
                                                <option value="{{ $type->id }}"
                                                    data-has-children="{{ $type->children->count() > 0 ? '1' : '0' }}"
                                                    {{ $type->id == $expense->expense_type_id ? 'selected' : '' }}>
                                                    {{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 sub-expense-wrapper-{{ $expense->id }}"
                                    style="display: {{ $expense->expenseType->children->count() > 0 ? 'block' : 'none' }};">
                                    <div class="form-group">
                                        <label
                                            for="sub_expense_type_id_edit_{{ $expense->id }}">{{ __('Sub Expense Type') }}</label>
                                        <select name="sub_expense_type_id"
                                            id="sub_expense_type_id_edit_{{ $expense->id }}"
                                            class="form-control select2"
                                            data-dropdown-parent="#editExpense{{ $expense->id }}">
                                            <option value="">{{ __('Select Sub Expense Type') }}</option>
                                            @foreach ($expense->expenseType->children as $child)
                                                <option value="{{ $child->id }}"
                                                    {{ $expense->sub_expense_type_id == $child->id ? 'selected' : '' }}>
                                                    {{ $child->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount_edit_{{ $expense->id }}">{{ __('Total Amount') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control edit-total-amount" id="amount_edit_{{ $expense->id }}" name="amount"
                                            value="{{ $expense->amount }}" data-expense-id="{{ $expense->id }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="paid_amount_edit_{{ $expense->id }}">{{ __('Paid Amount') }}</label>
                                        <input type="number" step="0.01" class="form-control" id="paid_amount_edit_{{ $expense->id }}"
                                            value="{{ $expense->paid_amount }}" readonly>
                                        <small class="text-muted">{{ __('Auto-calculated from payments below') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="due_amount_edit_{{ $expense->id }}">{{ __('Due Amount') }}</label>
                                        <input type="number" step="0.01" class="form-control" id="due_amount_edit_{{ $expense->id }}"
                                            value="{{ $expense->due_amount }}" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>{{ __('Payment Details') }}</label>
                                        <div class="table-responsive">
                                            <table class="table table-bordered expense-payment-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 30%">{{ __('Payment Type') }}</th>
                                                        <th style="width: 30%">{{ __('Account') }}</th>
                                                        <th style="width: 30%">{{ __('Amount') }}</th>
                                                        <th style="width: 10%">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="expensePaymentRowsEdit{{ $expense->id }}">
                                                    @php
                                                        $existingPayments = $expense->payments()->whereIn('payment_type', ['expense', 'direct_expense'])->get();
                                                    @endphp
                                                    @if($existingPayments->count() > 0)
                                                        @foreach($existingPayments as $pIndex => $payment)
                                                            <tr class="payment-row-edit" data-counter="{{ $pIndex + 1 }}" data-expense-id="{{ $expense->id }}">
                                                                <td>
                                                                    <select name="payment_type[]" class="form-control expense-pay-type-edit" data-expense-id="{{ $expense->id }}" required>
                                                                        <option value="">{{ __('Select') }}</option>
                                                                        @foreach (accountList() as $key => $list)
                                                                            @php
                                                                                $paymentAccountType = $payment->account ? $payment->account->account_type : 'cash';
                                                                            @endphp
                                                                            <option value="{{ $key }}" @if ($key == $paymentAccountType) selected @endif>{{ $list }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td class="expense-account-info-edit">
                                                                    @if($payment->account && !in_array($payment->account->account_type, ['cash', 'advance']))
                                                                        <select name="account_id[]" class="form-control" required>
                                                                            <option value="">{{ __('Select') }}</option>
                                                                            @foreach ($accounts->where('account_type', $payment->account->account_type) as $account)
                                                                                @if ($payment->account->account_type == 'bank')
                                                                                    <option value="{{ $account->id }}" {{ $payment->account_id == $account->id ? 'selected' : '' }}>
                                                                                        {{ $account->bank_account_number }} ({{ $account->bank->name ?? 'N/A' }})
                                                                                    </option>
                                                                                @elseif($payment->account->account_type == 'mobile_banking')
                                                                                    <option value="{{ $account->id }}" {{ $payment->account_id == $account->id ? 'selected' : '' }}>
                                                                                        {{ $account->mobile_number }} ({{ $account->mobile_bank_name }})
                                                                                    </option>
                                                                                @elseif($payment->account->account_type == 'card')
                                                                                    <option value="{{ $account->id }}" {{ $payment->account_id == $account->id ? 'selected' : '' }}>
                                                                                        {{ $account->card_number }} ({{ $account->bank->name ?? 'N/A' }})
                                                                                    </option>
                                                                                @endif
                                                                            @endforeach
                                                                        </select>
                                                                    @else
                                                                        <input type="hidden" name="account_id[]" value="cash">
                                                                        <span class="text-muted">Cash</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" name="paying_amount[]" class="form-control expense-paying-amount-edit" data-expense-id="{{ $expense->id }}" placeholder="{{ __('Amount') }}" value="{{ $payment->amount }}" required>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group btn-group-sm">
                                                                        @if($pIndex == 0)
                                                                            <a href="javascript:;" class="btn btn-sm btn-primary add-expense-payment-edit" data-expense-id="{{ $expense->id }}">
                                                                                <i class="fa fa-plus"></i>
                                                                            </a>
                                                                        @else
                                                                            <a href="javascript:;" class="btn btn-sm btn-danger remove-expense-payment-edit" data-expense-id="{{ $expense->id }}">
                                                                                <i class="fa fa-trash"></i>
                                                                            </a>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr class="payment-row-edit" data-counter="1" data-expense-id="{{ $expense->id }}">
                                                            <td>
                                                                <select name="payment_type[]" class="form-control expense-pay-type-edit" data-expense-id="{{ $expense->id }}" required>
                                                                    <option value="">{{ __('Select') }}</option>
                                                                    @foreach (accountList() as $key => $list)
                                                                        <option value="{{ $key }}" @if ($key == $expense->payment_type) selected @endif>{{ $list }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="expense-account-info-edit">
                                                                @if($expense->payment_type == 'cash' || $expense->payment_type == 'advance' || !$expense->account_id)
                                                                    <input type="hidden" name="account_id[]" value="{{ $expense->payment_type }}">
                                                                    <span class="text-muted">{{ ucfirst($expense->payment_type) }}</span>
                                                                @else
                                                                    <select name="account_id[]" class="form-control" required>
                                                                        <option value="">{{ __('Select') }}</option>
                                                                        @foreach ($accounts->where('account_type', $expense->payment_type) as $account)
                                                                            @if ($expense->payment_type == 'bank')
                                                                                <option value="{{ $account->id }}" {{ $expense->account_id == $account->id ? 'selected' : '' }}>
                                                                                    {{ $account->bank_account_number }} ({{ $account->bank->name ?? 'N/A' }})
                                                                                </option>
                                                                            @elseif($expense->payment_type == 'mobile_banking')
                                                                                <option value="{{ $account->id }}" {{ $expense->account_id == $account->id ? 'selected' : '' }}>
                                                                                    {{ $account->mobile_number }} ({{ $account->mobile_bank_name }})
                                                                                </option>
                                                                            @elseif($expense->payment_type == 'card')
                                                                                <option value="{{ $account->id }}" {{ $expense->account_id == $account->id ? 'selected' : '' }}>
                                                                                    {{ $account->card_number }} ({{ $account->bank->name ?? 'N/A' }})
                                                                                </option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" name="paying_amount[]" class="form-control expense-paying-amount-edit" data-expense-id="{{ $expense->id }}" placeholder="{{ __('Amount') }}" value="{{ $expense->paid_amount }}" required>
                                                            </td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="javascript:;" class="btn btn-sm btn-primary add-expense-payment-edit" data-expense-id="{{ $expense->id }}">
                                                                        <i class="fa fa-plus"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="note_edit_{{ $expense->id }}">{{ __('Note') }}</label>
                                        <textarea name="note" id="note_edit_{{ $expense->id }}" cols="30" rows="2" class="form-control" placeholder="{{ __('Enter note (optional)') }}">{{ $expense->note }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="memo_edit_{{ $expense->id }}">{{ __('Memo') }}</label>
                                        <textarea name="memo" id="memo_edit_{{ $expense->id }}" cols="30" rows="2" class="form-control" placeholder="{{ __('Enter memo (optional)') }}">{{ $expense->memo }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="document_{{ $expense->id }}">{{ __('Document') }}</label>
                                        <input type="file" class="form-control" id="document_{{ $expense->id }}" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        <small class="text-muted">{{ __('Accepted: PDF, JPG, PNG, DOC, DOCX') }}</small>
                                        @if($expense->document)
                                            <div class="mt-2">
                                                <a href="{{ asset($expense->document) }}" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i> {{ __('View Current Document') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-type-form{{ $expense->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
    @push('js')
        <script>
            $(document).ready(function() {
                $('#addExpense').on('shown.bs.modal', function() {
                    $(this).find('select[name="payment_type"]').select2({
                        dropdownParent: $('#addExpense')
                    });
                });

                @foreach ($expenses as $expense)
                    $('#editExpense{{ $expense->id }}').on('shown.bs.modal', function() {
                        $(this).find('select[name="payment_type"]').select2({
                            dropdownParent: $('#editExpense{{ $expense->id }}')
                        });
                    });
                @endforeach
                const reqType = '{{ request()->type }}';
                if (reqType) {
                    $('#addExpense').modal('show');
                }

                let accounts = @json($accounts);
                let expenseTypes = @json($types);

                // Handle expense type change for ADD modal
                $('#expense_type_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const hasChildren = selectedOption.data('has-children');
                    const expenseTypeId = $(this).val();

                    if (hasChildren == '1' && expenseTypeId) {
                        loadSubExpenseTypes(expenseTypeId, '#sub_expense_type_id', '#addExpense');
                        $('.sub-expense-wrapper').slideDown();
                    } else {
                        $('.sub-expense-wrapper').slideUp();
                        $('#sub_expense_type_id').val('');
                    }
                });

                // Handle expense type change for EDIT modals
                $('.expense-type-edit').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const hasChildren = selectedOption.data('has-children');
                    const expenseTypeId = $(this).val();
                    const expenseId = $(this).data('expense-id');
                    const subExpenseSelect = `#sub_expense_type_id_edit_${expenseId}`;
                    const parentModal = `#editExpense${expenseId}`;

                    if (hasChildren == '1' && expenseTypeId) {
                        loadSubExpenseTypes(expenseTypeId, subExpenseSelect, parentModal);
                        $(`.sub-expense-wrapper-${expenseId}`).slideDown();
                    } else {
                        $(`.sub-expense-wrapper-${expenseId}`).slideUp();
                        $(subExpenseSelect).val('');
                    }
                });

                // Function to load sub expense types via AJAX
                function loadSubExpenseTypes(parentId, selectElement, parentModal) {
                    // Filter children from expenseTypes
                    const children = expenseTypes.filter(type => type.parent_id == parentId);

                    let options = '<option value="">{{ __('Select Sub Expense Type') }}</option>';
                    children.forEach(child => {
                        options += `<option value="${child.id}">${child.name}</option>`;
                    });

                    $(selectElement).html(options);

                    // Reinitialize select2 if it's being used
                    if ($(selectElement).hasClass('select2')) {
                        $(selectElement).select2({
                            dropdownParent: $(parentModal)
                        });
                    }
                }

                // Multiple payment handling
                let paymentCounter = 1;

                // Add payment row
                $(document).on('click', '.add-expense-payment', function() {
                    paymentCounter++;
                    const newRow = `
                        <tr class="payment-row" data-counter="${paymentCounter}">
                            <td>
                                <select name="payment_type[]" class="form-control expense-pay-type" required>
                                    <option value="">{{ __('Select') }}</option>
                                    @foreach (accountList() as $key => $list)
                                        <option value="{{ $key }}">{{ $list }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="expense-account-info">
                                <input type="hidden" name="account_id[]" value="">
                                <span class="text-muted">-</span>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="paying_amount[]" class="form-control expense-paying-amount" placeholder="{{ __('Amount') }}" required>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="javascript:;" class="btn btn-sm btn-danger remove-expense-payment">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                    $('#expensePaymentRows').append(newRow);
                });

                // Remove payment row
                $(document).on('click', '.remove-expense-payment', function() {
                    $(this).closest('tr').remove();
                    calculateTotalPaid();
                });

                // Handle payment type change for expense payments
                $(document).on('change', '.expense-pay-type', function() {
                    const paymentType = $(this).val();
                    const row = $(this).closest('tr');
                    const accountInfoTd = row.find('.expense-account-info');

                    if (paymentType == '' || paymentType == 'cash' || paymentType == 'advance') {
                        const displayText = paymentType == 'cash' ? 'Cash' : (paymentType == 'advance' ? 'Advance' : '-');
                        accountInfoTd.html(`
                            <input type="hidden" name="account_id[]" value="${paymentType}">
                            <span class="text-muted">${displayText}</span>
                        `);
                    } else {
                        const filterAccount = accounts.filter(account => account.account_type === paymentType);
                        let selectHtml = `<select name="account_id[]" class="form-control" required><option value="">{{ __('Select') }}</option>`;

                        filterAccount.forEach(account => {
                            let optionText = '';
                            if (paymentType == 'bank') {
                                optionText = `${account.bank_account_number} (${account.bank?.name || 'N/A'})`;
                            } else if (paymentType == 'mobile_banking') {
                                optionText = `${account.mobile_number} (${account.mobile_bank_name})`;
                            } else if (paymentType == 'card') {
                                optionText = `${account.card_number} (${account.bank?.name || 'N/A'})`;
                            }
                            selectHtml += `<option value="${account.id}">${optionText}</option>`;
                        });

                        selectHtml += `</select>`;
                        accountInfoTd.html(selectHtml);
                    }
                });

                // Calculate paid amount from payments (for add modal)
                $(document).on('input', '.expense-paying-amount', function() {
                    calculatePaidAndDue();
                });

                // Calculate due when total amount changes (for add modal)
                $('#total_amount').on('input', function() {
                    calculatePaidAndDue();
                });

                function calculatePaidAndDue() {
                    let paid = 0;
                    $('.expense-paying-amount').each(function() {
                        const val = parseFloat($(this).val()) || 0;
                        paid += val;
                    });
                    const total = parseFloat($('#total_amount').val()) || 0;
                    const due = total - paid;
                    $('#paid_amount_display').val(paid.toFixed(2));
                    $('#due_amount_display').val(due >= 0 ? due.toFixed(2) : '0.00');
                }

                // Edit modal - Add payment row
                $(document).on('click', '.add-expense-payment-edit', function() {
                    const expenseId = $(this).data('expense-id');
                    const tbody = $(`#expensePaymentRowsEdit${expenseId}`);
                    const counter = tbody.find('tr').length + 1;

                    const newRow = `
                        <tr class="payment-row-edit" data-counter="${counter}" data-expense-id="${expenseId}">
                            <td>
                                <select name="payment_type[]" class="form-control expense-pay-type-edit" data-expense-id="${expenseId}" required>
                                    <option value="">{{ __('Select') }}</option>
                                    @foreach (accountList() as $key => $list)
                                        <option value="{{ $key }}">{{ $list }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="expense-account-info-edit">
                                <input type="hidden" name="account_id[]" value="">
                                <span class="text-muted">-</span>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="paying_amount[]" class="form-control expense-paying-amount-edit" data-expense-id="${expenseId}" placeholder="{{ __('Amount') }}" required>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="javascript:;" class="btn btn-sm btn-danger remove-expense-payment-edit" data-expense-id="${expenseId}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(newRow);
                });

                // Edit modal - Remove payment row
                $(document).on('click', '.remove-expense-payment-edit', function() {
                    const expenseId = $(this).data('expense-id');
                    $(this).closest('tr').remove();
                    calculatePaidAndDueEdit(expenseId);
                });

                // Edit modal - Handle payment type change
                $(document).on('change', '.expense-pay-type-edit', function() {
                    const paymentType = $(this).val();
                    const row = $(this).closest('tr');
                    const accountInfoTd = row.find('.expense-account-info-edit');

                    if (paymentType == '' || paymentType == 'cash' || paymentType == 'advance') {
                        const displayText = paymentType == 'cash' ? 'Cash' : (paymentType == 'advance' ? 'Advance' : '-');
                        accountInfoTd.html(`
                            <input type="hidden" name="account_id[]" value="${paymentType}">
                            <span class="text-muted">${displayText}</span>
                        `);
                    } else {
                        const filterAccount = accounts.filter(account => account.account_type === paymentType);
                        let selectHtml = `<select name="account_id[]" class="form-control" required><option value="">{{ __('Select') }}</option>`;

                        filterAccount.forEach(account => {
                            let optionText = '';
                            if (paymentType == 'bank') {
                                optionText = `${account.bank_account_number} (${account.bank?.name || 'N/A'})`;
                            } else if (paymentType == 'mobile_banking') {
                                optionText = `${account.mobile_number} (${account.mobile_bank_name})`;
                            } else if (paymentType == 'card') {
                                optionText = `${account.card_number} (${account.bank?.name || 'N/A'})`;
                            }
                            selectHtml += `<option value="${account.id}">${optionText}</option>`;
                        });

                        selectHtml += `</select>`;
                        accountInfoTd.html(selectHtml);
                    }
                });

                // Edit modal - Calculate paid and due amounts
                $(document).on('input', '.expense-paying-amount-edit', function() {
                    const expenseId = $(this).data('expense-id');
                    calculatePaidAndDueEdit(expenseId);
                });

                // Edit modal - Calculate due when total amount changes
                $(document).on('input', '.edit-total-amount', function() {
                    const expenseId = $(this).data('expense-id');
                    calculatePaidAndDueEdit(expenseId);
                });

                function calculatePaidAndDueEdit(expenseId) {
                    let paid = 0;
                    $(`.expense-paying-amount-edit[data-expense-id="${expenseId}"]`).each(function() {
                        const val = parseFloat($(this).val()) || 0;
                        paid += val;
                    });
                    const total = parseFloat($(`#amount_edit_${expenseId}`).val()) || 0;
                    const due = total - paid;
                    $(`#paid_amount_edit_${expenseId}`).val(paid.toFixed(2));
                    $(`#due_amount_edit_${expenseId}`).val(due >= 0 ? due.toFixed(2) : '0.00');
                }
            });

            function deleteData(id) {
                let url = "{{ route('admin.expense.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
