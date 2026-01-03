@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Add Stock Adjustment') }}</title>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Add Stock Adjustment') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.stock-adjustment.store') }}" method="POST">
                @csrf
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
                                        {{ old('ingredient_id') == $ingredient->id ? 'selected' : '' }}>
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
                                    <option value="{{ $key }}" {{ old('adjustment_type') == $key ? 'selected' : '' }}>
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
                                    class="form-control" value="{{ old('quantity') }}" required>
                                <span class="input-group-text" id="unit-label">{{ __('Unit') }}</span>
                            </div>
                            @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">
                                <span id="current-stock"></span>
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="adjustment_date">{{ __('Adjustment Date') }} <span class="text-danger">*</span></label>
                            <input type="text" name="adjustment_date" id="adjustment_date"
                                class="form-control datepicker" value="{{ old('adjustment_date', date('d-m-Y')) }}" required>
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
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ __('Estimated Cost') }}</label>
                            <div class="form-control bg-light" id="estimated-cost">0.00</div>
                            <small class="text-muted">{{ __('Based on average cost per unit') }}</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="reason">{{ __('Reason') }}</label>
                            <textarea name="reason" id="reason" class="form-control" rows="2" placeholder="Enter reason for adjustment...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="notes">{{ __('Additional Notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> {{ __('Save Adjustment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                let costPerUnit = 0;

                $('#ingredient_id').on('change', function() {
                    const selected = $(this).find(':selected');
                    const stock = selected.data('stock') || 0;
                    const unit = selected.data('unit') || 'Unit';
                    costPerUnit = parseFloat(selected.data('cost')) || 0;

                    $('#unit-label').text(unit);
                    $('#current-stock').text('Current Stock: ' + stock + ' ' + unit);
                    updateEstimatedCost();
                });

                $('#quantity').on('input', function() {
                    updateEstimatedCost();
                });

                function updateEstimatedCost() {
                    const quantity = parseFloat($('#quantity').val()) || 0;
                    const total = (quantity * costPerUnit).toFixed(2);
                    $('#estimated-cost').text(total);
                }

                // Trigger change on page load if ingredient is pre-selected
                if ($('#ingredient_id').val()) {
                    $('#ingredient_id').trigger('change');
                }
            });
        </script>
    @endpush
@endsection
