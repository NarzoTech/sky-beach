@extends('admin.layouts.app')

@section('title', __('Tax Ledger'))

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">{{ __('Tax Ledger') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.tax-reports.index') }}">{{ __('Tax Reports') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Ledger') }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
                <i class="fas fa-plus me-1"></i> {{ __('Add Adjustment') }}
            </button>
            <a href="{{ route('admin.tax-reports.export', request()->all()) }}" class="btn btn-outline-success">
                <i class="fas fa-download me-1"></i> {{ __('Export') }}
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.tax-reports.ledger') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">{{ __('Start Date') }}</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('End Date') }}</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Type') }}</label>
                    <select name="type" class="form-select">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="collected" {{ request('type') == 'collected' ? 'selected' : '' }}>{{ __('Collected') }}</option>
                        <option value="paid" {{ request('type') == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>{{ __('Adjustment') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('Active Only') }}</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="voided" {{ request('status') == 'voided' ? 'selected' : '' }}>{{ __('Voided') }}</option>
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Search') }}</label>
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Reference #') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> {{ __('Filter') }}
                    </button>
                    <a href="{{ route('admin.tax-reports.ledger') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ __('Tax Collected') }}</h6>
                    <h4 class="text-success mb-0">{{ number_format($filteredSummary['total_collected'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ __('Tax Paid') }}</h6>
                    <h4 class="text-danger mb-0">{{ number_format($filteredSummary['total_paid'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="text-muted">{{ __('Total Entries') }}</h6>
                    <h4 class="text-info mb-0">{{ $filteredSummary['total_entries'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Tax') }}</th>
                            <th class="text-end">{{ __('Rate') }}</th>
                            <th class="text-end">{{ __('Taxable') }}</th>
                            <th class="text-end">{{ __('Tax Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                        <tr class="{{ $entry->status === 'voided' ? 'table-secondary text-decoration-line-through' : '' }}">
                            <td>{{ $entry->transaction_date->format('d M Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $entry->reference_type === 'sale' ? 'primary' : ($entry->reference_type === 'purchase' ? 'warning' : 'secondary') }}">
                                    {{ strtoupper($entry->reference_type) }}
                                </span>
                                @if($entry->sale_id && $entry->sale)
                                    <a href="{{ route('admin.sales.show', $entry->sale_id) }}" target="_blank">
                                        {{ $entry->reference_number }}
                                    </a>
                                @else
                                    {{ $entry->reference_number }}
                                @endif
                            </td>
                            <td>
                                @if($entry->type === 'collected')
                                    <span class="badge bg-success">{{ __('Collected') }}</span>
                                @elseif($entry->type === 'paid')
                                    <span class="badge bg-danger">{{ __('Paid') }}</span>
                                @else
                                    <span class="badge bg-info">{{ __('Adjustment') }}</span>
                                @endif
                            </td>
                            <td>{{ $entry->tax_name ?? '-' }}</td>
                            <td class="text-end">{{ number_format($entry->tax_rate, 2) }}%</td>
                            <td class="text-end">{{ number_format($entry->taxable_amount, 2) }}</td>
                            <td class="text-end fw-bold">
                                @if($entry->type === 'collected')
                                    <span class="text-success">+{{ number_format($entry->tax_amount, 2) }}</span>
                                @else
                                    <span class="text-danger">-{{ number_format(abs($entry->tax_amount), 2) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($entry->status === 'active')
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @elseif($entry->status === 'voided')
                                    <span class="badge bg-secondary">{{ __('Voided') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('Adjusted') }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($entry->description, 30) }}</small>
                                @if($entry->void_reason)
                                    <br><small class="text-danger">{{ __('Void: ') }}{{ $entry->void_reason }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($entry->status === 'active')
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#voidModal"
                                        data-entry-id="{{ $entry->id }}"
                                        data-entry-ref="{{ $entry->reference_number }}">
                                    <i class="fas fa-ban"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">{{ __('No tax entries found.') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($entries->hasPages())
        <div class="card-footer">
            {{ $entries->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Void Modal -->
<div class="modal fade" id="voidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="voidForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Void Tax Entry') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to void this entry?') }} <strong id="voidEntryRef"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Reason for voiding') }} <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required
                                  placeholder="{{ __('Enter the reason for voiding this entry...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Void Entry') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Adjustment Modal -->
<div class="modal fade" id="adjustmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tax-reports.adjustment') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create Tax Adjustment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Adjustment Type') }} <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="collected">{{ __('Tax Collected (Increase Liability)') }}</option>
                            <option value="paid">{{ __('Tax Paid (Decrease Liability)') }}</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Transaction Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Taxable Amount') }}</label>
                        <input type="number" name="taxable_amount" class="form-control" step="0.01" min="0" value="0">
                        <small class="text-muted">{{ __('Optional: The base amount before tax') }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Tax Amount') }} <span class="text-danger">*</span></label>
                        <input type="number" name="tax_amount" class="form-control" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" required
                                  placeholder="{{ __('Explain the reason for this adjustment...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create Adjustment') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const voidModal = document.getElementById('voidModal');
    voidModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const entryId = button.getAttribute('data-entry-id');
        const entryRef = button.getAttribute('data-entry-ref');

        document.getElementById('voidEntryRef').textContent = entryRef;
        document.getElementById('voidForm').action = '{{ url("admin/tax-reports/void") }}/' + entryId;
    });
});
</script>
@endpush
