@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Tax Reports') }}</title>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ __('Tax Reports') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Tax Reports') }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tax-reports.ledger') }}" class="btn btn-outline-primary">
                <i class="fas fa-list me-1"></i> {{ __('View Ledger') }}
            </a>
            <a href="{{ route('admin.tax-reports.export', request()->all()) }}" class="btn btn-outline-success">
                <i class="fas fa-download me-1"></i> {{ __('Export CSV') }}
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.tax-reports.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Start Date') }}</label>
                    <input type="date" name="start_date" class="form-control"
                           value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" class="form-control"
                           value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> {{ __('Filter') }}
                    </button>
                    <a href="{{ route('admin.tax-reports.index') }}" class="btn btn-outline-secondary">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">{{ __('Tax Collected') }}</h6>
                            <h3 class="mb-0">{{ number_format($currentSummary['total_tax_collected'], 2) }}</h3>
                        </div>
                        <div class="display-4 opacity-50">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                    <small>{{ __('Output Tax (Sales)') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">{{ __('Tax Paid') }}</h6>
                            <h3 class="mb-0">{{ number_format($currentSummary['total_tax_paid'], 2) }}</h3>
                        </div>
                        <div class="display-4 opacity-50">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                    <small>{{ __('Input Tax (Purchases)') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">{{ __('Net Tax Payable') }}</h6>
                            <h3 class="mb-0">{{ number_format($currentSummary['net_tax_payable'], 2) }}</h3>
                        </div>
                        <div class="display-4 opacity-50">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                    </div>
                    <small>{{ __('Collected - Paid') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-white-50">{{ __('Transactions') }}</h6>
                            <h3 class="mb-0">{{ $currentSummary['total_transactions'] }}</h3>
                        </div>
                        <div class="display-4 opacity-50">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <small>{{ __('Total Tax Entries') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Taxable Amounts -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('Total Taxable Sales') }}</h6>
                    <h4>{{ number_format($currentSummary['total_taxable_sales'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted">{{ __('Total Taxable Purchases') }}</h6>
                    <h4>{{ number_format($currentSummary['total_taxable_purchases'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Trend Chart -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Tax Collection Trend (Last 12 Months)') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="taxTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Yearly Summary -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Year to Date') }} ({{ now()->year }})</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>{{ __('Tax Collected') }}</td>
                            <td class="text-end text-success fw-bold">
                                {{ number_format($yearlySummary['total_tax_collected'], 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('Tax Paid') }}</td>
                            <td class="text-end text-danger fw-bold">
                                {{ number_format($yearlySummary['total_tax_paid'], 2) }}
                            </td>
                        </tr>
                        <tr class="table-primary">
                            <td><strong>{{ __('Net Payable') }}</strong></td>
                            <td class="text-end fw-bold">
                                {{ number_format($yearlySummary['net_tax_payable'], 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('Taxable Sales') }}</td>
                            <td class="text-end">
                                {{ number_format($yearlySummary['total_taxable_sales'], 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('Taxable Purchases') }}</td>
                            <td class="text-end">
                                {{ number_format($yearlySummary['total_taxable_purchases'], 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('Total Entries') }}</td>
                            <td class="text-end">
                                {{ $yearlySummary['total_transactions'] }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Entries -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Recent Tax Entries') }}</h5>
            <a href="{{ route('admin.tax-reports.ledger') }}" class="btn btn-sm btn-outline-primary">
                {{ __('View All') }}
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Tax Name') }}</th>
                            <th class="text-end">{{ __('Rate') }}</th>
                            <th class="text-end">{{ __('Taxable Amount') }}</th>
                            <th class="text-end">{{ __('Tax Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEntries as $entry)
                        <tr>
                            <td>{{ $entry->transaction_date->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ strtoupper($entry->reference_type) }}</span>
                                {{ $entry->reference_number }}
                            </td>
                            <td>
                                @if($entry->type === 'collected')
                                    <span class="badge bg-success">{{ __('Collected') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Paid') }}</span>
                                @endif
                            </td>
                            <td>{{ $entry->tax_name }}</td>
                            <td class="text-end">{{ number_format($entry->tax_rate, 2) }}%</td>
                            <td class="text-end">{{ number_format($entry->taxable_amount, 2) }}</td>
                            <td class="text-end fw-bold">
                                @if($entry->type === 'collected')
                                    <span class="text-success">+{{ number_format($entry->tax_amount, 2) }}</span>
                                @else
                                    <span class="text-danger">-{{ number_format($entry->tax_amount, 2) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">{{ __('No tax entries found.') }}</p>
                                <a href="{{ route('admin.tax-reports.sync-sales') }}" class="btn btn-sm btn-primary mt-2"
                                   onclick="return confirm('{{ __('This will sync existing sales to the tax ledger. Continue?') }}')">
                                    {{ __('Sync Existing Sales') }}
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('taxTrendChart').getContext('2d');
    const periodData = @json($periodSummaries);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: periodData.map(p => {
                const date = new Date(p.period_start);
                return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
            }),
            datasets: [
                {
                    label: '{{ __("Tax Collected") }}',
                    data: periodData.map(p => p.total_tax_collected),
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: '{{ __("Tax Paid") }}',
                    data: periodData.map(p => p.total_tax_paid),
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: '{{ __("Net Payable") }}',
                    data: periodData.map(p => p.net_tax_payable),
                    type: 'line',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
