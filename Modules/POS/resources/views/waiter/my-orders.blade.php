@extends('admin.layouts.master')

@section('title', __('My Orders'))

@section('content')
<div class="card">
    <div class="card-header-tab card-header">
        <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
            <h4 class="section_title">{{ __('My Orders') }}</h4>
        </div>
        <div class="btn-actions-pane-right actions-icon-btn">
            <a href="{{ route('admin.waiter.dashboard') }}" class="btn btn-primary">
                <i class="bx bx-arrow-back"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table style="width: 100%;" class="table">
                <thead>
                    <tr>
                        <th>{{ __('SL.') }}</th>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ __('Table') }}</th>
                        <th>{{ __('Items') }}</th>
                        <th>{{ __('Total') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Time') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
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
                        <td>{{ $order->details->count() }} {{ __('items') }}</td>
                        <td><strong>{{ currency($order->total) }}</strong></td>
                        <td>
                            @if($order->status == 'pending' || $order->status == 'confirmed' || $order->status == 'preparing')
                            <span class="badge bg-warning">{{ __('Processing') }}</span>
                            @elseif($order->status == 'ready')
                            <span class="badge bg-info">{{ __('Ready') }}</span>
                            @elseif($order->status == 'completed' || $order->status == 'delivered')
                            <span class="badge bg-success">{{ __('Completed') }}</span>
                            @elseif($order->status == 'cancelled')
                            <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                            @else
                            <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $order->created_at->format('d M, H:i') }}</small>
                            <br>
                            <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.waiter.order-details', $order->id) }}"
                               class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="{{ __('View') }}">
                                <i class="bx bx-show"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bx bx-receipt bx-lg text-muted mb-3"></i>
                            <p class="text-muted mb-0">{{ __('No orders found') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="float-right">
            {{ $orders->onEachSide(0)->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
