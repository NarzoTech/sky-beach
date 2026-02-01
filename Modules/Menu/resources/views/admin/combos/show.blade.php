@extends('admin.layouts.master')
@section('title', __('View Combo Deal'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ $combo->name }}</h4>
                                <div>
                                    <a href="{{ route('admin.combo.edit', $combo->id) }}" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> {{ __('Edit') }}
                                    </a>
                                    <a href="{{ route('admin.combo.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        @if ($combo->image)
                                            <img src="{{ asset('storage/' . $combo->image) }}" alt="{{ $combo->name }}" class="img-fluid rounded" style="max-height: 300px; width: 100%; object-fit: cover;">
                                        @else
                                            <div style="height: 200px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-image fa-3x text-muted"></i>
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            <h5>{{ __('Status') }}</h5>
                                            <span class="badge {{ $combo->status ? 'bg-success' : 'bg-danger' }}">
                                                {{ $combo->status ? __('Active') : __('Inactive') }}
                                            </span>
                                            <span class="badge {{ $combo->is_active ? 'bg-primary' : 'bg-warning' }}">
                                                {{ $combo->is_active ? __('Running') : __('Paused') }}
                                            </span>
                                        </div>

                                        <div class="mt-3">
                                            <h5>{{ __('Duration') }}</h5>
                                            @if ($combo->start_date || $combo->end_date)
                                                <p class="mb-0">
                                                    @if ($combo->start_date)
                                                        <strong>{{ __('Start') }}:</strong> {{ $combo->start_date->format('M d, Y') }}<br>
                                                    @endif
                                                    @if ($combo->end_date)
                                                        <strong>{{ __('End') }}:</strong> {{ $combo->end_date->format('M d, Y') }}
                                                    @endif
                                                </p>
                                            @else
                                                <p class="text-muted mb-0">{{ __('Always available') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-lg-8">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="200">{{ __('Name') }}</th>
                                                <td>{{ $combo->name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Slug') }}</th>
                                                <td>{{ $combo->slug }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Description') }}</th>
                                                <td>{{ $combo->description ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Original Price') }}</th>
                                                <td><span class="text-muted text-decoration-line-through">{{ number_format($combo->original_price, 2) }}</span></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Combo Price') }}</th>
                                                <td><strong class="text-success h4">{{ number_format($combo->combo_price, 2) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Savings') }}</th>
                                                <td>
                                                    @if ($combo->savings > 0)
                                                        <span class="badge bg-success">{{ number_format($combo->savings, 2) }} ({{ number_format($combo->savings_percentage, 0) }}% off)</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if ($combo->discount_type)
                                                <tr>
                                                    <th>{{ __('Discount') }}</th>
                                                    <td>
                                                        {{ $combo->discount_value }}{{ $combo->discount_type === 'percentage' ? '%' : '' }}
                                                        ({{ ucfirst($combo->discount_type) }})
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>

                                        <!-- Combo Items -->
                                        <div class="mt-4">
                                            <h5>{{ __('Included Items') }} ({{ $combo->items->count() }})</h5>
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Item') }}</th>
                                                        <th>{{ __('Variant') }}</th>
                                                        <th>{{ __('Quantity') }}</th>
                                                        <th>{{ __('Unit Price') }}</th>
                                                        <th>{{ __('Total') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $totalOriginal = 0; @endphp
                                                    @foreach ($combo->items as $item)
                                                        @php
                                                            $unitPrice = ($item->menuItem->base_price ?? 0) + ($item->variant->price_adjustment ?? 0);
                                                            $lineTotal = $unitPrice * $item->quantity;
                                                            $totalOriginal += $lineTotal;
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                @if ($item->menuItem)
                                                                    <a href="{{ route('admin.menu-item.show', $item->menu_item_id) }}">
                                                                        {{ $item->menuItem->name }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">{{ __('Deleted Item') }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $item->variant->name ?? __('Default') }}</td>
                                                            <td>{{ $item->quantity }}</td>
                                                            <td>{{ number_format($unitPrice, 2) }}</td>
                                                            <td>{{ number_format($lineTotal, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="table-secondary">
                                                        <th colspan="4">{{ __('Total Original Price') }}</th>
                                                        <th>{{ number_format($totalOriginal, 2) }}</th>
                                                    </tr>
                                                    <tr class="table-success">
                                                        <th colspan="4">{{ __('Combo Price') }}</th>
                                                        <th>{{ number_format($combo->combo_price, 2) }}</th>
                                                    </tr>
                                                    @if ($combo->savings > 0)
                                                        <tr class="table-info">
                                                            <th colspan="4">{{ __('Customer Saves') }}</th>
                                                            <th>{{ number_format($combo->savings, 2) }}</th>
                                                        </tr>
                                                    @endif
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
