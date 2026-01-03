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
                                            <label for="purchase_unit_id">{{ __('Purchase Unit') }}<span
                                                    class="text-danger">*</span></label>
                                            <select name="purchase_unit_id" id="purchase_unit_id" class="form-control select2" required>
                                                <option value="">{{ __('Select Unit') }}</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}"
                                                        @if (old('purchase_unit_id', $product->purchase_unit_id) == $unit->id) selected @endif>
                                                        {{ $unit->name }} ({{ $unit->ShortName }})
                                                    </option>
                                                @endforeach
                                            </select>
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
                                                    class="form-control" value="{{ old('conversion_rate', $product->conversion_rate ?? 1) }}" required>
                                                <div class="input-group-text">
                                                    <i class="fa fa-question-circle text-info" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" title="{{ __('How many Consumption Unit is equal for 1 Purchase Unit') }}"></i>
                                                </div>
                                            </div>
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
