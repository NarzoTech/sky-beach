@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Stock Adjustment') }}</title>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Edit Stock Adjustment') }} - {{ $adjustment->adjustment_number }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.stock-adjustment.update', $adjustment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ingredient_id">{{ __('Ingredient') }} <span class="text-danger">*</span></label>
                            <select name="ingredient_id" id="ingredient_id" class="form-control select2" required>
                                <option value="">{{ __('Select Ingredient') }}</option>
                                @foreach ($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}"
                                        data-stock="{{ $ingredient->stock }}"
                                        data-unit="{{ $ingredient->purchaseUnit->name ?? $ingredient->unit->name ?? '' }}"
                                        data-cost="{{ $ingredient->average_cost ?? $ingredient->purchase_price ?? 0 }}"
                                        {{ old('ingredient_id', $adjustment->ingredient_id) == $ingredient->id ? 'selected' : '' }}>
                                        {{ $ingredient->name }} (Stock: {{ $ingredient->stock }} {{ $ingredient->purchaseUnit->name ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('ingredient_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="adjustment_type">{{ __('Adjustment Type') }} <span class="text-danger">*</span></label>
                            <select name="adjustment_type" id="adjustment_type" class="form-control" required>
                                <option value="">{{ __('Select Type') }}</option>
                                @foreach ($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('adjustment_type', $adjustment->adjustment_type) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('adjustment_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="quantity">{{ __('Quantity') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.0001" min="0.0001" name="quantity" id="quantity"
                                    class="form-control" value="{{ old('quantity', abs($adjustment->quantity)) }}" required>
                                <span class="input-group-text" id="unit-label">{{ $adjustment->unit->name ?? 'Unit' }}</span>
                            </div>
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="adjustment_date">{{ __('Adjustment Date') }} <span class="text-danger">*</span></label>
                            <input type="text" name="adjustment_date" id="adjustment_date"
                                class="form-control datepicker" value="{{ old('adjustment_date', $adjustment->adjustment_date->format('d-m-Y')) }}" required>
                            @error('adjustment_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="warehouse_id">{{ __('Warehouse') }}</label>
                            <select name="warehouse_id" id="warehouse_id" class="form-control">
                                <option value="">{{ __('Select Warehouse') }}</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $adjustment->warehouse_id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Current Cost') }}</label>
                            <div class="form-control bg-light">{{ currency($adjustment->total_cost) }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="reason">{{ __('Reason') }}</label>
                            <textarea name="reason" id="reason" class="form-control" rows="2">{{ old('reason', $adjustment->reason) }}</textarea>
                            @error('reason')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="notes">{{ __('Additional Notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes', $adjustment->notes) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> {{ __('Update Adjustment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
