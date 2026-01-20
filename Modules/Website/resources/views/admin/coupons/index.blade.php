@extends('admin.layout')

@section('title', __('Coupons'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Coupons') }}</h4>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="bx bx-plus me-1"></i>{{ __('Add Coupon') }}
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
        <div class="col-sm-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted">{{ __('Total Coupons') }}</span>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-gift"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted">{{ __('Active Coupons') }}</span>
                            <h3 class="mb-0">{{ $stats['active'] }}</h3>
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
        <div class="col-sm-6 col-lg-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="text-muted">{{ __('Total Usage') }}</span>
                            <h3 class="mb-0">{{ $stats['total_usage'] }}</h3>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-trending-up"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <form action="{{ route('admin.coupons.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search code or name...') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
                        <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount') }}</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bx bx-search me-1"></i>{{ __('Filter') }}
                    </button>
                </div>
                @if(request()->hasAny(['search', 'status', 'type']))
                    <div class="col-md-2">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary w-100">
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
                        <th>{{ __('Code') }}</th>
                        <th>{{ __('Discount') }}</th>
                        <th>{{ __('Min Order') }}</th>
                        <th>{{ __('Usage') }}</th>
                        <th>{{ __('Valid Period') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th style="width: 120px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $coupon->code }}</div>
                                @if($coupon->name)
                                    <small class="text-muted">{{ $coupon->name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $coupon->discount_display }}</span>
                                <div class="small text-muted">{{ $coupon->type_label }}</div>
                            </td>
                            <td>
                                @if($coupon->min_order_amount > 0)
                                    ${{ number_format($coupon->min_order_amount, 2) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {{ $coupon->used_count }}
                                @if($coupon->usage_limit)
                                    / {{ $coupon->usage_limit }}
                                @endif
                            </td>
                            <td>
                                @if($coupon->valid_from || $coupon->valid_until)
                                    <small>
                                        {{ $coupon->valid_from ? $coupon->valid_from->format('M d, Y') : __('Any') }}
                                        -
                                        {{ $coupon->valid_until ? $coupon->valid_until->format('M d, Y') : __('No end') }}
                                    </small>
                                @else
                                    <span class="text-muted">{{ __('Always valid') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $coupon->status_badge_class }}">{{ $coupon->status_label }}</span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('admin.coupons.show', $coupon) }}">
                                            <i class="bx bx-show me-1"></i>{{ __('View') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.coupons.edit', $coupon) }}">
                                            <i class="bx bx-edit me-1"></i>{{ __('Edit') }}
                                        </a>
                                        <button type="button" class="dropdown-item toggle-status" data-id="{{ $coupon->id }}">
                                            <i class="bx bx-{{ $coupon->is_active ? 'pause' : 'play' }} me-1"></i>
                                            {{ $coupon->is_active ? __('Deactivate') : __('Activate') }}
                                        </button>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this coupon?') }}')">
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
                                    <i class="bx bx-gift bx-lg mb-2"></i>
                                    <p class="mb-0">{{ __('No coupons found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
            <div class="card-footer">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.toggle-status').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch(`{{ url('admin/coupons') }}/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
});
</script>
@endpush
