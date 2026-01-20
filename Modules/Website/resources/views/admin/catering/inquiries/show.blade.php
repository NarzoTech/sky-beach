@extends('admin.layout')

@section('title', __('Inquiry') . ' ' . $inquiry->inquiry_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ __('Inquiry Details') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.catering.inquiries.index') }}">{{ __('Inquiries') }}</a></li>
                    <li class="breadcrumb-item active">{{ $inquiry->inquiry_number }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.catering.inquiries.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('Customer Information') }}</h5>
                    <span class="badge bg-{{ $inquiry->status_badge_class }} fs-6">{{ $inquiry->status_label }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Full Name') }}</label>
                            <div class="fw-semibold">{{ $inquiry->name }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Email') }}</label>
                            <div>
                                <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Phone') }}</label>
                            <div>
                                <a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Event Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Event Type') }}</label>
                            <div class="fw-semibold">{{ $inquiry->event_type_label }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Event Date') }}</label>
                            <div class="fw-semibold">{{ $inquiry->event_date->format('F j, Y') }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Event Time') }}</label>
                            <div class="fw-semibold">
                                @if($inquiry->event_time)
                                    {{ \Carbon\Carbon::parse($inquiry->event_time)->format('g:i A') }}
                                @else
                                    <span class="text-muted">{{ __('Not specified') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Number of Guests') }}</label>
                            <div class="fw-semibold">{{ $inquiry->guest_count }}</div>
                        </div>
                        @if($inquiry->package)
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">{{ __('Selected Package') }}</label>
                                <div>
                                    <a href="{{ route('admin.catering.packages.edit', $inquiry->package) }}" class="fw-semibold">
                                        {{ $inquiry->package->name }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">{{ __('Estimated Price') }}</label>
                                <div class="fw-semibold text-primary">${{ number_format($inquiry->estimated_price, 2) }}</div>
                            </div>
                        @endif
                        @if($inquiry->venue_address)
                            <div class="col-12 mb-3">
                                <label class="text-muted small">{{ __('Venue Address') }}</label>
                                <div>{{ $inquiry->venue_address }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Special Requirements -->
            @if($inquiry->special_requirements)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Special Requirements') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{!! nl2br(e($inquiry->special_requirements)) !!}</p>
                    </div>
                </div>
            @endif

            <!-- Admin Notes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Admin Notes') }}</h5>
                </div>
                <div class="card-body">
                    @if($inquiry->admin_notes)
                        <p class="mb-0">{!! nl2br(e($inquiry->admin_notes)) !!}</p>
                    @else
                        <p class="text-muted mb-0">{{ __('No notes added yet.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Update Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Update Status') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.catering.inquiries.update-status', $inquiry) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-select" id="statusSelect">
                                <option value="pending" {{ $inquiry->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>{{ __('Contacted') }}</option>
                                <option value="quoted" {{ $inquiry->status === 'quoted' ? 'selected' : '' }}>{{ __('Quoted') }}</option>
                                <option value="confirmed" {{ $inquiry->status === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                <option value="cancelled" {{ $inquiry->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>

                        <div class="mb-3" id="quotedAmountField" style="{{ $inquiry->status === 'quoted' ? '' : 'display: none;' }}">
                            <label class="form-label">{{ __('Quoted Amount') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="quoted_amount" class="form-control" step="0.01" min="0" value="{{ $inquiry->quoted_amount }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Admin Notes') }}</label>
                            <textarea name="admin_notes" class="form-control" rows="3">{{ $inquiry->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-save me-1"></i>{{ __('Update') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Timeline') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline timeline-dashed">
                        <li class="timeline-item timeline-item-primary">
                            <span class="timeline-indicator timeline-indicator-primary">
                                <i class="bx bx-envelope"></i>
                            </span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <small class="text-muted">{{ __('Inquiry Submitted') }}</small>
                                </div>
                                <div class="small">{{ $inquiry->created_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </li>
                        @if($inquiry->contacted_at)
                            <li class="timeline-item timeline-item-info">
                                <span class="timeline-indicator timeline-indicator-info">
                                    <i class="bx bx-phone"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <small class="text-muted">{{ __('Contacted') }}</small>
                                    </div>
                                    <div class="small">{{ $inquiry->contacted_at->format('M d, Y g:i A') }}</div>
                                </div>
                            </li>
                        @endif
                        @if($inquiry->quoted_at)
                            <li class="timeline-item timeline-item-warning">
                                <span class="timeline-indicator timeline-indicator-warning">
                                    <i class="bx bx-dollar"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <small class="text-muted">{{ __('Quote Sent') }}</small>
                                    </div>
                                    <div class="small">{{ $inquiry->quoted_at->format('M d, Y g:i A') }}</div>
                                    @if($inquiry->quoted_amount)
                                        <div class="fw-semibold text-success">${{ number_format($inquiry->quoted_amount, 2) }}</div>
                                    @endif
                                </div>
                            </li>
                        @endif
                        @if($inquiry->confirmed_at)
                            <li class="timeline-item timeline-item-success">
                                <span class="timeline-indicator timeline-indicator-success">
                                    <i class="bx bx-check"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <small class="text-muted">{{ __('Confirmed') }}</small>
                                    </div>
                                    <div class="small">{{ $inquiry->confirmed_at->format('M d, Y g:i A') }}</div>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <a href="mailto:{{ $inquiry->email }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bx bx-envelope me-1"></i>{{ __('Send Email') }}
                    </a>
                    <a href="tel:{{ $inquiry->phone }}" class="btn btn-outline-success w-100 mb-2">
                        <i class="bx bx-phone me-1"></i>{{ __('Call Customer') }}
                    </a>
                    <form action="{{ route('admin.catering.inquiries.destroy', $inquiry) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this inquiry?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bx bx-trash me-1"></i>{{ __('Delete Inquiry') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        padding-left: 0;
        list-style: none;
    }
    .timeline-item {
        position: relative;
        padding-left: 40px;
        padding-bottom: 20px;
        border-left: 2px solid #e7e7e8;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-indicator {
        position: absolute;
        left: -13px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #fff;
    }
    .timeline-indicator-primary {
        color: #696cff;
        border: 2px solid #696cff;
    }
    .timeline-indicator-info {
        color: #03c3ec;
        border: 2px solid #03c3ec;
    }
    .timeline-indicator-warning {
        color: #ffab00;
        border: 2px solid #ffab00;
    }
    .timeline-indicator-success {
        color: #71dd37;
        border: 2px solid #71dd37;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('statusSelect');
    const quotedAmountField = document.getElementById('quotedAmountField');

    statusSelect.addEventListener('change', function() {
        if (this.value === 'quoted') {
            quotedAmountField.style.display = 'block';
        } else {
            quotedAmountField.style.display = 'none';
        }
    });
});
</script>
@endpush
