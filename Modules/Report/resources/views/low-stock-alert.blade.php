@extends('admin.layouts.master')
@section('title', __('Low Stock Alert'))

@section('content')
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-danger mb-1">{{ $data['criticalCount'] }}</h3>
                    <p class="mb-0 text-muted">{{ __('Out of Stock') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-warning mb-1">{{ $data['lowCount'] }}</h3>
                    <p class="mb-0 text-muted">{{ __('Low Stock') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-info mb-1">{{ $data['totalItems'] }}</h3>
                    <p class="mb-0 text-muted">{{ __('Total Items Needing Attention') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="section_title">{{ __('Low Stock Alert') }}</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Ingredient') }}</th>
                            <th>{{ __('SKU') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Current Stock') }}</th>
                            <th>{{ __('Alert Level') }}</th>
                            <th>{{ __('Unit') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ingredients as $index => $ingredient)
                            @php
                                $currentStock = (float) str_replace(',', '', $ingredient->stock);
                                $isOutOfStock = $currentStock <= 0;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $ingredient->name }}</strong></td>
                                <td>{{ $ingredient->sku }}</td>
                                <td>{{ $ingredient->category->name ?? 'N/A' }}</td>
                                <td class="{{ $isOutOfStock ? 'text-danger' : 'text-warning' }}">
                                    <strong>{{ number_format($currentStock, 2) }}</strong>
                                </td>
                                <td>{{ number_format($ingredient->stock_alert, 2) }}</td>
                                <td>{{ $ingredient->purchaseUnit->name ?? $ingredient->unit->name ?? 'N/A' }}</td>
                                <td>
                                    @if($isOutOfStock)
                                        <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ __('Low Stock') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.purchase.create') }}?ingredient_id={{ $ingredient->id }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-shopping-cart"></i> {{ __('Purchase') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-success">
                                    <i class="fa fa-check-circle"></i> {{ __('All ingredients are sufficiently stocked!') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
