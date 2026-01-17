@extends('admin.layouts.master')

@section('title')
    <title>{{ __('My Orders') }}</title>
@endsection

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title mb-0">
                        <i class="fas fa-receipt me-2"></i>{{ __('My Orders') }}
                    </h3>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.waiter.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Dashboard') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>{{ __('Order #') }}</th>
                                <th>{{ __('Table') }}</th>
                                <th>{{ __('Items') }}</th>
                                <th>{{ __('Total') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td>
                                    <strong>#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                </td>
                                <td>
                                    @if($order->table)
                                    <span class="badge bg-info">{{ $order->table->name }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $order->details->count() }} items</td>
                                <td><strong>{{ number_format($order->total, 2) }}</strong></td>
                                <td>
                                    @if($order->status == 0)
                                    <span class="badge bg-warning">{{ __('Processing') }}</span>
                                    @elseif($order->status == 1)
                                    <span class="badge bg-success">{{ __('Completed') }}</span>
                                    @else
                                    <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $order->created_at->format('d M, H:i') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.waiter.order-details', $order->id) }}"
                                           class="btn btn-outline-primary" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($order->status == 0)
                                        <a href="{{ route('admin.waiter.add-to-order', $order->id) }}"
                                           class="btn btn-outline-success" title="{{ __('Add Items') }}">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">{{ __('No orders found') }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $orders->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
