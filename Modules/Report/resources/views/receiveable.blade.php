@extends('admin.layouts.master')
@section('title', __('Due Report'))


@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="" method="GET">
                <div class="row">
                    <div class="col-xxl-3 col-md-4">
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
                    <div class="col-xxl-3 col-md-4">
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
            <h4 class="section_title">{{ __('Due Report') }}</h4>
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
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice No') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Invoice') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $start = checkPaginate($sales) ? $sales->firstItem() : 1;
                        @endphp
                        @foreach ($sales as $key => $sale)
                            @if ($sale->due_amount == 0)
                                @continue
                            @endif
                            <tr>
                                <td>{{ $start + $key }}</td>
                                <td>{{ formatDate($sale->order_date) }}</td>
                                <td>{{ $sale->invoice }}</td>
                                <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                                <td>{{ currency($sale->due_amount) }}</td>
                                <td>
                                    <a class="btn btn-primary"
                                        href="{{ route('admin.sales.invoice', $sale->id) }}">{{ __('Invoice') }}</a>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-end"><b>{{ __('Total') }}</b></td>
                            <td colspan="2"><b>{{ currency($totalDues) }}</b></td>
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
