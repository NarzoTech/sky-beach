@extends('admin.layouts.master')
@section('title', __('Wastage Summary Report'))

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Wastage Summary Report') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back to Adjustments') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <form class="search_form mb-4" action="" method="GET">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('Date Range') }}</label>
                            <div class="input-group input-daterange">
                                <input type="text" placeholder="From Date" class="form-control datepicker" name="from_date"
                                    value="{{ request('from_date', $fromDate->format('d-m-Y')) }}" autocomplete="off">
                                <span class="input-group-text">to</span>
                                <input type="text" placeholder="To Date" class="form-control datepicker" name="to_date"
                                    value="{{ request('to_date', $toDate->format('d-m-Y')) }}" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mb-4">
                @php
                    $totalWastageCost = $summary->sum('total_cost');
                @endphp
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3>{{ currency($totalWastageCost) }}</h3>
                            <p class="mb-0">{{ __('Total Wastage Cost') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Adjustment Type') }}</th>
                            <th class="text-center">{{ __('Count') }}</th>
                            <th class="text-end">{{ __('Total Quantity') }}</th>
                            <th class="text-end">{{ __('Total Cost') }}</th>
                            <th class="text-end">{{ __('% of Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary as $item)
                            <tr>
                                <td>
                                    <span class="badge bg-danger">
                                        {{ $types[$item->adjustment_type] ?? ucfirst($item->adjustment_type) }}
                                    </span>
                                </td>
                                <td class="text-center">{{ $item->count }}</td>
                                <td class="text-end">{{ number_format($item->total_quantity, 2) }}</td>
                                <td class="text-end">{{ currency($item->total_cost) }}</td>
                                <td class="text-end">
                                    @if($totalWastageCost > 0)
                                        {{ number_format(($item->total_cost / $totalWastageCost) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ __('No wastage data found for the selected period') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($summary->count() > 0)
                        <tfoot class="bg-light">
                            <tr>
                                <th>{{ __('Total') }}</th>
                                <th class="text-center">{{ $summary->sum('count') }}</th>
                                <th class="text-end">{{ number_format($summary->sum('total_quantity'), 2) }}</th>
                                <th class="text-end">{{ currency($totalWastageCost) }}</th>
                                <th class="text-end">100%</th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <div class="mt-4">
                <p class="text-muted">
                    <small>
                        {{ __('Report Period') }}: {{ $fromDate->format('d M, Y') }} - {{ $toDate->format('d M, Y') }}<br>
                        {{ __('This report shows wastage, damage, theft, and internal consumption adjustments only.') }}
                    </small>
                </p>
            </div>
        </div>
    </div>
@endsection
