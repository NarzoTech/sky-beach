@extends('admin.layouts.master')
@section('title', __('Edit Menu Add-on'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="card mb-3 page-title-card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="section_title">{{ __('Edit Menu Add-on') }}: {{ $addon->name }}</h4>
                        <a href="{{ route('admin.menu-addon.index') }}" class="btn btn-primary">
                            <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.menu-addon.update', $addon->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Add-on Information') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" id="name" required value="{{ old('name', $addon->name) }}">
                                                                @error('name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="price">{{ __('Price') }}<span class="text-danger">*</span></label>
                                                                <input type="number" name="price" class="form-control" id="price" required value="{{ old('price', $addon->price) }}" step="0.01" min="0">
                                                                @error('price')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="description">{{ __('Description') }}</label>
                                                                <textarea name="description" class="form-control" id="description" rows="3">{{ old('description', $addon->description) }}</textarea>
                                                                @error('description')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Ingredients/Recipe -->
                                            <div class="card mt-3">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5><i class="bx bx-food-menu me-2"></i> {{ __('Ingredients (Recipe)') }}</h5>
                                                    <button type="button" class="btn btn-sm btn-primary" id="add-ingredient">
                                                        <i class="fas fa-plus"></i> {{ __('Add Ingredient') }}
                                                    </button>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id="ingredients-table">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th width="40%">{{ __('Ingredient') }}</th>
                                                                    <th width="25%">{{ __('Quantity') }}</th>
                                                                    <th width="15%">{{ __('Cost') }}</th>
                                                                    <th width="10%">{{ __('Action') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ingredients-body">
                                                                @forelse($addon->recipes as $index => $recipe)
                                                                    @php
                                                                        $ingredient = $recipe->ingredient;
                                                                        $unitShortName = $ingredient?->consumptionUnit?->ShortName ?? $ingredient?->unit?->ShortName ?? '-';
                                                                        $cost = $ingredient?->consumption_unit_cost ?? $ingredient?->cost ?? 0;
                                                                    @endphp
                                                                    <tr class="ingredient-row">
                                                                        <td>
                                                                            <select name="recipes[{{ $index }}][ingredient_id]" class="form-control select2 ingredient-select ingredient-product" required>
                                                                                <option value="">{{ __('Select Ingredient') }}</option>
                                                                                @foreach($ingredients as $ing)
                                                                                    @php
                                                                                        $ingUnitShort = $ing->consumptionUnit?->ShortName ?? $ing->unit?->ShortName ?? '';
                                                                                        $ingCost = $ing->consumption_unit_cost ?? $ing->cost ?? 0;
                                                                                    @endphp
                                                                                    <option value="{{ $ing->id }}"
                                                                                        data-cost="{{ $ingCost }}"
                                                                                        data-unit="{{ $ingUnitShort }}"
                                                                                        data-unit-id="{{ $ing->consumption_unit_id ?? $ing->unit_id ?? '' }}"
                                                                                        {{ $recipe->ingredient_id == $ing->id ? 'selected' : '' }}>
                                                                                        {{ $ing->name }} ({{ $ingCost }}/{{ $ingUnitShort }})
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group">
                                                                                <input type="number" name="recipes[{{ $index }}][quantity_required]" class="form-control ingredient-quantity" step="0.01" min="0.01" value="{{ $recipe->quantity_required }}" required>
                                                                                <input type="hidden" name="recipes[{{ $index }}][unit_id]" class="ingredient-unit-id" value="{{ $recipe->unit_id }}">
                                                                                <div class="input-group-append">
                                                                                    <span class="input-group-text ingredient-unit-label" style="min-width: 50px;">{{ $unitShortName }}</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="ingredient-cost">{{ number_format($cost * $recipe->quantity_required, 2) }}</td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-danger remove-ingredient">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @empty
                                                                    <tr id="no-ingredients-row">
                                                                        <td colspan="4" class="text-center text-muted py-4">
                                                                            <i class="fas fa-info-circle mr-2"></i>{{ __('No ingredients added yet. Click "Add Ingredient" to add.') }}
                                                                        </td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                            <tfoot class="bg-light">
                                                                <tr>
                                                                    <th colspan="2" class="text-right">{{ __('Total Ingredient Cost:') }}</th>
                                                                    <th id="total-ingredient-cost">0.00</th>
                                                                    <th></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-lightbulb mr-1"></i>
                                                        {{ __('Add ingredients with the required quantity for making this add-on. Stock will be deducted when this add-on is sold.') }}
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Menu Items Using This Add-on -->
                                            @if ($addon->menuItems->count() > 0)
                                                <div class="card mt-3">
                                                    <div class="card-header">
                                                        <h5>{{ __('Used in Menu Items') }} ({{ $addon->menuItems->count() }})</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ __('Item') }}</th>
                                                                        <th>{{ __('Max Qty') }}</th>
                                                                        <th>{{ __('Required') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($addon->menuItems as $item)
                                                                        <tr>
                                                                            <td>
                                                                                <a href="{{ route('admin.menu-item.edit', $item->id) }}">
                                                                                    {{ $item->name }}
                                                                                </a>
                                                                            </td>
                                                                            <td>{{ $item->pivot->max_quantity }}</td>
                                                                            <td>
                                                                                @if ($item->pivot->is_required)
                                                                                    <span class="badge bg-danger">{{ __('Yes') }}</span>
                                                                                @else
                                                                                    {{ __('No') }}
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Image') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div id="image-preview" class="mb-3">
                                                        @if ($addon->image)
                                                            <img src="{{ asset($addon->image) }}" alt="{{ $addon->name }}" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                                                        @endif
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="file" name="image" class="form-control" id="image" accept="image/*">
                                                        @error('image')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Status') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="status">{{ __('Status') }}<span class="text-danger">*</span></label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="1" {{ old('status', $addon->status) == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                            <option value="0" {{ old('status', $addon->status) == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mt-3">
                                                <div class="card-body text-center">
                                                    <x-admin.save-button :text="__('Update Add-on')"></x-admin.save-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script>
        (function($) {
            "use strict";

            var ingredients = @json($ingredients ?? []);
            var ingredientIndex = {{ $addon->recipes->count() }};

            $(document).ready(function() {
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

                // Add ingredient row
                $('#add-ingredient').on('click', function() {
                    addIngredientRow();
                });

                // Remove ingredient row
                $(document).on('click', '.remove-ingredient', function() {
                    $(this).closest('tr').remove();
                    calculateTotalCost();
                    checkEmptyIngredients();
                });

                // Calculate cost when quantity or product changes
                $(document).on('change', '.ingredient-product, .ingredient-quantity', function() {
                    var row = $(this).closest('tr');
                    calculateRowCost(row);
                    calculateTotalCost();
                });

                // Initial calculation for existing rows
                calculateTotalCost();
            });

            function addIngredientRow() {
                $('#no-ingredients-row').hide();

                var ingredientOptions = '<option value="">{{ __("Select Ingredient") }}</option>';
                ingredients.forEach(function(ingredient) {
                    var unitShort = ingredient.consumption_unit ? (ingredient.consumption_unit.ShortName || ingredient.consumption_unit.name) : (ingredient.unit ? (ingredient.unit.ShortName || ingredient.unit.name) : '');
                    var cost = ingredient.consumption_unit_cost || ingredient.cost || 0;
                    ingredientOptions += '<option value="' + ingredient.id + '" data-cost="' + cost + '" data-unit="' + unitShort + '" data-unit-id="' + (ingredient.consumption_unit_id || ingredient.unit_id || '') + '">' + ingredient.name + ' (' + cost + '/' + unitShort + ')</option>';
                });

                var row = `
                    <tr class="ingredient-row">
                        <td>
                            <select name="recipes[${ingredientIndex}][ingredient_id]" class="form-control select2 ingredient-select ingredient-product" required>
                                ${ingredientOptions}
                            </select>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" name="recipes[${ingredientIndex}][quantity_required]" class="form-control ingredient-quantity" step="0.01" min="0.01" value="1" required>
                                <input type="hidden" name="recipes[${ingredientIndex}][unit_id]" class="ingredient-unit-id">
                                <div class="input-group-append">
                                    <span class="input-group-text ingredient-unit-label" style="min-width: 50px;">-</span>
                                </div>
                            </div>
                        </td>
                        <td class="ingredient-cost">0.00</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-ingredient">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#ingredients-body').append(row);
                ingredientIndex++;

                $('#ingredients-body tr:last .ingredient-select').select2({
                    width: '100%',
                    placeholder: '{{ __("Search ingredient...") }}',
                    allowClear: true
                });
            }

            function calculateRowCost(row) {
                var ingredientSelect = row.find('.ingredient-product');
                var quantityInput = row.find('.ingredient-quantity');
                var costCell = row.find('.ingredient-cost');
                var unitLabel = row.find('.ingredient-unit-label');
                var unitIdInput = row.find('.ingredient-unit-id');

                var selectedOption = ingredientSelect.find(':selected');
                var ingredientCost = parseFloat(selectedOption.data('cost')) || 0;
                var unitName = selectedOption.data('unit') || '-';
                var unitId = selectedOption.data('unit-id') || '';
                var quantity = parseFloat(quantityInput.val()) || 0;
                var totalCost = ingredientCost * quantity;

                costCell.text(totalCost.toFixed(2));
                unitLabel.text(unitName);
                unitIdInput.val(unitId);
            }

            function calculateTotalCost() {
                var total = 0;
                $('.ingredient-cost').each(function() {
                    total += parseFloat($(this).text()) || 0;
                });
                $('#total-ingredient-cost').text(total.toFixed(2));
            }

            function checkEmptyIngredients() {
                if ($('.ingredient-row').length === 0) {
                    $('#no-ingredients-row').show();
                }
            }
        })(jQuery);
    </script>
@endpush
