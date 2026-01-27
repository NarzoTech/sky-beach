@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Inquiry') }} {{ $inquiry->inquiry_number }}</title>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ __('Inquiry Details') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.restaurant.catering.inquiries.index') }}">{{ __('Inquiries') }}</a></li>
                    <li class="breadcrumb-item active">{{ $inquiry->inquiry_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quotationModal">
                <i class="bx bx-calculator me-1"></i>{{ $inquiry->quoted_amount ? __('Edit Quotation') : __('Create Quotation') }}
            </button>
            <a href="{{ route('admin.restaurant.catering.inquiries.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
            </a>
        </div>
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
                                    <a href="{{ route('admin.restaurant.catering.packages.edit', $inquiry->package) }}" class="fw-semibold">
                                        {{ $inquiry->package->name }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">{{ __('Package Price/Person') }}</label>
                                <div class="fw-semibold text-primary">{{ currency($inquiry->package->price_per_person) }}</div>
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

            <!-- Quotation Details (if exists) -->
            @if($inquiry->quoted_amount)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bx bx-receipt me-2"></i>{{ __('Quotation') }}</h5>
                        @if($inquiry->quotation_valid_until)
                            <small class="text-muted">
                                {{ __('Valid until') }}: {{ $inquiry->quotation_valid_until->format('M d, Y') }}
                                @if($inquiry->quotation_valid_until->isPast())
                                    <span class="badge bg-danger ms-1">{{ __('Expired') }}</span>
                                @endif
                            </small>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        @if($inquiry->quotation_items && count($inquiry->quotation_items) > 0)
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Description') }}</th>
                                            <th class="text-center" style="width: 100px;">{{ __('Qty') }}</th>
                                            <th class="text-end" style="width: 120px;">{{ __('Unit Price') }}</th>
                                            <th class="text-end" style="width: 120px;">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inquiry->quotation_items as $item)
                                            <tr>
                                                <td>{{ $item['description'] }}</td>
                                                <td class="text-center">{{ $item['quantity'] }}</td>
                                                <td class="text-end">{{ currency($item['unit_price']) }}</td>
                                                <td class="text-end">{{ currency($item['total']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>{{ __('Subtotal') }}</strong></td>
                                            <td class="text-end">{{ currency($inquiry->quotation_subtotal) }}</td>
                                        </tr>
                                        @if($inquiry->quotation_discount > 0)
                                            <tr class="text-success">
                                                <td colspan="3" class="text-end">
                                                    <strong>{{ __('Discount') }}</strong>
                                                    @if($inquiry->quotation_discount_type === 'percentage')
                                                        ({{ $inquiry->quotation_discount }}%)
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    -{{ currency($inquiry->quotation_discount_type === 'percentage' ? ($inquiry->quotation_subtotal * $inquiry->quotation_discount / 100) : $inquiry->quotation_discount) }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if($inquiry->quotation_tax_rate > 0)
                                            <tr>
                                                <td colspan="3" class="text-end">
                                                    <strong>{{ __('Tax') }}</strong> ({{ $inquiry->quotation_tax_rate }}%)
                                                </td>
                                                <td class="text-end">{{ currency($inquiry->quotation_tax_amount) }}</td>
                                            </tr>
                                        @endif
                                        @if($inquiry->quotation_delivery_fee > 0)
                                            <tr>
                                                <td colspan="3" class="text-end"><strong>{{ __('Delivery Fee') }}</strong></td>
                                                <td class="text-end">{{ currency($inquiry->quotation_delivery_fee) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-primary">
                                            <td colspan="3" class="text-end"><strong class="fs-5">{{ __('Grand Total') }}</strong></td>
                                            <td class="text-end"><strong class="fs-5">{{ currency($inquiry->quoted_amount) }}</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ __('Quoted Amount') }}</span>
                                    <strong class="fs-4 text-primary">{{ currency($inquiry->quoted_amount) }}</strong>
                                </div>
                            </div>
                        @endif

                        @if($inquiry->quotation_notes)
                            <div class="card-body border-top">
                                <label class="text-muted small d-block mb-1">{{ __('Quotation Notes') }}</label>
                                <p class="mb-0">{{ $inquiry->quotation_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

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
                    <form action="{{ route('admin.restaurant.catering.inquiries.update-status', $inquiry) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $inquiry->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>{{ __('Contacted') }}</option>
                                <option value="quoted" {{ $inquiry->status === 'quoted' ? 'selected' : '' }}>{{ __('Quoted') }}</option>
                                <option value="confirmed" {{ $inquiry->status === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                <option value="cancelled" {{ $inquiry->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
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
                                        <div class="fw-semibold text-success">{{ currency($inquiry->quoted_amount) }}</div>
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
                    <form action="{{ route('admin.restaurant.catering.inquiries.destroy', $inquiry) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this inquiry?') }}')">
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

<!-- Quotation Modal -->
<div class="modal fade" id="quotationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bx bx-calculator me-2"></i>{{ __('Create Quotation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.restaurant.catering.inquiries.save-quotation', $inquiry) }}" method="POST" id="quotationForm">
                @csrf
                <div class="modal-body">
                    <!-- Quick Info -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-4">
                                <small class="text-muted">{{ __('Customer') }}</small>
                                <div class="fw-semibold">{{ $inquiry->name }}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">{{ __('Event') }}</small>
                                <div class="fw-semibold">{{ $inquiry->event_type_label }}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">{{ __('Guests') }}</small>
                                <div class="fw-semibold">{{ $inquiry->guest_count }} {{ __('persons') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Line Items -->
                    <h6 class="mb-3">{{ __('Quotation Items') }}</h6>
                    <div id="quotation-items">
                        @if($inquiry->quotation_items && count($inquiry->quotation_items) > 0)
                            @foreach($inquiry->quotation_items as $index => $item)
                                <div class="quotation-item row g-2 mb-2">
                                    <div class="col-md-5">
                                        <input type="text" name="items[{{ $index }}][description]" class="form-control" placeholder="{{ __('Description') }}" value="{{ $item['description'] }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-qty" placeholder="{{ __('Qty') }}" value="{{ $item['quantity'] }}" min="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="items[{{ $index }}][unit_price]" class="form-control item-price" placeholder="{{ __('Unit Price') }}" value="{{ $item['unit_price'] }}" step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-item">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @if($inquiry->package)
                                <div class="quotation-item row g-2 mb-2">
                                    <div class="col-md-5">
                                        <input type="text" name="items[0][description]" class="form-control" placeholder="{{ __('Description') }}" value="{{ $inquiry->package->name }} ({{ $inquiry->guest_count }} guests)" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="items[0][quantity]" class="form-control item-qty" placeholder="{{ __('Qty') }}" value="{{ $inquiry->guest_count }}" min="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="items[0][unit_price]" class="form-control item-price" placeholder="{{ __('Unit Price') }}" value="{{ $inquiry->package->price_per_person }}" step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-item">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="quotation-item row g-2 mb-2">
                                    <div class="col-md-5">
                                        <input type="text" name="items[0][description]" class="form-control" placeholder="{{ __('Description') }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="items[0][quantity]" class="form-control item-qty" placeholder="{{ __('Qty') }}" value="1" min="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="items[0][unit_price]" class="form-control item-price" placeholder="{{ __('Unit Price') }}" step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100 remove-item">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="add-item">
                        <i class="bx bx-plus me-1"></i>{{ __('Add Item') }}
                    </button>

                    <hr>

                    <!-- Discount, Tax, Delivery -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Discount') }}</label>
                            <div class="input-group">
                                <input type="number" name="discount" class="form-control" id="discount" step="0.01" min="0" value="{{ $inquiry->quotation_discount ?? 0 }}">
                                <select name="discount_type" class="form-select" id="discount_type" style="max-width: 100px;">
                                    <option value="fixed" {{ ($inquiry->quotation_discount_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>{{ currency_icon() }}</option>
                                    <option value="percentage" {{ ($inquiry->quotation_discount_type ?? '') === 'percentage' ? 'selected' : '' }}>%</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Tax Rate') }} (%)</label>
                            <input type="number" name="tax_rate" class="form-control" id="tax_rate" step="0.01" min="0" max="100" value="{{ $inquiry->quotation_tax_rate ?? 0 }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Delivery Fee') }}</label>
                            <input type="number" name="delivery_fee" class="form-control" id="delivery_fee" step="0.01" min="0" value="{{ $inquiry->quotation_delivery_fee ?? 0 }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Valid Until') }}</label>
                            <input type="date" name="valid_until" class="form-control" value="{{ $inquiry->quotation_valid_until ? $inquiry->quotation_valid_until->format('Y-m-d') : '' }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Quotation Notes') }}</label>
                        <textarea name="quotation_notes" class="form-control" rows="2" placeholder="{{ __('Additional notes for the customer...') }}">{{ $inquiry->quotation_notes }}</textarea>
                    </div>

                    <!-- Summary -->
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Subtotal') }}:</span>
                                <span id="summary-subtotal">{{ currency_icon() }}0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 text-success" id="summary-discount-row" style="display: none !important;">
                                <span>{{ __('Discount') }}:</span>
                                <span id="summary-discount">-{{ currency_icon() }}0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" id="summary-tax-row" style="display: none !important;">
                                <span>{{ __('Tax') }}:</span>
                                <span id="summary-tax">{{ currency_icon() }}0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" id="summary-delivery-row" style="display: none !important;">
                                <span>{{ __('Delivery Fee') }}:</span>
                                <span id="summary-delivery">{{ currency_icon() }}0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong class="fs-5">{{ __('Grand Total') }}:</strong>
                                <strong class="fs-5 text-primary" id="summary-total">{{ currency_icon() }}0.00</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>{{ __('Save Quotation') }}
                    </button>
                </div>
            </form>
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
    const currencySymbol = '{{ currency_icon() }}';
    let itemIndex = {{ $inquiry->quotation_items ? count($inquiry->quotation_items) : 1 }};

    // Add new item row
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('quotation-items');
        const newRow = document.createElement('div');
        newRow.className = 'quotation-item row g-2 mb-2';
        newRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="items[${itemIndex}][description]" class="form-control" placeholder="{{ __('Description') }}" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-qty" placeholder="{{ __('Qty') }}" value="1" min="1" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control item-price" placeholder="{{ __('Unit Price') }}" step="0.01" min="0" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger w-100 remove-item">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
        itemIndex++;
        attachRemoveHandlers();
        attachCalculateHandlers();
    });

    // Remove item row
    function attachRemoveHandlers() {
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.onclick = function() {
                const items = document.querySelectorAll('.quotation-item');
                if (items.length > 1) {
                    this.closest('.quotation-item').remove();
                    calculateTotals();
                }
            };
        });
    }

    // Calculate totals on input change
    function attachCalculateHandlers() {
        document.querySelectorAll('.item-qty, .item-price').forEach(input => {
            input.oninput = calculateTotals;
        });
    }

    function calculateTotals() {
        let subtotal = 0;

        document.querySelectorAll('.quotation-item').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            subtotal += qty * price;
        });

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const discountType = document.getElementById('discount_type').value;
        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
        const deliveryFee = parseFloat(document.getElementById('delivery_fee').value) || 0;

        let discountAmount = 0;
        if (discountType === 'percentage') {
            discountAmount = subtotal * (discount / 100);
        } else {
            discountAmount = discount;
        }

        const afterDiscount = subtotal - discountAmount;
        const taxAmount = afterDiscount * (taxRate / 100);
        const grandTotal = afterDiscount + taxAmount + deliveryFee;

        // Update summary
        document.getElementById('summary-subtotal').textContent = currencySymbol + subtotal.toFixed(2);
        document.getElementById('summary-discount').textContent = '-' + currencySymbol + discountAmount.toFixed(2);
        document.getElementById('summary-tax').textContent = currencySymbol + taxAmount.toFixed(2);
        document.getElementById('summary-delivery').textContent = currencySymbol + deliveryFee.toFixed(2);
        document.getElementById('summary-total').textContent = currencySymbol + grandTotal.toFixed(2);

        // Show/hide rows
        document.getElementById('summary-discount-row').style.display = discountAmount > 0 ? 'flex' : 'none';
        document.getElementById('summary-tax-row').style.display = taxAmount > 0 ? 'flex' : 'none';
        document.getElementById('summary-delivery-row').style.display = deliveryFee > 0 ? 'flex' : 'none';
    }

    // Initial setup
    attachRemoveHandlers();
    attachCalculateHandlers();

    // Calculate on discount/tax/delivery change
    document.getElementById('discount').addEventListener('input', calculateTotals);
    document.getElementById('discount_type').addEventListener('change', calculateTotals);
    document.getElementById('tax_rate').addEventListener('input', calculateTotals);
    document.getElementById('delivery_fee').addEventListener('input', calculateTotals);

    // Initial calculation
    calculateTotals();
});
</script>
@endpush
