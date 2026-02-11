@extends('admin.layouts.master')
@section('title', __('Table Performance Report'))

@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="" method="GET">
                <div class="row">
                    <div class="col-xxl-2 col-md-4">
                        <div class="form-group">
                            <select name="floor" class="form-control">
                                <option value="">{{ __('All Floors') }}</option>
                                @foreach ($floors as $floor)
                                    <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>
                                        {{ __('Floor') }} {{ $floor }}
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
            <h4 class="section_title">{{ __('Table Performance Report') }}</h4>
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
                            <th>{{ __('Table Name') }}</th>
                            <th>{{ __('Floor') }}</th>
                            <th>{{ __('Capacity') }}</th>
                            <th>{{ __('Total Orders') }}</th>
                            <th>{{ __('Total Revenue') }}</th>
                            <th>{{ __('Avg Order Value') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $start = checkPaginate($tables) ? $tables->firstItem() : 1;
                        @endphp
                        @foreach ($tables as $index => $table)
                            <tr>
                                <td>{{ $start + $index }}</td>
                                <td>{{ $table->table->name ?? 'N/A' }}</td>
                                <td>{{ $table->table->floor ?? 'N/A' }}</td>
                                <td>{{ $table->table->capacity ?? 'N/A' }}</td>
                                <td>{{ $table->total_orders }}</td>
                                <td>{{ currency($table->net_revenue ?? ($table->total_revenue - ($table->total_tax ?? 0))) }}</td>
                                <td>{{ currency($table->total_orders > 0 ? ($table->net_revenue ?? ($table->total_revenue - ($table->total_tax ?? 0))) / $table->total_orders : 0) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" class="text-end"><b>{{ __('Total') }}</b></td>
                            <td><b>{{ $data['totalOrders'] }}</b></td>
                            <td><b>{{ currency($data['totalRevenue']) }}</b></td>
                            <td><b>{{ currency($data['totalOrders'] > 0 ? $data['totalRevenue'] / $data['totalOrders'] : 0) }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ checkPaginate($tables) ? $tables->onEachSide(0)->links() : '' }}
                </div>
            @endif
        </div>
    </div>
@endsection
