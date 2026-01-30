@extends('admin.layouts.app')

@section('title', __('Tax Periods'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ __('Tax Period Management') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tax-reports.index') }}">{{ __('Tax Reports') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Periods') }}</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePeriodModal">
            <i class="fas fa-plus me-1"></i> {{ __('Generate Period') }}
        </button>
    </div>

    <!-- Periods Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Tax Filing Periods') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Period') }}</th>
                            <th class="text-end">{{ __('Tax Collected') }}</th>
                            <th class="text-end">{{ __('Tax Paid') }}</th>
                            <th class="text-end">{{ __('Net Payable') }}</th>
                            <th class="text-end">{{ __('Transactions') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Closed') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periods as $period)
                        <tr>
                            <td>
                                <strong>{{ $period->period_start->format('M Y') }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $period->period_start->format('d M') }} - {{ $period->period_end->format('d M Y') }}
                                </small>
                            </td>
                            <td class="text-end text-success fw-bold">
                                {{ number_format($period->total_tax_collected, 2) }}
                            </td>
                            <td class="text-end text-danger fw-bold">
                                {{ number_format($period->total_tax_paid, 2) }}
                            </td>
                            <td class="text-end text-primary fw-bold">
                                {{ number_format($period->net_tax_payable, 2) }}
                            </td>
                            <td class="text-end">
                                {{ $period->total_transactions }}
                            </td>
                            <td>
                                <span class="badge {{ $period->getStatusBadgeClass() }}">
                                    {{ ucfirst($period->status) }}
                                </span>
                            </td>
                            <td>
                                @if($period->closed_at)
                                    {{ $period->closed_at->format('d M Y') }}
                                    <br>
                                    <small class="text-muted">{{ $period->closedBy?->name ?? 'System' }}</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <!-- Recalculate -->
                                    <form action="{{ route('admin.tax-reports.generate-period') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="period_start" value="{{ $period->period_start->format('Y-m-d') }}">
                                        <input type="hidden" name="period_end" value="{{ $period->period_end->format('Y-m-d') }}">
                                        <button type="submit" class="btn btn-outline-info" title="{{ __('Recalculate') }}">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>

                                    @if($period->status === 'open')
                                    <!-- Close Period -->
                                    <form action="{{ route('admin.tax-reports.close-period', $period->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('{{ __('Close this period? This cannot be undone.') }}')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning" title="{{ __('Close Period') }}">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    </form>
                                    @endif

                                    @if($period->status === 'closed')
                                    <!-- Mark as Filed -->
                                    <form action="{{ route('admin.tax-reports.mark-filed', $period->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('{{ __('Mark this period as filed?') }}')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="{{ __('Mark as Filed') }}">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <!-- View Details -->
                                    <a href="{{ route('admin.tax-reports.ledger', [
                                        'start_date' => $period->period_start->format('Y-m-d'),
                                        'end_date' => $period->period_end->format('Y-m-d')
                                    ]) }}" class="btn btn-outline-primary" title="{{ __('View Details') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <p class="mb-0">{{ __('No tax periods found. Generate a period to get started.') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($periods->hasPages())
        <div class="card-footer">
            {{ $periods->links() }}
        </div>
        @endif
    </div>

    <!-- Info Card -->
    <div class="card mt-4">
        <div class="card-body">
            <h6><i class="fas fa-info-circle text-info me-2"></i>{{ __('About Tax Periods') }}</h6>
            <ul class="mb-0 text-muted">
                <li>{{ __('Tax periods help you organize and track tax obligations by filing period.') }}</li>
                <li>{{ __('An "Open" period can still receive new tax entries and be recalculated.') }}</li>
                <li>{{ __('A "Closed" period is finalized and ready for filing.') }}</li>
                <li>{{ __('A "Filed" period indicates the tax return has been submitted.') }}</li>
                <li>{{ __('Click the sync button to recalculate a period\'s totals from the ledger.') }}</li>
            </ul>
        </div>
    </div>
</div>

<!-- Generate Period Modal -->
<div class="modal fade" id="generatePeriodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tax-reports.generate-period') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Generate Tax Period') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Period Type') }}</label>
                        <select id="periodType" class="form-select" onchange="updatePeriodDates()">
                            <option value="monthly">{{ __('Monthly') }}</option>
                            <option value="quarterly">{{ __('Quarterly') }}</option>
                            <option value="custom">{{ __('Custom') }}</option>
                        </select>
                    </div>
                    <div class="mb-3" id="monthSelect">
                        <label class="form-label">{{ __('Month') }}</label>
                        <input type="month" id="selectedMonth" class="form-control" value="{{ date('Y-m') }}" onchange="updatePeriodDates()">
                    </div>
                    <div class="row" id="customDates" style="display: none;">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" name="period_start" id="periodStart" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('End Date') }}</label>
                            <input type="date" name="period_end" id="periodEnd" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Generate') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updatePeriodDates() {
    const periodType = document.getElementById('periodType').value;
    const monthSelect = document.getElementById('monthSelect');
    const customDates = document.getElementById('customDates');
    const periodStart = document.getElementById('periodStart');
    const periodEnd = document.getElementById('periodEnd');
    const selectedMonth = document.getElementById('selectedMonth').value;

    if (periodType === 'custom') {
        monthSelect.style.display = 'none';
        customDates.style.display = 'flex';
    } else {
        monthSelect.style.display = 'block';
        customDates.style.display = 'none';

        if (selectedMonth) {
            const [year, month] = selectedMonth.split('-').map(Number);
            let startDate, endDate;

            if (periodType === 'monthly') {
                startDate = new Date(year, month - 1, 1);
                endDate = new Date(year, month, 0);
            } else if (periodType === 'quarterly') {
                const quarter = Math.floor((month - 1) / 3);
                startDate = new Date(year, quarter * 3, 1);
                endDate = new Date(year, (quarter + 1) * 3, 0);
            }

            periodStart.value = startDate.toISOString().split('T')[0];
            periodEnd.value = endDate.toISOString().split('T')[0];
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updatePeriodDates();
});
</script>
@endpush
