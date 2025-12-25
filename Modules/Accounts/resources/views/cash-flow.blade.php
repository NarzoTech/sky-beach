@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Cash Flow') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xl-9 col-lg-6">
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
                            <div class="col-xl-3 col-lg-6">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> Cash Flow
                    @if (!isset($hasDateFilter) || !$hasDateFilter)
                        <span class="badge bg-info">{{ __('All Time') }}</span>
                    @else
                        <span class="badge bg-secondary">{{ __('Filtered') }}</span>
                    @endif
                </h4>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3" id="cash-summary-table">
                    <thead>
                        <tr>
                            <td colspan="2" class="text-center bg-primary">
                                <h5 class="mb-0 text-white">Cash In</h5>
                            </td>
                            <td colspan="2" class="text-center bg-warning">
                                <h5 class="mb-0 text-white">Cash Out</h5>
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/1.png') }}" class="icon-img" />
                                Product Sale
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['productSale']) }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/12.png') }}" class="icon-img" />
                                Sale Return
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['sale_return']) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/4.png') }}" class="icon-img" />
                                Balance Deposit
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['balance_deposit']) }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/13.png') }}" class="icon-img" />
                                Balance Withdraw
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['balance_withdraw']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/3.png') }}" class="icon-img" />
                                Customer Due
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['customer_due']) }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/3.png') }}" class="icon-img" />
                                Customer Due Send
                            </td>
                            <td>
                                <span>
                                    TK 0.00
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/15.png') }}" class="icon-img" />
                                Customer Advance
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['customer_advance']) }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/6.png') }}" class="icon-img" />
                                Customer Advance Refund
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['customer_advance_refund']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/10.png') }}" class="icon-img" />
                                Supplier Due Receive
                            </td>
                            <td>
                                <span>
                                    TK 0.00
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/10.png') }}" class="icon-img" />
                                Supplier Due Pay
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['supplierDuePay']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/15.png') }}" class="icon-img" />
                                Supplier Advance Refund
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['supplierAdvanceRefund']) }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/15.png') }}" class="icon-img" />
                                Supplier Advance
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['supplierAdvancePay']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/6.png') }}" class="icon-img" />
                                Purchase Return
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['purchaseReturn'] ?? 0) }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/6.png') }}" class="icon-img" />
                                Purchase
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['purchase']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/7.png') }}" class="icon-img" />
                                Service
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['serviceSale']) }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/11.png') }}" class="icon-img" />
                                Expense
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['expenses']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/1.png') }}" class="icon-img" />
                                Installment
                            </td>
                            <td>
                                <span>
                                    TK 0.00
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/14.png') }}" class="icon-img" />
                                Salary
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['salary']) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/8.png') }}" class="icon-img" />
                                Balance Transfer (In)
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['balance_transfer'] ?? 0) }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/17.png') }}" class="icon-img" />
                                Balance Transfer (Out)
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['balance_transfer'] ?? 0) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/15.png') }}" class="icon-img" />
                                {{ __('Expense Supplier Advance Refund') }}
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['expenseSupplierAdvanceRefund'] ?? 0) }}
                                </span>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/11.png') }}" class="icon-img" />
                                {{ __('Expense Supplier Payment') }}
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['expenseSupplierPayment'] ?? 0) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/10.png') }}" class="icon-img" />
                                {{ __('Expense Supplier Due Pay') }}
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['expenseSupplierDuePay'] ?? 0) }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                                <img src="{{ asset('backend/img/cash-flow/15.png') }}" class="icon-img" />
                                {{ __('Expense Supplier Advance') }}
                            </td>
                            <td>
                                <span>
                                    {{ currency($data['expenseSupplierAdvancePay'] ?? 0) }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td colspan="" class="text-left">
                                <b>
                                    Total : {{ currency($data['totalReceive']) }}
                                </b>
                            </td>
                            <td></td>
                            <td colspan="" class="text-left">
                                <b>
                                    Total : {{ currency($data['totalPay']) }}
                                </b>
                            </td>
                        </tr>
                        @if (isset($hasDateFilter) && $hasDateFilter)
                        <tr>
                            <td colspan="3" class="text-end">
                                <h5 class="m-0">
                                    <b>Opening Balance =</b>
                                </h5>
                            </td>
                            <td colspan="" class="text-left">
                                <h5 class="m-0">
                                    <b>{{ currency($openingBalance) }}</b>
                                </h5>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-end">
                                <h5 class="m-0">
                                    <b>{{ isset($hasDateFilter) && $hasDateFilter ? __('Current Balance') : __('Net Balance') }} =</b>
                                </h5>
                            </td>
                            <td class="text-left">
                                <h5 class="m-0">
                                    <b>{{ currency($currentBalance) }}</b>
                                </h5>
                                @if (isset($hasDateFilter) && $hasDateFilter)
                                ({{ __('Opening Balance') }} + {{ __('Cash In') }} - {{ __('Cash Out') }})
                                @else
                                ({{ __('Cash In') }} - {{ __('Cash Out') }})
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection
