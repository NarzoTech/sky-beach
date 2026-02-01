@extends('admin.layouts.master')
@section('title', __('Quotation') . ' ' . $quotation->quotation_number)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ __('Quotation Details') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.restaurant.catering.quotations.index') }}">{{ __('Quotations') }}</a></li>
                    <li class="breadcrumb-item active">{{ $quotation->quotation_number }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.restaurant.catering.quotations.print', $quotation) }}" class="btn btn-info" target="_blank">
                <i class="bx bx-printer me-1"></i>{{ __('Print') }}
            </a>
            <a href="{{ route('admin.restaurant.catering.quotations.pdf', $quotation) }}" class="btn btn-secondary">
                <i class="bx bx-download me-1"></i>{{ __('PDF') }}
            </a>
            <a href="{{ route('admin.restaurant.catering.quotations.edit', $quotation) }}" class="btn btn-primary">
                <i class="bx bx-edit me-1"></i>{{ __('Edit') }}
            </a>
            <a href="{{ route('admin.restaurant.catering.quotations.index') }}" class="btn btn-outline-secondary">
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
            <!-- Quotation Header -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="text-primary mb-1">{{ $quotation->quotation_number }}</h3>
                            <p class="text-muted mb-0">{{ __('Created on') }} {{ $quotation->quoted_at ? $quotation->quoted_at->format('F d, Y') : $quotation->created_at->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="badge bg-{{ $quotation->status_badge_class }} fs-6">{{ $quotation->status_label }}</span>
                            @if($quotation->quotation_valid_until)
                                <div class="mt-2">
                                    @if($quotation->quotation_valid_until->isPast())
                                        <span class="text-danger"><i class="bx bx-error-circle me-1"></i>{{ __('Expired on') }} {{ $quotation->quotation_valid_until->format('M d, Y') }}</span>
                                    @else
                                        <span class="text-success"><i class="bx bx-check-circle me-1"></i>{{ __('Valid until') }} {{ $quotation->quotation_valid_until->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-user me-2"></i>{{ __('Customer Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Full Name') }}</label>
                            <div class="fw-semibold">{{ $quotation->name }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Email') }}</label>
                            <div>
                                <a href="mailto:{{ $quotation->email }}">{{ $quotation->email }}</a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Phone') }}</label>
                            <div>
                                <a href="tel:{{ $quotation->phone }}">{{ $quotation->phone }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-calendar-event me-2"></i>{{ __('Event Details') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">{{ __('Event Type') }}</label>
                            <div class="fw-semibold">{{ $quotation->event_type_label }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">{{ __('Event Date') }}</label>
                            <div class="fw-semibold">{{ $quotation->event_date->format('F d, Y') }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">{{ __('Event Time') }}</label>
                            <div class="fw-semibold">
                                @if($quotation->event_time)
                                    {{ \Carbon\Carbon::parse($quotation->event_time)->format('g:i A') }}
                                @else
                                    <span class="text-muted">{{ __('Not specified') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted small">{{ __('Number of Guests') }}</label>
                            <div class="fw-semibold">
                                <span class="badge bg-primary fs-6">{{ $quotation->guest_count }} {{ __('guests') }}</span>
                            </div>
                        </div>
                        @if($quotation->package)
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">{{ __('Selected Package') }}</label>
                                <div class="fw-semibold">{{ $quotation->package->name }}</div>
                                <div class="small text-muted">{{ currency($quotation->package->price_per_person) }} {{ __('per person') }}</div>
                            </div>
                        @endif
                        @if($quotation->venue_address)
                            <div class="col-12 mb-3">
                                <label class="text-muted small">{{ __('Venue Address') }}</label>
                                <div>{{ $quotation->venue_address }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quotation Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>{{ __('Quotation Items') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Description') }}</th>
                                    <th class="text-center" style="width: 100px;">{{ __('Qty') }}</th>
                                    <th class="text-end" style="width: 130px;">{{ __('Unit Price') }}</th>
                                    <th class="text-end" style="width: 130px;">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($quotation->quotation_items && count($quotation->quotation_items) > 0)
                                    @foreach($quotation->quotation_items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item['description'] }}</td>
                                            <td class="text-center">{{ $item['quantity'] }}</td>
                                            <td class="text-end">{{ currency($item['unit_price']) }}</td>
                                            <td class="text-end">{{ currency($item['total'] ?? $item['quantity'] * $item['unit_price']) }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">{{ __('No items') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>{{ __('Subtotal') }}</strong></td>
                                    <td class="text-end">{{ currency($quotation->quotation_subtotal) }}</td>
                                </tr>
                                @if($quotation->quotation_discount > 0)
                                    <tr class="text-success">
                                        <td colspan="4" class="text-end">
                                            <strong>{{ __('Discount') }}</strong>
                                            @if($quotation->quotation_discount_type === 'percentage')
                                                ({{ $quotation->quotation_discount }}%)
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            -{{ currency($quotation->quotation_discount_type === 'percentage' ? ($quotation->quotation_subtotal * $quotation->quotation_discount / 100) : $quotation->quotation_discount) }}
                                        </td>
                                    </tr>
                                @endif
                                @if($quotation->quotation_tax_rate > 0)
                                    <tr>
                                        <td colspan="4" class="text-end">
                                            <strong>{{ __('Tax') }}</strong> ({{ $quotation->quotation_tax_rate }}%)
                                        </td>
                                        <td class="text-end">{{ currency($quotation->quotation_tax_amount) }}</td>
                                    </tr>
                                @endif
                                @if($quotation->quotation_delivery_fee > 0)
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>{{ __('Delivery Fee') }}</strong></td>
                                        <td class="text-end">{{ currency($quotation->quotation_delivery_fee) }}</td>
                                    </tr>
                                @endif
                                <tr class="table-primary">
                                    <td colspan="4" class="text-end"><strong class="fs-5">{{ __('Grand Total') }}</strong></td>
                                    <td class="text-end"><strong class="fs-5">{{ currency($quotation->quoted_amount) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes & Terms -->
            @if($quotation->quotation_notes || $quotation->quotation_terms)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-note me-2"></i>{{ __('Notes & Terms') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($quotation->quotation_notes)
                            <div class="mb-3">
                                <label class="text-muted small d-block mb-1">{{ __('Notes') }}</label>
                                <p class="mb-0">{!! nl2br(e($quotation->quotation_notes)) !!}</p>
                            </div>
                        @endif
                        @if($quotation->quotation_terms)
                            <div>
                                <label class="text-muted small d-block mb-1">{{ __('Terms & Conditions') }}</label>
                                <p class="mb-0">{!! nl2br(e($quotation->quotation_terms)) !!}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Update Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Update Status') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.restaurant.catering.inquiries.update-status', $quotation) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label class="form-label">{{ __('Status') }}</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $quotation->status === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="contacted" {{ $quotation->status === 'contacted' ? 'selected' : '' }}>{{ __('Contacted') }}</option>
                                <option value="quoted" {{ $quotation->status === 'quoted' ? 'selected' : '' }}>{{ __('Quoted') }}</option>
                                <option value="confirmed" {{ $quotation->status === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                <option value="cancelled" {{ $quotation->status === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Admin Notes') }}</label>
                            <textarea name="admin_notes" class="form-control" rows="3">{{ $quotation->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-save me-1"></i>{{ __('Update Status') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Summary -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">{{ __('Quick Summary') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Guests') }}:</span>
                        <strong>{{ $quotation->guest_count }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Event Date') }}:</span>
                        <strong>{{ $quotation->event_date->format('M d, Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Items') }}:</span>
                        <strong>{{ $quotation->quotation_items ? count($quotation->quotation_items) : 0 }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fs-5">{{ __('Total') }}:</span>
                        <strong class="fs-4 text-primary">{{ currency($quotation->quoted_amount) }}</strong>
                    </div>
                    @if($quotation->guest_count > 0 && $quotation->quoted_amount > 0)
                        <div class="text-end text-muted small">
                            {{ currency($quotation->quoted_amount / $quotation->guest_count) }} {{ __('per guest') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <a href="mailto:{{ $quotation->email }}?subject=Quotation {{ $quotation->quotation_number }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bx bx-envelope me-1"></i>{{ __('Send Email') }}
                    </a>
                    <a href="tel:{{ $quotation->phone }}" class="btn btn-outline-success w-100 mb-2">
                        <i class="bx bx-phone me-1"></i>{{ __('Call Customer') }}
                    </a>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $quotation->phone) }}?text={{ urlencode('Hello ' . $quotation->name . ', regarding quotation ' . $quotation->quotation_number) }}" target="_blank" class="btn btn-outline-success w-100 mb-2">
                        <i class="bx bxl-whatsapp me-1"></i>{{ __('WhatsApp') }}
                    </a>
                    <hr>
                    <form action="{{ route('admin.restaurant.catering.quotations.destroy', $quotation) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this quotation?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bx bx-trash me-1"></i>{{ __('Delete Quotation') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Timeline') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline timeline-dashed">
                        <li class="timeline-item timeline-item-primary">
                            <span class="timeline-indicator timeline-indicator-primary">
                                <i class="bx bx-file"></i>
                            </span>
                            <div class="timeline-event">
                                <div class="timeline-header">
                                    <small class="text-muted">{{ __('Quotation Created') }}</small>
                                </div>
                                <div class="small">{{ $quotation->quoted_at ? $quotation->quoted_at->format('M d, Y g:i A') : $quotation->created_at->format('M d, Y g:i A') }}</div>
                            </div>
                        </li>
                        @if($quotation->contacted_at)
                            <li class="timeline-item timeline-item-info">
                                <span class="timeline-indicator timeline-indicator-info">
                                    <i class="bx bx-phone"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <small class="text-muted">{{ __('Contacted') }}</small>
                                    </div>
                                    <div class="small">{{ $quotation->contacted_at->format('M d, Y g:i A') }}</div>
                                </div>
                            </li>
                        @endif
                        @if($quotation->confirmed_at)
                            <li class="timeline-item timeline-item-success">
                                <span class="timeline-indicator timeline-indicator-success">
                                    <i class="bx bx-check"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <small class="text-muted">{{ __('Confirmed') }}</small>
                                    </div>
                                    <div class="small">{{ $quotation->confirmed_at->format('M d, Y g:i A') }}</div>
                                </div>
                            </li>
                        @endif
                    </ul>
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
    .timeline-indicator-success {
        color: #71dd37;
        border: 2px solid #71dd37;
    }
</style>
@endpush
