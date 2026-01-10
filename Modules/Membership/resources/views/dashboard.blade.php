@extends('layout')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-4">Membership & Loyalty Dashboard</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Programs</h6>
                    <h3 class="card-text">
                        {{ $active_programs }}<small class="text-muted">/{{ $total_programs }}</small>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Customers</h6>
                    <h3 class="card-text">
                        {{ $active_customers }}<small class="text-muted">/{{ $total_customers }}</small>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Points Earned</h6>
                    <h3 class="card-text">{{ number_format($total_points_earned, 0) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title text-muted">Outstanding Points</h6>
                    <h3 class="card-text">{{ number_format($outstanding_points, 0) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Top Customers</h5>
                    <a href="{{ route('membership.customers.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Phone</th>
                                <th>Name</th>
                                <th class="text-right">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top_customers as $customer)
                                <tr>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->name ?? '-' }}</td>
                                    <td class="text-right">{{ number_format($customer->total_points, 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No customers yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Transactions</h5>
                    <a href="{{ route('membership.transactions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Type</th>
                                <th class="text-right">Points</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->customer->phone }}</td>
                                    <td>
                                        <span class="badge @if($transaction->isEarning()) bg-success @elseif($transaction->isRedemption()) bg-warning @else bg-info @endif">
                                            {{ $transaction->transaction_type }}
                                        </span>
                                    </td>
                                    <td class="text-right">{{ $transaction->points_amount }}</td>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No transactions yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('membership.programs.index') }}" class="btn btn-primary">Manage Programs</a>
                    <a href="{{ route('membership.rules.index') }}" class="btn btn-primary">Manage Rules</a>
                    <a href="{{ route('membership.customers.index') }}" class="btn btn-primary">View Customers</a>
                    <a href="{{ route('membership.transactions.index') }}" class="btn btn-primary">View Transactions</a>
                    <a href="{{ route('membership.transactions.statistics') }}" class="btn btn-info">Statistics</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
