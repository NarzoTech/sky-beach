@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Catering Packages') }}</title>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Catering Packages') }}</h4>
        <a href="{{ route('admin.restaurant.catering.packages.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>{{ __('Add Package') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom">
            <form action="{{ route('admin.restaurant.catering.packages.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search packages...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="is_active" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bx bx-search me-1"></i>{{ __('Filter') }}
                    </button>
                </div>
                @if(request()->hasAny(['search', 'is_active']))
                    <div class="col-md-2">
                        <a href="{{ route('admin.restaurant.catering.packages.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bx bx-x me-1"></i>{{ __('Clear') }}
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 60px;">{{ __('Image') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Price/Person') }}</th>
                        <th>{{ __('Guests') }}</th>
                        <th>{{ __('Inquiries') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th style="width: 120px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $package)
                        <tr>
                            <td>
                                <img src="{{ $package->image_url }}" alt="{{ $package->name }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $package->name }}</div>
                                @if($package->is_featured)
                                    <span class="badge bg-warning text-dark"><i class="bx bx-star me-1"></i>{{ __('Featured') }}</span>
                                @endif
                            </td>
                            <td>{{ currency($package->price_per_person) }}</td>
                            <td>{{ $package->min_guests }} - {{ $package->max_guests }}</td>
                            <td>
                                <a href="{{ route('admin.restaurant.catering.inquiries.index', ['package_id' => $package->id]) }}" class="text-primary">
                                    {{ $package->inquiries_count }}
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
                                            <i class="bx bx-show me-1"></i>{{ __('View') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.restaurant.catering.packages.edit', $package) }}">
                                            <i class="bx bx-edit me-1"></i>{{ __('Edit') }}
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('admin.restaurant.catering.packages.destroy', $package) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this package?') }}')">
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
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bx bx-package bx-lg mb-2"></i>
                                    <p class="mb-0">{{ __('No catering packages found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($packages->hasPages())
            <div class="card-footer">
                {{ $packages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
