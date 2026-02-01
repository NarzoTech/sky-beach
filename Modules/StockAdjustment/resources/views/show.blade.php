@extends('admin.layouts.master')
@section('title', __('Stock Adjustment Details'))

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Stock Adjustment') }} - {{ $adjustment->adjustment_number }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
                <a href="{{ route('admin.stock-adjustment.edit', $adjustment->id) }}" class="btn btn-warning">
                    <i class="fa fa-edit"></i> {{ __('Edit') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">{{ __('Adjustment Number') }}</th>
                            <td>{{ $adjustment->adjustment_number }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <td>{{ $adjustment->adjustment_date->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Type') }}</th>
                            <td>
                                <span class="badge {{ $adjustment->isDecrease() ? 'bg-danger' : 'bg-success' }}">
                                    {{ $adjustment->adjustment_type_label }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                <span class="badge bg-{{ $adjustment->status === 'approved' ? 'success' : ($adjustment->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($adjustment->status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">{{ __('Ingredient') }}</th>
                            <td>{{ $adjustment->ingredient->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Quantity') }}</th>
                            <td class="{{ $adjustment->quantity < 0 ? 'text-danger' : 'text-success' }}">
                                <strong>{{ $adjustment->quantity < 0 ? '-' : '+' }}{{ number_format(abs($adjustment->quantity), 4) }}</strong>
                                {{ $adjustment->unit->name ?? '' }}
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Cost Per Unit') }}</th>
                            <td>{{ currency($adjustment->cost_per_unit) }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Total Cost') }}</th>
                            <td><strong>{{ currency($adjustment->total_cost) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <h6>{{ __('Reason') }}</h6>
                    <p class="text-muted">{{ $adjustment->reason ?? 'No reason provided' }}</p>
                </div>
                <div class="col-md-6">
                    <h6>{{ __('Notes') }}</h6>
                    <p class="text-muted">{{ $adjustment->notes ?? 'No notes' }}</p>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        {{ __('Created By') }}: {{ $adjustment->createdBy->name ?? 'System' }}<br>
                        {{ __('Created At') }}: {{ $adjustment->created_at->format('d M, Y h:i A') }}
                    </small>
                </div>
                @if($adjustment->warehouse)
                <div class="col-md-6">
                    <small class="text-muted">
                        {{ __('Warehouse') }}: {{ $adjustment->warehouse->name }}
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
