@extends('admin.layouts.master')
@section('title', __('Loyalty Transactions'))
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form action="" method="GET" class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="type">{{ __('Type') }}</label>
                        <select name="type" id="type" class="form-control">
                            <option value="">{{ __('All Types') }}</option>
                            <option value="earn" {{ $filters['type'] == 'earn' ? 'selected' : '' }}>{{ __('Earn') }}</option>
                            <option value="redeem" {{ $filters['type'] == 'redeem' ? 'selected' : '' }}>{{ __('Redeem') }}</option>
                            <option value="adjustment" {{ $filters['type'] == 'adjustment' ? 'selected' : '' }}>{{ __('Adjustment') }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="start_date">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $filters['start_date'] }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="end_date">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $filters['end_date'] }}">
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">{{ __('Filter') }}</button>
                    <a href="{{ route('membership.transactions.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Loyalty Transactions') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('membership.transactions.statistics', request()->query()) }}" class="btn btn-info">
                    <i class="fa fa-chart-bar"></i> {{ __('Statistics') }}
                </a>
                <a href="{{ route('membership.transactions.export', request()->query()) }}" class="btn btn-success">
                    <i class="fa fa-download"></i> {{ __('Export CSV') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table mb-5">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Points') }}</th>
                            <th>{{ __('Balance Before') }}</th>
                            <th>{{ __('Balance After') }}</th>
                            <th>{{ __('Source') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $transaction)
                            <tr>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('membership.customers.show', $transaction->loyalty_customer_id) }}">
                                        {{ $transaction->customer->phone ?? 'N/A' }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $transaction->customer->name ?? '' }}</small>
                                </td>
                                <td>
                                    @if ($transaction->transaction_type == 'earn')
                                        <span class="badge bg-success">{{ __('Earned') }}</span>
                                    @elseif ($transaction->transaction_type == 'redeem')
                                        <span class="badge bg-danger">{{ __('Redeemed') }}</span>
                                    @elseif ($transaction->transaction_type == 'adjustment')
                                        <span class="badge bg-info">{{ __('Adjusted') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $transaction->transaction_type }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($transaction->points_amount >= 0)
                                        <span class="text-success">+{{ number_format($transaction->points_amount) }}</span>
                                    @else
                                        <span class="text-danger">{{ number_format($transaction->points_amount) }}</span>
                                    @endif
                                </td>
                                <td>{{ number_format($transaction->points_balance_before) }}</td>
                                <td>{{ number_format($transaction->points_balance_after) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $transaction->source_type ?? 'N/A')) }}</td>
                                <td>
                                    <a href="{{ route('membership.transactions.show', $transaction) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('No transactions found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="float-right">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
