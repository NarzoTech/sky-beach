@extends('admin.layouts.master')
@section('title', __('Menu Item Sales Report'))

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
                            <select name="category" class="form-control">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-4">
                        <div class="form-group">
                            <select name="par-page" id="par-page" class="form-control">
                                <option value="">{{ __('Per Page') }}</option>
                                <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>{{ __('10') }}</option>
                                <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>{{ __('50') }}</option>
                                <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>{{ __('100') }}</option>
                                <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>{{ __('All') }}</option>
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
            <h4 class="section_title">{{ __('Menu Item Sales Report') }}</h4>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i> Excel</button>
                <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i> PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Menu Item') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Qty Sold') }}</th>
                            <th>{{ __('Revenue') }}</th>
                            <th>{{ __('Cost (COGS)') }}</th>
                            <th>{{ __('Profit') }}</th>
                            <th>{{ __('Profit %') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $start = checkPaginate($items) ? $items->firstItem() : 1;
                        @endphp
                        @foreach ($items as $index => $item)
                            <tr>
                                <td>{{ $start + $index }}</td>
                                <td>{{ $item->menuItem->name ?? 'N/A' }}</td>
                                <td>{{ $item->menuItem->category->name ?? 'N/A' }}</td>
                                <td>{{ $item->total_qty }}</td>
                                <td>{{ currency($item->total_revenue) }}</td>
                                <td>{{ currency($item->total_cogs) }}</td>
                                <td>{{ currency($item->total_profit) }}</td>
                                <td>{{ $item->total_revenue > 0 ? round(($item->total_profit / $item->total_revenue) * 100, 1) : 0 }}%</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-end"><b>{{ __('Total') }}</b></td>
                            <td><b>{{ $data['totalQty'] }}</b></td>
                            <td><b>{{ currency($data['totalRevenue']) }}</b></td>
                            <td><b>{{ currency($data['totalCogs']) }}</b></td>
                            <td><b>{{ currency($data['totalProfit']) }}</b></td>
                            <td><b>{{ $data['totalRevenue'] > 0 ? round(($data['totalProfit'] / $data['totalRevenue']) * 100, 1) : 0 }}%</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ checkPaginate($items) ? $items->onEachSide(0)->links() : '' }}
                </div>
            @endif
        </div>
    </div>
@endsection
