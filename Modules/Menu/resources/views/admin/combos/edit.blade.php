@extends('admin.layouts.master')
@section('title', __('Edit Combo Deal'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Edit Combo Deal') }}: {{ $combo->name }}</h4>
                                <div>
                                    <a href="{{ route('admin.combo.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.combo.update', $combo->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <!-- Basic Information -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Basic Information') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label for="name">{{ __('Combo Name') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" id="name" required value="{{ old('name', $combo->name) }}">
                                                                @error('name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="slug">{{ __('Slug') }}</label>
                                                                <input type="text" name="slug" class="form-control" id="slug" value="{{ old('slug', $combo->slug) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="description">{{ __('Description') }}</label>
                                                                <textarea name="description" class="form-control" id="description" rows="3">{{ old('description', $combo->description) }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pricing -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Pricing') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="combo_price">{{ __('Combo Price') }}<span class="text-danger">*</span></label>
                                                                <input type="number" name="combo_price" class="form-control" id="combo_price" required value="{{ old('combo_price', $combo->combo_price) }}" step="0.01" min="0">
                                                                @error('combo_price')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="discount_type">{{ __('Discount Type') }}</label>
                                                                <select name="discount_type" id="discount_type" class="form-control">
                                                                    <option value="">{{ __('None') }}</option>
                                                                    <option value="percentage" {{ old('discount_type', $combo->discount_type) === 'percentage' ? 'selected' : '' }}>{{ __('Percentage') }}</option>
                                                                    <option value="fixed" {{ old('discount_type', $combo->discount_type) === 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="discount_value">{{ __('Discount Value') }}</label>
                                                                <input type="number" name="discount_value" class="form-control" id="discount_value" value="{{ old('discount_value', $combo->discount_value) }}" step="0.01" min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Combo Items -->
                                            <div class="card mt-3">
                                                <div class="card-header d-flex justify-content-between">
                                                    <h5>{{ __('Combo Items') }}</h5>
                                                    <button type="button" class="btn btn-sm btn-primary" id="addItem">
                                                        <i class="fa fa-plus"></i> {{ __('Add Item') }}
                                                    </button>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id="itemsTable">
                                                            <thead>
                                                                <tr>
                                                                    <th width="40%">{{ __('Menu Item') }}</th>
                                                                    <th width="25%">{{ __('Variant') }}</th>
                                                                    <th width="15%">{{ __('Quantity') }}</th>
                                                                    <th width="10%">{{ __('Price') }}</th>
                                                                    <th width="10%">{{ __('Action') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="itemsList">
                                                                @foreach ($combo->items as $index => $comboItem)
                                                                    <tr class="item-row">
                                                                        <td>
                                                                            <select name="items[{{ $index }}][menu_item_id]" class="form-control item-select" required>
                                                                                <option value="">{{ __('Select Item') }}</option>
                                                                                @foreach ($menuItems as $item)
                                                                                    <option value="{{ $item->id }}"
                                                                                        data-price="{{ $item->base_price }}"
                                                                                        data-variants='@json($item->variants)'
                                                                                        {{ $comboItem->menu_item_id == $item->id ? 'selected' : '' }}>
                                                                                        {{ $item->name }} ({{ number_format($item->base_price, 2) }})
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select name="items[{{ $index }}][variant_id]" class="form-control variant-select">
                                                                                <option value="">{{ __('Default') }}</option>
                                                                                @if ($comboItem->menuItem)
                                                                                    @foreach ($comboItem->menuItem->variants as $variant)
                                                                                        <option value="{{ $variant->id }}"
                                                                                            data-adjustment="{{ $variant->price_adjustment }}"
                                                                                            {{ $comboItem->variant_id == $variant->id ? 'selected' : '' }}>
                                                                                            {{ $variant->name }} ({{ $variant->price_adjustment >= 0 ? '+' : '' }}{{ number_format($variant->price_adjustment, 2) }})
                                                                                        </option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity-input" value="{{ $comboItem->quantity }}" min="1">
                                                                        </td>
                                                                        @php
                                                                            $itemPrice = ($comboItem->menuItem->base_price ?? 0) + ($comboItem->variant->price_adjustment ?? 0);
                                                                            $rowTotal = $itemPrice * $comboItem->quantity;
                                                                        @endphp
                                                                        <td class="item-price">{{ number_format($rowTotal, 2) }}</td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-danger remove-item">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                                <tr class="table-secondary">
                                                                    <th colspan="3">{{ __('Original Total') }}</th>
                                                                    <th id="originalTotal">{{ number_format($combo->original_price, 2) }}</th>
                                                                    <th></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Duration -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Duration') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="start_date">{{ __('Start Date') }}</label>
                                                                <input type="date" name="start_date" class="form-control" id="start_date" value="{{ old('start_date', $combo->start_date?->format('Y-m-d')) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="end_date">{{ __('End Date') }}</label>
                                                                <input type="date" name="end_date" class="form-control" id="end_date" value="{{ old('end_date', $combo->end_date?->format('Y-m-d')) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <!-- Image -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Image') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    @if ($combo->image)
                                                        <div class="mb-3">
                                                            <img src="{{ $combo->image_url }}" alt="{{ $combo->name }}" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                                                        </div>
                                                    @endif
                                                    <div class="form-group">
                                                        <input type="file" name="image" class="form-control" id="image" accept="image/*">
                                                    </div>
                                                    <div id="image-preview" class="mt-2"></div>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Status') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="status">{{ __('Status') }}<span class="text-danger">*</span></label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="1" {{ old('status', $combo->status) == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                            <option value="0" {{ old('status', $combo->status) == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="is_active">{{ __('Running') }}</label>
                                                        <select name="is_active" id="is_active" class="form-control">
                                                            <option value="1" {{ old('is_active', $combo->is_active) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                            <option value="0" {{ old('is_active', $combo->is_active) == 0 ? 'selected' : '' }}>{{ __('No (Paused)') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="display_order">{{ __('Display Order') }}</label>
                                                        <input type="number" name="display_order" class="form-control" id="display_order" value="{{ old('display_order', $combo->display_order) }}" min="0">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Savings Preview -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Savings Preview') }}</h5>
                                                </div>
                                                <div class="card-body text-center">
                                                    <p class="text-muted mb-1">{{ __('Original Price') }}</p>
                                                    <h5 class="text-muted text-decoration-line-through" id="previewOriginal">{{ number_format($combo->original_price, 2) }}</h5>
                                                    <p class="text-muted mb-1">{{ __('Combo Price') }}</p>
                                                    <h3 class="text-success" id="previewCombo">{{ number_format($combo->combo_price, 2) }}</h3>
                                                    <p class="mb-0">
                                                        <span class="badge bg-success" id="previewSavings">{{ number_format($combo->savings_percentage, 0) }}% off</span>
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="card mt-3">
                                                <div class="card-body text-center">
                                                    <x-admin.save-button :text="__('Update Combo')"></x-admin.save-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            'use strict';

            var menuItems = @json($menuItems);
            var rowIndex = {{ $combo->items->count() }};

            // Image preview
            $('#image').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#image-preview').html('<img src="' + e.target.result + '" style="max-width: 100%; max-height: 200px; border-radius: 5px;">');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Add item row
            $('#addItem').on('click', function() {
                var itemOptions = '<option value="">{{ __("Select Item") }}</option>';
                menuItems.forEach(function(item) {
                    itemOptions += '<option value="' + item.id + '" data-price="' + item.base_price + '" data-variants=\'' + JSON.stringify(item.variants) + '\'>' + item.name + ' (' + parseFloat(item.base_price).toFixed(2) + ')</option>';
                });

                var newRow = `
                    <tr class="item-row">
                        <td>
                            <select name="items[${rowIndex}][menu_item_id]" class="form-control item-select" required>
                                ${itemOptions}
                            </select>
                        </td>
                        <td>
                            <select name="items[${rowIndex}][variant_id]" class="form-control variant-select">
                                <option value="">{{ __('Default') }}</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[${rowIndex}][quantity]" class="form-control quantity-input" value="1" min="1">
                        </td>
                        <td class="item-price">0.00</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-item">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#itemsList').append(newRow);
                rowIndex++;
            });

            // Remove item row
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
                updateTotals();
            });

            // Handle item selection
            $(document).on('change', '.item-select', function() {
                var row = $(this).closest('tr');
                var selected = $(this).find(':selected');
                var variants = selected.data('variants') || [];
                var variantSelect = row.find('.variant-select');

                // Clear and populate variants
                variantSelect.html('<option value="">{{ __("Default") }}</option>');
                variants.forEach(function(variant) {
                    var priceAdj = parseFloat(variant.price_adjustment) || 0;
                    var priceText = priceAdj >= 0 ? '+' + priceAdj.toFixed(2) : priceAdj.toFixed(2);
                    variantSelect.append('<option value="' + variant.id + '" data-adjustment="' + priceAdj + '">' + variant.name + ' (' + priceText + ')</option>');
                });

                updateRowPrice(row);
            });

            // Handle variant selection
            $(document).on('change', '.variant-select', function() {
                var row = $(this).closest('tr');
                updateRowPrice(row);
            });

            // Handle quantity change
            $(document).on('change', '.quantity-input', function() {
                var row = $(this).closest('tr');
                updateRowPrice(row);
            });

            // Handle combo price change
            $('#combo_price').on('input', function() {
                updateSavingsPreview();
            });

            function updateRowPrice(row) {
                var itemSelect = row.find('.item-select');
                var variantSelect = row.find('.variant-select');
                var quantity = parseInt(row.find('.quantity-input').val()) || 1;

                var basePrice = parseFloat(itemSelect.find(':selected').data('price')) || 0;
                var variantAdj = parseFloat(variantSelect.find(':selected').data('adjustment')) || 0;
                var totalPrice = (basePrice + variantAdj) * quantity;

                row.find('.item-price').text(totalPrice.toFixed(2));
                updateTotals();
            }

            function updateTotals() {
                var total = 0;
                $('.item-price').each(function() {
                    total += parseFloat($(this).text()) || 0;
                });
                $('#originalTotal').text(total.toFixed(2));
                $('#previewOriginal').text(total.toFixed(2));
                updateSavingsPreview();
            }

            function updateSavingsPreview() {
                var original = parseFloat($('#originalTotal').text()) || 0;
                var combo = parseFloat($('#combo_price').val()) || 0;

                $('#previewCombo').text(combo.toFixed(2));

                if (original > 0 && combo < original) {
                    var savings = ((original - combo) / original) * 100;
                    $('#previewSavings').text(savings.toFixed(0) + '% off').removeClass('bg-secondary').addClass('bg-success');
                } else {
                    $('#previewSavings').text('0% off').removeClass('bg-success').addClass('bg-secondary');
                }
            }
        });
    </script>
@endpush
