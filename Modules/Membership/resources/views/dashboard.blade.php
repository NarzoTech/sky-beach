@extends('admin.layouts.master')
@section('title', __('Membership Dashboard'))
@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">{{ __('Programs') }}</h6>
                    <h3 class="text-primary">
                        {{ $active_programs }}<small class="text-muted">/{{ $total_programs }}</small>
                    </h3>
                    <small class="text-muted">{{ __('Active / Total') }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">{{ __('Customers') }}</h6>
                    <h3 class="text-success">
                        {{ $active_customers }}<small class="text-muted">/{{ $total_customers }}</small>
                    </h3>
                    <small class="text-muted">{{ __('Active / Total') }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">{{ __('Points Earned') }}</h6>
                    <h3 class="text-info">{{ number_format($total_points_earned, 0) }}</h3>
                    <small class="text-muted">{{ __('Total earned') }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted">{{ __('Outstanding Points') }}</h6>
                    <h3 class="text-warning">{{ number_format($outstanding_points, 0) }}</h3>
                    <small class="text-muted">{{ __('To be redeemed') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header-tab card-header">
                    <div class="card-header-title">
                        <h5 class="mb-0">{{ __('Top Customers') }}</h5>
                    </div>
                    <div class="btn-actions-pane-right">
                        <a href="{{ route('membership.customers.index') }}" class="btn btn-sm btn-primary">{{ __('View All') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th class="text-end">{{ __('Points') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_customers as $customer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('membership.customers.show', $customer) }}">
                                                {{ $customer->phone }}
                                            </a>
                                        </td>
                                        <td>{{ $customer->name ?? '-' }}</td>
                                        <td class="text-end"><strong>{{ number_format($customer->total_points, 0) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">{{ __('No customers yet') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header-tab card-header">
                    <div class="card-header-title">
                        <h5 class="mb-0">{{ __('Recent Transactions') }}</h5>
                    </div>
                    <div class="btn-actions-pane-right">
                        <a href="{{ route('membership.transactions.index') }}" class="btn btn-sm btn-primary">{{ __('View All') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th class="text-end">{{ __('Points') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->customer->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if($transaction->transaction_type == 'earn')
                                                <span class="badge bg-success">{{ __('Earned') }}</span>
                                            @elseif($transaction->transaction_type == 'redeem')
                                                <span class="badge bg-danger">{{ __('Redeemed') }}</span>
                                            @else
                                                <span class="badge bg-info">{{ $transaction->transaction_type }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($transaction->points_amount >= 0)
                                                <span class="text-success">+{{ number_format($transaction->points_amount) }}</span>
                                            @else
                                                <span class="text-danger">{{ number_format($transaction->points_amount) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->created_at->format('M d, H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">{{ __('No transactions yet') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('membership.programs.index') }}" class="btn btn-primary me-2">
                        <i class="fa fa-list"></i> {{ __('Manage Programs') }}
                    </a>
                    <a href="{{ route('membership.customers.index') }}" class="btn btn-success me-2">
                        <i class="fa fa-users"></i> {{ __('View Customers') }}
                    </a>
                    <a href="{{ route('membership.transactions.index') }}" class="btn btn-info me-2">
                        <i class="fa fa-exchange"></i> {{ __('View Transactions') }}
                    </a>
                    <a href="{{ route('membership.transactions.statistics') }}" class="btn btn-warning">
                        <i class="fa fa-chart-bar"></i> {{ __('Statistics') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
