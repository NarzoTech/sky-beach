@extends('admin.layout')

@section('title', __('Coupon') . ' ' . $coupon->code)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ __('Coupon Details') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.coupons.index') }}">{{ __('Coupons') }}</a></li>
                    <li class="breadcrumb-item active">{{ $coupon->code }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-primary">
                <i class="bx bx-edit me-1"></i>{{ __('Edit') }}
            </a>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Coupon Info -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $coupon->code }}</h5>
                    <span class="badge bg-{{ $coupon->status_badge_class }} fs-6">{{ $coupon->status_label }}</span>
                </div>
                <div class="card-body">
                    @if($coupon->name)
                        <h6 class="text-muted mb-3">{{ $coupon->name }}</h6>
                    @endif

                    @if($coupon->description)
                        <p>{{ $coupon->description }}</p>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Discount Type') }}</label>
                            <div class="fw-semibold">{{ $coupon->type_label }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Discount Value') }}</label>
                            <div class="fw-semibold text-primary fs-5">{{ $coupon->discount_display }}</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Min Order Amount') }}</label>
                            <div class="fw-semibold">
                                @if($coupon->min_order_amount > 0)
                                    ${{ number_format($coupon->min_order_amount, 2) }}
                                @else
                                    {{ __('No minimum') }}
                                @endif
                            </div>
                        </div>
                        @if($coupon->max_discount)
                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">{{ __('Max Discount') }}</label>
                                <div class="fw-semibold">${{ number_format($coupon->max_discount, 2) }}</div>
                            </div>
                        @endif
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Valid From') }}</label>
                            <div class="fw-semibold">
                                {{ $coupon->valid_from ? $coupon->valid_from->format('M d, Y') : __('Immediately') }}
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">{{ __('Valid Until') }}</label>
                            <div class="fw-semibold">
                                {{ $coupon->valid_until ? $coupon->valid_until->format('M d, Y') : __('No expiry') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Recent Usage') }}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Order') }}</th>
                                <th>{{ __('Discount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupon->usages as $usage)
                                <tr>
                                    <td>{{ $usage->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $usage->user_identifier }}</td>
                                    <td>
                                        @if($usage->sale)
                                            <a href="{{ route('admin.website-orders.show', $usage->sale_id) }}">
                                                #{{ $usage->sale->id }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-success">${{ number_format($usage->discount_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        {{ __('No usage history yet.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Usage Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Usage Statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="display-4 fw-bold text-primary">{{ $coupon->used_count }}</div>
                        <div class="text-muted">{{ __('Times Used') }}</div>
                    </div>

                    @if($coupon->usage_limit)
                        @php $usagePercentage = min(100, ($coupon->used_count / $coupon->usage_limit) * 100); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ __('Usage Progress') }}</span>
                                <span>{{ round($usagePercentage) }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-{{ $usagePercentage >= 90 ? 'danger' : ($usagePercentage >= 70 ? 'warning' : 'success') }}" style="width: {{ $usagePercentage }}%"></div>
                            </div>
                            <small class="text-muted">{{ $coupon->used_count }} / {{ $coupon->usage_limit }} {{ __('uses') }}</small>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="bx bx-infinite me-1"></i>{{ __('Unlimited usage') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Limits Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Limits') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('Total Limit') }}</span>
                        <span class="fw-semibold">{{ $coupon->usage_limit ?? __('Unlimited') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('Per User Limit') }}</span>
                        <span class="fw-semibold">{{ $coupon->usage_limit_per_user }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Quick Actions') }}</h5>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-{{ $coupon->is_active ? 'warning' : 'success' }} w-100 mb-2 toggle-status" data-id="{{ $coupon->id }}">
                        <i class="bx bx-{{ $coupon->is_active ? 'pause' : 'play' }} me-1"></i>
                        {{ $coupon->is_active ? __('Deactivate') : __('Activate') }}
                    </button>
                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this coupon?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bx bx-trash me-1"></i>{{ __('Delete Coupon') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelector('.toggle-status').addEventListener('click', function() {
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
</script>
@endpush
