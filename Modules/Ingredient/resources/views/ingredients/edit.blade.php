@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Ingredient') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="section_title">{{ __('Edit Ingredient') }}</h4>
                    <div>
                        <a href="{{ route('admin.ingredient.index') }}" class="btn btn-primary"><i
                                class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="create_product_table" action="{{ route('admin.ingredient.update', $product) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">{{ __('Name') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" id="name"
                                                value="{{ old('name', $product->name) }}" required>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sku">{{ __('Code') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" name="sku" class="form-control currency"
                                                    id="sku" value="{{ old('sku', $product->sku) }}" required>
                                                <div class="input-group-text mb-0 edit_sku generate_sku cursor-pointer">
                                                    <i class="fas fa-barcode"></i>
                                                </div>
                                            </div>
                                            @error('sku')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">{{ __('Category') }}<span
                                                    class="text-danger">*</span></label>
                                            <select name="category_id" id="categories" class="form-control select2"
                                                required>
                                                <option value="">{{ __('Select Category') }}
                                                </option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        @if (old('category_id', $product->category_id) == $cat->id) selected @endif>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="brand_id">{{ __('Brand') }}</label>
                                            <select name="brand_id" id="brand_id" class="form-control select2">
                                                <option value="">{{ __('Select Brand') }}</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}"
                                                        @if (old('brand_id', $product->brand_id) == $brand->id) selected @endif>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="purchase_unit_id">{{ __('Purchase Unit') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="purchase_unit_id" id="purchase_unit_id" class="form-control select2" required>
                                                    <option value="">{{ __('Select Unit') }}</option>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}"
                                                            @if (old('purchase_unit_id', $product->purchase_unit_id) == $unit->id) selected @endif>
                                                            {{ $unit->name }} ({{ $unit->ShortName }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-text">
                                                    <a href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#unitModal" class="text-primary" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('Add new unit') }}">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            @error('purchase_unit_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consumption_unit_id">{{ __('Consumption Unit') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="consumption_unit_id" id="consumption_unit_id" class="form-control select2" required>
                                                    <option value="">{{ __('Select Unit') }}</option>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}"
                                                            @if (old('consumption_unit_id', $product->consumption_unit_id) == $unit->id) selected @endif>
                                                            {{ $unit->name }} ({{ $unit->ShortName }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="input-group-text">
                                                    <a href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#unitModal" class="text-primary" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('Add new unit') }}">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                </div>
                                                <div class="input-group-text">
                                                    <i class="fa fa-question-circle text-info" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('In which unit you make food') }}"></i>
                                                </div>
                                            </div>
                                            @error('consumption_unit_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="conversion_rate">{{ __('Conversion Rate') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.0001" name="conversion_rate" id="conversion_rate"
                                                    class="form-control" value="{{ old('conversion_rate', $product->conversion_rate ?? 1) }}" required readonly>
                                                <div class="input-group-text">
                                                    <a href="javascript:;" id="enableConversionRate" class="text-primary" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('Click to manually edit') }}">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </div>
                                                <div class="input-group-text">
                                                    <i class="fa fa-question-circle text-info" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('Auto-calculated from unit settings. 1 Purchase Unit = X Consumption Units') }}"></i>
                                                </div>
                                            </div>
                                            <small class="text-muted" id="conversionRateHelp"></small>
                                            @error('conversion_rate')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="purchase_price">{{ __('Purchase Price') }}
                                                ({{ currency_icon() }})<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" name="purchase_price" id="purchase_price"
                                                    class="form-control" value="{{ old('purchase_price', $product->purchase_price ?? 0) }}" required>
                                                <div class="input-group-text">
                                                    <i class="fa fa-question-circle text-info" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('You can change this price in purchase form') }}"></i>
                                                </div>
                                            </div>
                                            @error('purchase_price')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consumption_unit_cost">{{ __('Cost Per Unit') }}
                                                ({{ currency_icon() }})</label>
                                            <div class="input-group">
                                                <input type="number" step="0.0001" name="consumption_unit_cost" id="consumption_unit_cost"
                                                    class="form-control" value="{{ old('consumption_unit_cost', $product->consumption_unit_cost ?? 0) }}" readonly>
                                                <div class="input-group-text">
                                                    <i class="fa fa-question-circle text-info" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('In Consumption Unit') }}"></i>
                                                </div>
                                            </div>
                                            @error('consumption_unit_cost')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Low Qty') }}</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" name="stock_alert"
                                                    value="{{ old('stock_alert', $product->stock_alert ?? 0) }}">
                                                <div class="input-group-text">
                                                    <i class="fa fa-question-circle text-info" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('In Purchase Unit') }}"></i>
                                                </div>
                                            </div>
                                            @error('stock_alert')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-12">
                                        <div id="image-preview" class="image-preview"
                                            style="background-image: url('{{ asset($product->image) }}')">
                                            <label for="upload" id="image-label">{{ __('Image') }}</label>
                                            <input type="file" name="image" id="image-upload">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="status">{{ __('Status') }}<span
                                                    class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" @if (old('status', $product->status) == 1) selected @endif>
                                                    {{ __('Active') }}</option>
                                                <option value="0" @if (old('status', $product->status) == 0) selected @endif>
                                                    {{ __('Inactive') }}</option>
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="">
                                            <x-admin.update-button :text="__('Update')">
                                            </x-admin.update-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- unit create modal --}}
    @include('ingredient::unit-types.unit-modal')
@endsection

@push('js')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });

                $('[name="name"]').on('input', function() {
                    var name = $(this).val();
                    var slug = convertToSlug(name);
                    $("[name='slug']").val(slug);
                });

                // Calculate consumption unit cost
                function calculateConsumptionCost() {
                    var purchasePrice = parseFloat($('#purchase_price').val()) || 0;
                    var conversionRate = parseFloat($('#conversion_rate').val()) || 1;

                    if (conversionRate > 0) {
                        var costPerUnit = purchasePrice / conversionRate;
                        $('#consumption_unit_cost').val(costPerUnit.toFixed(4));
                    }
                }

                // Trigger calculation on input change
                $('#purchase_price, #conversion_rate').on('input change', function() {
                    calculateConsumptionCost();
                });

                // Units data for conversion calculation
                const unitsData = @json($units);

                // Enable manual edit of conversion rate
                $('#enableConversionRate').on('click', function() {
                    $('#conversion_rate').prop('readonly', false).focus();
                    $(this).hide();
                });

                // Auto-calculate conversion rate when units change
                $('#purchase_unit_id, #consumption_unit_id').on('change', function() {
                    calculateConversionRate();
                });

                function calculateConversionRate() {
                    const purchaseUnitId = $('#purchase_unit_id').val();
                    const consumptionUnitId = $('#consumption_unit_id').val();

                    if (!purchaseUnitId || !consumptionUnitId) {
                        $('#conversion_rate').val(1);
                        $('#conversionRateHelp').text('');
                        calculateConsumptionCost();
                        return;
                    }

                    if (purchaseUnitId === consumptionUnitId) {
                        $('#conversion_rate').val(1);
                        $('#conversionRateHelp').text('{{ __("Same unit selected") }}');
                        calculateConsumptionCost();
                        return;
                    }

                    const purchaseUnit = unitsData.find(u => u.id == purchaseUnitId);
                    const consumptionUnit = unitsData.find(u => u.id == consumptionUnitId);

                    if (!purchaseUnit || !consumptionUnit) {
                        $('#conversion_rate').val(1);
                        $('#conversionRateHelp').text('');
                        calculateConsumptionCost();
                        return;
                    }

                    // Calculate conversion rate based on unit relationships
                    let conversionRate = 1;
                    let helpText = '';

                    // Get base unit IDs
                    const purchaseBaseId = purchaseUnit.base_unit || purchaseUnit.id;
                    const consumptionBaseId = consumptionUnit.base_unit || consumptionUnit.id;

                    // Check if units are in the same family
                    if (purchaseBaseId === consumptionBaseId ||
                        purchaseUnit.id === consumptionBaseId ||
                        consumptionUnit.id === purchaseBaseId ||
                        purchaseUnit.base_unit === consumptionUnit.id ||
                        consumptionUnit.base_unit === purchaseUnit.id) {

                        // Convert purchase unit to base value
                        let purchaseToBase = 1;
                        if (purchaseUnit.base_unit) {
                            if (purchaseUnit.operator === '*') {
                                purchaseToBase = parseFloat(purchaseUnit.operator_value) || 1;
                            } else {
                                purchaseToBase = 1 / (parseFloat(purchaseUnit.operator_value) || 1);
                            }
                        }

                        // Convert base to consumption unit
                        let baseToConsumption = 1;
                        if (consumptionUnit.base_unit) {
                            if (consumptionUnit.operator === '*') {
                                baseToConsumption = 1 / (parseFloat(consumptionUnit.operator_value) || 1);
                            } else {
                                baseToConsumption = parseFloat(consumptionUnit.operator_value) || 1;
                            }
                        }

                        // If purchase unit is the base of consumption unit
                        // e.g., Purchase=Kg, Consumption=Gram (Gram has base_unit=Kg, operator=/, value=1000)
                        // 1 Gram = 1 Kg / 1000, so 1 Kg = 1000 Grams
                        if (consumptionUnit.base_unit === purchaseUnit.id) {
                            if (consumptionUnit.operator === '/') {
                                // 1 consumption = 1 base / value, so 1 base = value consumption
                                conversionRate = parseFloat(consumptionUnit.operator_value) || 1;
                            } else {
                                // 1 consumption = 1 base * value, so 1 base = 1/value consumption
                                conversionRate = 1 / (parseFloat(consumptionUnit.operator_value) || 1);
                            }
                        }
                        // If consumption unit is the base of purchase unit
                        // e.g., Purchase=Gram, Consumption=Kg (Gram has base_unit=Kg, operator=/, value=1000)
                        // 1 Gram = 1 Kg / 1000, so 1 Gram = 0.001 Kg
                        else if (purchaseUnit.base_unit === consumptionUnit.id) {
                            if (purchaseUnit.operator === '/') {
                                // 1 purchase = 1 base / value, so 1 purchase = 1/value base
                                conversionRate = 1 / (parseFloat(purchaseUnit.operator_value) || 1);
                            } else {
                                // 1 purchase = 1 base * value, so 1 purchase = value base
                                conversionRate = parseFloat(purchaseUnit.operator_value) || 1;
                            }
                        }
                        // Both have same base unit
                        else if (purchaseBaseId === consumptionBaseId) {
                            // Convert: purchase -> base -> consumption
                            // purchaseToBase: how many base units = 1 purchase unit
                            // baseToConsumption: how many consumption units = 1 base unit
                            conversionRate = purchaseToBase * baseToConsumption;
                        }

                        helpText = `1 ${purchaseUnit.name} = ${conversionRate} ${consumptionUnit.name}`;
                    } else {
                        // Different unit families - enable manual input
                        $('#conversion_rate').prop('readonly', false);
                        helpText = '{{ __("Different unit families - please enter manually") }}';
                        $('#enableConversionRate').hide();
                    }

                    $('#conversion_rate').val(conversionRate.toFixed(4));
                    $('#conversionRateHelp').text(helpText);
                    calculateConsumptionCost();
                }

                // Show current conversion help text on load
                (function() {
                    const purchaseUnit = unitsData.find(u => u.id == $('#purchase_unit_id').val());
                    const consumptionUnit = unitsData.find(u => u.id == $('#consumption_unit_id').val());
                    if (purchaseUnit && consumptionUnit) {
                        const rate = parseFloat($('#conversion_rate').val()) || 1;
                        $('#conversionRateHelp').text(`1 ${purchaseUnit.name} = ${rate} ${consumptionUnit.name}`);
                    }
                })();

                $.uploadPreview({
                    input_field: "#image-upload",
                    preview_box: "#image-preview",
                    label_field: "#image-label",
                    label_default: "Choose File",
                    label_selected: "Change File",
                    no_label: false
                });

            });
        })(jQuery);
    </script>
@endpush
