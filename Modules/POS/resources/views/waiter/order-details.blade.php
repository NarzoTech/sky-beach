@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Order Details') }} #{{ $order->id }}</title>
@endsection

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title mb-0">
                        <i class="fas fa-receipt me-2"></i>{{ __('Order') }} #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                    </h3>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.waiter.my-orders') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Orders') }}
                    </a>
                    @if($order->status == 0)
                    <a href="{{ route('admin.waiter.add-to-order', $order->id) }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>{{ __('Add Items') }}
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Order Info -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Order Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td><strong>{{ __('Order #') }}</strong></td>
                                <td>{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Table') }}</strong></td>
                                <td>
                                    @if($order->table)
                                    <span class="badge bg-info">{{ $order->table->name }}</span>
                                    @else
                                    -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Guests') }}</strong></td>
                                <td>{{ $order->guest_count ?? 1 }}</td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Status') }}</strong></td>
                                <td>
                                    @if($order->status == 0)
                                    <span class="badge bg-warning">{{ __('Processing') }}</span>
                                    @elseif($order->status == 1)
                                    <span class="badge bg-success">{{ __('Completed') }}</span>
                                    @else
                                    <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Payment') }}</strong></td>
                                <td>
                                    @if($order->payment_status == 1)
                                    <span class="badge bg-success">{{ __('Paid') }}</span>
                                    @else
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>{{ __('Created') }}</strong></td>
                                <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            @if($order->customer)
                            <tr>
                                <td><strong>{{ __('Customer') }}</strong></td>
                                <td>{{ $order->customer->name }}</td>
                            </tr>
                            @endif
                        </table>

                        @if($order->special_instructions)
                        <hr>
                        <div>
                            <strong>{{ __('Special Instructions') }}:</strong>
                            <p class="text-muted mb-0">{{ $order->special_instructions }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                @if($order->status == 0)
                <div class="card mt-3">
                    <div class="card-body">
                        <button class="btn btn-danger w-100" onclick="cancelOrder()">
                            <i class="fas fa-times me-1"></i>{{ __('Cancel Order') }}
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Order Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Order Items') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Item') }}</th>
                                        <th class="text-center">{{ __('Qty') }}</th>
                                        <th class="text-end">{{ __('Price') }}</th>
                                        <th class="text-end">{{ __('Subtotal') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->details as $detail)
                                    <tr>
                                        <td>
                                            <strong>{{ $detail->menuItem->name ?? $detail->service->name ?? 'Item' }}</strong>
                                            @if($detail->addons)
                                                @php $addons = is_string($detail->addons) ? json_decode($detail->addons, true) : $detail->addons; @endphp
                                                @if(is_array($addons) && count($addons) > 0)
                                                <div class="small text-muted">
                                                    @foreach($addons as $addon)
                                                    <span class="badge bg-light text-dark me-1">+ {{ $addon['name'] }}</span>
                                                    @endforeach
                                                </div>
                                                @endif
                                            @endif
                                            @if($detail->note)
                                            <div class="small text-info">
                                                <i class="fas fa-sticky-note"></i> {{ $detail->note }}
                                            </div>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $detail->quantity }}</td>
                                        <td class="text-end">{{ number_format($detail->price, 2) }}</td>
                                        <td class="text-end">{{ number_format($detail->sub_total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>{{ __('Subtotal') }}</strong></td>
                                        <td class="text-end">{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    @if($order->tax_amount > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">{{ __('Tax') }}</td>
                                        <td class="text-end">{{ number_format($order->tax_amount, 2) }}</td>
                                    </tr>
                                    @endif
                                    @if($order->discount_amount > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">{{ __('Discount') }}</td>
                                        <td class="text-end">-{{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>{{ __('Total') }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($order->total, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                @if($order->payments && $order->payments->count() > 0)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Payments') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Method') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->payments as $payment)
                                <tr>
                                    <td>{{ ucfirst($payment->payment_type ?? 'Cash') }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->created_at->format('d M, H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function cancelOrder() {
        Swal.fire({
            title: "{{ __('Cancel Order?') }}",
            text: "{{ __('Are you sure you want to cancel this order? This action cannot be undone.') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: "{{ __('Yes, Cancel It') }}",
            cancelButtonText: "{{ __('No, Keep It') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.waiter.cancel-order', $order->id) }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cancelled',
                                text: response.message
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to cancel order.'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
