@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Daily Sales Report') }}</title>
@endsection


@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 form-group search-wrapper">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="Search">
                                            <button type="submit">
                                                <i class="far fa-arrow-alt-circle-right"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="order_by" id="order_by" class="form-control">
                                                <option value="">{{ __('Order By') }}</option>
                                                <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                                    {{ __('ASC') }}
                                                </option>
                                                <option value="desc"
                                                    {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                                    {{ __('DESC') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="par-page" id="par-page" class="form-control">
                                                <option value="">{{ __('Per Page') }}</option>
                                                <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('10') }}
                                                </option>
                                                <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('50') }}
                                                </option>
                                                <option value="100"
                                                    {{ '100' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('100') }}
                                                </option>
                                                <option value="all"
                                                    {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('All') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="From Date" name="from_date"
                                                value="{{ request()->get('from_date') }}" class="form-control datepicker"
                                                autocomplete="off">
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <input type="text" placeholder="To Date" name="to_date"
                                                value="{{ request()->get('to_date') }}" class="form-control datepicker"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    {{-- excel  buttons --}}
                                    <div class="row">
                                        <div class="col-md-4 form-group mx-auto">
                                            <div class="btn-group" role="group" aria-label="Basic example">
                                                <button type="button" class="btn btn-secondary export"><i
                                                        class="far fa-file-excel"></i>
                                                    Excel</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    Daily Sales Report
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive table-invoice">
                                    <table class="table table-striped">
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
                                            @foreach ($sales as $sale)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
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
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
