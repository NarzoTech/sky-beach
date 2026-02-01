@extends('admin.layouts.master')
@section('title', __('Catering Packages'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Catering Packages') }}</h4>
        <a href="{{ route('admin.restaurant.catering.packages.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>{{ __('Add Package') }}
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-2">
                            <p class="text-muted mb-1">{{ __('Total Packages') }}</p>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-package bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-2">
                            <p class="text-muted mb-1">{{ __('Active') }}</p>
                            <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-check-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-2">
                            <p class="text-muted mb-1">{{ __('Featured') }}</p>
                            <h3 class="mb-0 text-warning">{{ $stats['featured'] }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-star bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-2">
                            <p class="text-muted mb-1">{{ __('Total Inquiries') }}</p>
                            <h3 class="mb-0 text-info">{{ $stats['total_inquiries'] }}</h3>
                        </div>
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-envelope bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.restaurant.catering.packages.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('Search') }}</label>
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search packages...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select name="is_active" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-search me-1"></i> {{ __('Filter') }}
                    </button>
                    @if(request()->hasAny(['search', 'is_active']))
                        <a href="{{ route('admin.restaurant.catering.packages.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-reset"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Packages Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 70px;">{{ __('Image') }}</th>
                            <th>{{ __('Package') }}</th>
                            <th>{{ __('Price/Person') }}</th>
                            <th>{{ __('Guest Range') }}</th>
                            <th>{{ __('Inquiries') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th style="width: 100px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td>
                                    <img src="{{ $package->image_url }}" alt="{{ $package->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $package->name }}</strong>
                                        @if($package->is_featured)
                                            <span class="badge bg-warning text-dark ms-1"><i class="bx bx-star"></i></span>
                                        @endif
                                    </div>
                                    @if($package->description)
                                        <small class="text-muted d-block" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ Str::limit($package->description, 50) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-primary">{{ currency($package->price_per_person) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary">
                                        {{ $package->min_guests }} - {{ $package->max_guests }} {{ __('guests') }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.restaurant.catering.inquiries.index', ['package_id' => $package->id]) }}" class="badge bg-info">
                                        {{ $package->inquiries_count }} {{ __('inquiries') }}
                                    </a>
                                </td>
                                <td>
                                    @if($package->is_active)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="{{ route('website.catering.show', $package->slug) }}" target="_blank">
                                                <i class="bx bx-link-external me-2"></i>{{ __('View on Site') }}
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.restaurant.catering.packages.edit', $package) }}">
                                                <i class="bx bx-edit me-2"></i>{{ __('Edit') }}
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.restaurant.catering.packages.destroy', $package) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this package?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-2"></i>{{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bx bx-package fs-1 text-muted mb-2 d-block"></i>
                                    <p class="text-muted mb-2">{{ __('No catering packages found.') }}</p>
                                    <a href="{{ route('admin.restaurant.catering.packages.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bx bx-plus me-1"></i>{{ __('Create First Package') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($packages->hasPages())
                <div class="mt-3">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
