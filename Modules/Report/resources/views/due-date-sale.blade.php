@extends('admin.layouts.master')
@section('title', __('Due Date Report'))


@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="" method="GET">
                <div class="row">
                    <div class="col-xxl-2 col-md-4">
                        <div class="form-group search-wrapper">
                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}" class="form-control"
                                placeholder="Search">
                            <button type="submit">
                                <i class='bx bx-search'></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4">
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
                    <div class="col-xxl-2 col-md-4">
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
                    <div class="col-xxl-2 col-md-4">
                        <div class="form-group">
                            <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                <input type="text" id="dateRangePicker" placeholder="From Date"
                                    class="form-control datepicker" name="from_date"
                                    value="{{ request()->get('from_date') }}" autocomplete="off">
                                <span class="input-group-text">to</span>
                                <input type="text" placeholder="To Date" class="form-control datepicker" name="to_date"
                                    value="{{ request()->get('to_date') }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4">
                        <div class="form-group">
                            <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                            <button type="submit" class="btn bg-label-primary">{{ __('Search') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Due Date Report') }}</h4>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Sale Date') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th>{{ __('Invoice') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Paid') }}</th>
                            <th>{{ __('Paid By') }}</th>
                            <th>{{ __('Due') }}</th>
                            <th>{{ __('Return Amount') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $start = checkPaginate($sales) ? $sales->firstItem() : 1;
                        @endphp
                        @foreach ($sales as $index => $sale)
                            <tr>
                                <td>{{ $index + $start }}</td>
                                <td>{{ formatDate($sale->order_date) }}</td>
                                <td>{{ formatDate($sale->due_date) }}</td>
                                <td>{{ $sale->invoice }}</td>
                                <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                                <td>{{ $sale->customer->phone }}</td>
                                <td>{{ $sale->grand_total }}</td>
                                <td>{{ $sale->paid_amount }}</td>
                                <td>
                                    @foreach ($sale->payment as $payment)
                                        {{ $payment->account->account_type }} :
                                        {{ $payment->amount }}
                                        <br>
                                    @endforeach
                                </td>
                                <td>{{ $sale->due_amount }}</td>
                                <td>{{ $sale->saleReturns->sum('return_amount') }}</td>
                                <td>{{ $sale->due_amount == 0 ? 'Paid' : 'Due' }}</td>
                                <td>
                                    <a class="btn btn-primary"
                                        href="{{ route('admin.sales.invoice', $sale->id) }}">{{ __('Invoice') }}</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="9" class="text-center">
                                <b> {{ __('Total') }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['due']) }}</b>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $sales->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
