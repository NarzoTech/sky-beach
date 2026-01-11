@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Transaction Statistics') }}</title>
@endsection
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form action="" method="GET" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="warehouse_id">{{ __('Warehouse') }}</label>
                        <select name="warehouse_id" id="warehouse_id" class="form-control">
                            <option value="">{{ __('All Warehouses') }}</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $filters['warehouse_id'] == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $filters['start_date'] }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $filters['end_date'] }}">
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                    <a href="{{ route('membership.transactions.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ number_format($total_earned) }}</h3>
                    <p class="mb-0">{{ __('Total Points Earned') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ number_format($total_redeemed) }}</h3>
                    <p class="mb-0">{{ __('Total Points Redeemed') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ number_format($total_earned - $total_redeemed) }}</h3>
                    <p class="mb-0">{{ __('Net Points') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ number_format($active_customers) }}</h3>
                    <p class="mb-0">{{ __('Active Customers') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Earnings by Source') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Source') }}</th>
                                <th>{{ __('Count') }}</th>
                                <th>{{ __('Total Points') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($earnings_by_type as $earning)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $earning->source_type ?? 'Unknown')) }}</td>
                                    <td>{{ number_format($earning->count) }}</td>
                                    <td class="text-success">+{{ number_format($earning->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('No data available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Redemptions by Method') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Method') }}</th>
                                <th>{{ __('Count') }}</th>
                                <th>{{ __('Total Points') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($redemptions_by_type as $redemption)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $redemption->redemption_method ?? 'Unknown')) }}</td>
                                    <td>{{ number_format($redemption->count) }}</td>
                                    <td class="text-danger">-{{ number_format($redemption->total) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">{{ __('No data available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
