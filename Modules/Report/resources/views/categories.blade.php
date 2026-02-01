@extends('admin.layouts.master')
@section('title', __('Category Report'))


@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="" method="GET" class="card-body">
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
            <h4 class="section_title">{{ __('Report List') }}</h4>
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
                            <th>{{ __('Category Name') }}</th>
                            <th>{{ __('Purchase') }}</th>
                            <th>{{ __('Sold') }}</th>
                            <th>{{ __('Purchase Amount') }}</th>
                            <th>{{ __('Sold Amount') }}</th>
                        </tr>

                    </thead>
                    <tbody>
                        @php
                            $start = checkPaginate($categories) ? $categories->firstItem() : 1;
                        @endphp
                        @foreach ($categories as $index => $category)
                            <tr>
                                <td>{{ $start + $index }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->PurchaseSummary['count'] }}</td>
                                <td>{{ $category->sales_count }}</td>
                                <td>{{ currency($category->PurchaseSummary['amount']) }}</td>
                                <td>{{ currency($category->sales_amount) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="text-end">
                                <b>{{ __('Total') }}</b>
                            </td>
                            <td>
                                <b>{{ $data['totalPurchaseCount'] }}</b>
                            </td>
                            <td>
                                <b>{{ $data['totalSalesCount'] }}</b>
                            </td>
                            <td>
                                <b>{{ currency($data['totalPurchaseAmount']) }}</b>
                            </td>
                            <td>
                                <b>{{ currency($data['totalSalesAmount']) }}</b>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $categories->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
