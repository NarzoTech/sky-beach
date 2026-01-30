@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Catering Quotations') }}</title>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Catering Quotations') }}</h4>
        <a href="{{ route('admin.restaurant.catering.quotations.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>{{ __('Create Quotation') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted">{{ __('Total Quotations') }}</span>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-file"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted">{{ __('Pending') }}</span>
                            <h3 class="mb-0">{{ $stats['quoted'] }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-time"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted">{{ __('Confirmed') }}</span>
                            <h3 class="mb-0">{{ $stats['confirmed'] }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-check-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted">{{ __('Total Value') }}</span>
                            <h3 class="mb-0">{{ currency($stats['total_value']) }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-dollar"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <form action="{{ route('admin.restaurant.catering.quotations.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search quotation, name, email...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>{{ __('Quoted') }}</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="event_type" class="form-select">
                        <option value="">{{ __('All Events') }}</option>
                        @foreach($eventTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('event_type') === $key ? 'selected' : '' }}>{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="from_date" class="form-control" placeholder="{{ __('From Date') }}" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to_date" class="form-control" placeholder="{{ __('To Date') }}" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>{{ __('Quotation #') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Event') }}</th>
                        <th>{{ __('Event Date') }}</th>
                        <th>{{ __('Guests') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th style="width: 120px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                        <tr>
                            <td>
                                <a href="{{ route('admin.restaurant.catering.quotations.show', $quotation) }}" class="fw-semibold">
                                    {{ $quotation->quotation_number }}
                                </a>
                                <div class="small text-muted">{{ $quotation->quoted_at ? $quotation->quoted_at->format('M d, Y') : '' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $quotation->name }}</div>
                                <div class="small text-muted">{{ $quotation->phone }}</div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $quotation->event_type_label }}</span>
                                @if($quotation->package)
                                    <div class="small text-muted mt-1">{{ $quotation->package->name }}</div>
                                @endif
                            </td>
                            <td>{{ $quotation->event_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-label-primary">{{ $quotation->guest_count }} {{ __('guests') }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">{{ currency($quotation->quoted_amount) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $quotation->status_badge_class }}">{{ $quotation->status_label }}</span>
                                @if($quotation->quotation_valid_until && $quotation->quotation_valid_until->isPast())
                                    <div class="small text-danger mt-1">{{ __('Expired') }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.restaurant.catering.quotations.show', $quotation) }}" class="btn btn-sm btn-icon btn-outline-primary" title="{{ __('View') }}">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <a href="{{ route('admin.restaurant.catering.quotations.print', $quotation) }}" class="btn btn-sm btn-icon btn-outline-info" target="_blank" title="{{ __('Print') }}">
                                        <i class="bx bx-printer"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-icon btn-outline-secondary dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('admin.restaurant.catering.quotations.edit', $quotation) }}">
                                                <i class="bx bx-edit me-1"></i>{{ __('Edit') }}
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.restaurant.catering.quotations.pdf', $quotation) }}">
                                                <i class="bx bx-download me-1"></i>{{ __('Download PDF') }}
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.restaurant.catering.quotations.destroy', $quotation) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this quotation?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-1"></i>{{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bx bx-file bx-lg mb-2"></i>
                                    <p class="mb-0">{{ __('No quotations found.') }}</p>
                                    <a href="{{ route('admin.restaurant.catering.quotations.create') }}" class="btn btn-primary btn-sm mt-2">
                                        {{ __('Create First Quotation') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div class="card-footer">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
