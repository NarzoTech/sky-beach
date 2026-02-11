@extends('admin.layouts.master')
@section('title', __('Order Type Report'))

@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="" method="GET">
                <div class="row">
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
            <h4 class="section_title">{{ __('Order Type Report') }}</h4>
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
                            <th>{{ __('Order Type') }}</th>
                            <th>{{ __('Total Orders') }}</th>
                            <th>{{ __('Total Revenue') }} <small class="text-muted">({{ __('incl. Tax') }})</small></th>
                            <th>{{ __('Tax') }}</th>
                            <th>{{ __('Net Revenue') }} <small class="text-muted">({{ __('excl. Tax') }})</small></th>
                            <th>{{ __('Total Cost') }}</th>
                            <th>{{ __('Profit') }}</th>
                            <th>{{ __('% of Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $orderTypeLabels = [
                                'dine_in' => __('Dine In'),
                                'take_away' => __('Take Away'),
                                'website' => __('Website'),
                            ];
                        @endphp
                        @foreach ($orderTypes as $index => $type)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $orderTypeLabels[$type->order_type] ?? ucfirst(str_replace('_', ' ', $type->order_type)) }}</td>
                                <td>{{ $type->total_orders }}</td>
                                <td>{{ currency($type->total_revenue) }}</td>
                                <td class="text-danger">{{ currency($type->total_tax) }}</td>
                                <td>{{ currency($type->total_revenue - $type->total_tax) }}</td>
                                <td>{{ currency($type->total_cogs) }}</td>
                                <td>{{ currency($type->total_revenue - $type->total_tax - $type->total_cogs) }}</td>
                                <td>{{ $grandTotalOrders > 0 ? round(($type->total_orders / $grandTotalOrders) * 100, 1) : 0 }}%</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="text-end"><b>{{ __('Total') }}</b></td>
                            <td><b>{{ $data['totalOrders'] }}</b></td>
                            <td><b>{{ currency($data['totalRevenue']) }}</b></td>
                            <td class="text-danger"><b>{{ currency($data['totalTax']) }}</b></td>
                            <td><b>{{ currency($data['totalNetRevenue']) }}</b></td>
                            <td><b>{{ currency($data['totalCogs']) }}</b></td>
                            <td><b>{{ currency($data['totalProfit']) }}</b></td>
                            <td><b>100%</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
