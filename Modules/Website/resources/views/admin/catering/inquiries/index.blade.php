@extends('admin.layouts.master')
@section('title', __('Catering Inquiries'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Catering Inquiries') }}</h4>
        <a href="{{ route('admin.restaurant.catering.inquiries.export', request()->query()) }}" class="btn btn-outline-primary">
            <i class="bx bx-download me-1"></i>{{ __('Export CSV') }}
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
                            <span class="text-muted">{{ __('Total') }}</span>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-envelope"></i>
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
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
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
                            <span class="text-muted">{{ __('Quoted') }}</span>
                            <h3 class="mb-0">{{ $stats['quoted'] }}</h3>
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
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <form action="{{ route('admin.restaurant.catering.inquiries.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search name, email, phone...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>{{ __('Contacted') }}</option>
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
                        <th>{{ __('Inquiry #') }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Event') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Guests') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Quoted') }}</th>
                        <th style="width: 100px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiries as $inquiry)
                        <tr>
                            <td>
                                <a href="{{ route('admin.restaurant.catering.inquiries.show', $inquiry) }}" class="fw-semibold">
                                    {{ $inquiry->inquiry_number }}
                                </a>
                                <div class="small text-muted">{{ $inquiry->created_at->format('M d, Y') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $inquiry->name }}</div>
                                <div class="small text-muted">{{ $inquiry->email }}</div>
                            </td>
                            <td>
                                <span class="badge bg-label-secondary">{{ $inquiry->event_type_label }}</span>
                                @if($inquiry->package)
                                    <div class="small text-muted mt-1">{{ $inquiry->package->name }}</div>
                                @endif
                            </td>
                            <td>{{ $inquiry->event_date->format('M d, Y') }}</td>
                            <td>{{ $inquiry->guest_count }}</td>
                            <td>
                                <span class="badge bg-{{ $inquiry->status_badge_class }}">{{ $inquiry->status_label }}</span>
                            </td>
                            <td>
                                @if($inquiry->quoted_amount)
                                    <span class="fw-semibold text-success">{{ currency($inquiry->quoted_amount) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('admin.restaurant.catering.inquiries.show', $inquiry) }}">
                                            <i class="bx bx-show me-1"></i>{{ __('View Details') }}
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('admin.restaurant.catering.inquiries.destroy', $inquiry) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this inquiry?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-1"></i>{{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bx bx-envelope bx-lg mb-2"></i>
                                    <p class="mb-0">{{ __('No inquiries found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inquiries->hasPages())
            <div class="card-footer">
                {{ $inquiries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
