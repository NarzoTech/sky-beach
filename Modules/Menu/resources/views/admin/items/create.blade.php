@extends('admin.layouts.master')
@section('title', __('Add Menu Item'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Add Menu Item') }}</h4>
                                <div>
                                    <a href="{{ route('admin.menu-item.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.menu-item.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <!-- Basic Information -->
                                        <div class="col-lg-8">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Basic Information') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" id="name" required value="{{ old('name') }}">
                                                                @error('name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="slug">{{ __('Slug') }}</label>
                                                                <input type="text" name="slug" class="form-control" id="slug" value="{{ old('slug') }}" placeholder="auto-generated">
                                                                @error('slug')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="category_id">{{ __('Category') }}</label>
                                                                <select name="category_id" id="category_id" class="form-control select2">
                                                                    <option value="">{{ __('Select Category') }}</option>
                                                                    @foreach ($categories as $category)
                                                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('category_id')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="cuisine_type">{{ __('Cuisine Type') }}</label>
                                                                <input type="text" name="cuisine_type" class="form-control" id="cuisine_type" value="{{ old('cuisine_type') }}" placeholder="e.g., Italian, Thai, Indian">
                                                                @error('cuisine_type')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="sku">{{ __('SKU') }}</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="sku" class="form-control" id="sku" value="{{ old('sku') }}" placeholder="auto-generated">
                                                                    <div class="input-group-append">
                                                                        <button type="button" class="btn btn-outline-primary" id="generate-sku" title="{{ __('Generate SKU') }}">
                                                                            <i class="fas fa-sync-alt"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="short_description">{{ __('Short Description') }}</label>
                                                                <textarea name="short_description" class="form-control" id="short_description" rows="2">{{ old('short_description') }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="long_description">{{ __('Full Description') }}</label>
                                                                <textarea name="long_description" class="form-control summernote" id="long_description" rows="4">{{ old('long_description') }}</textarea>
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
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="base_price">{{ __('Base Price') }}<span class="text-danger">*</span></label>
                                                                <input type="number" name="base_price" class="form-control" id="base_price" required value="{{ old('base_price', 0) }}" step="0.01" min="0">
                                                                @error('base_price')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="discount_price">{{ __('Discount Price') }}</label>
                                                                <input type="number" name="discount_price" class="form-control" id="discount_price" value="{{ old('discount_price') }}" step="0.01" min="0">
                                                                <small class="text-muted">{{ __('Leave empty for no discount') }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="cost_price">{{ __('Cost Price') }}</label>
                                                                <input type="number" class="form-control bg-light" id="cost_price_display" value="0.00" step="0.01" readonly>
                                                                <small class="text-info"><i class="bx bx-info-circle"></i> {{ __('Auto-calculated') }}</small>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="preparation_time">{{ __('Prep Time (min)') }}</label>
                                                                <input type="number" name="preparation_time" class="form-control" id="preparation_time" value="{{ old('preparation_time') }}" min="0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Ingredients/Recipe -->
                                            <div class="card mt-3">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5><i class="fas fa-utensils mr-2"></i>{{ __('Ingredients (Recipe)') }}</h5>
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
                                                                <tr id="no-ingredients-row">
                                                                    <td colspan="4" class="text-center text-muted py-4">
                                                                        <i class="fas fa-info-circle mr-2"></i>{{ __('No ingredients added yet. Click "Add Ingredient" to add.') }}
                                                                    </td>
                                                                </tr>
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
                                                        {{ __('Add ingredients with the required quantity for making this menu item.') }}
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Dietary Information -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Dietary Information') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="calories">{{ __('Calories') }}</label>
                                                                <input type="number" name="calories" class="form-control" id="calories" value="{{ old('calories') }}" min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="is_vegetarian">{{ __('Vegetarian') }}</label>
                                                                <select name="is_vegetarian" id="is_vegetarian" class="form-control">
                                                                    <option value="0" {{ old('is_vegetarian', 0) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                    <option value="1" {{ old('is_vegetarian') == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="is_vegan">{{ __('Vegan') }}</label>
                                                                <select name="is_vegan" id="is_vegan" class="form-control">
                                                                    <option value="0" {{ old('is_vegan', 0) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                    <option value="1" {{ old('is_vegan') == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label for="is_spicy">{{ __('Spicy') }}</label>
                                                                <select name="is_spicy" id="is_spicy" class="form-control">
                                                                    <option value="0" {{ old('is_spicy', 0) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                    <option value="1" {{ old('is_spicy') == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="spice_level">{{ __('Spice Level (0-5)') }}</label>
                                                                <input type="number" name="spice_level" class="form-control" id="spice_level" value="{{ old('spice_level', 0) }}" min="0" max="5">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <label for="allergens">{{ __('Allergens') }}</label>
                                                                <select name="allergens[]" id="allergens" class="form-control select2" multiple>
                                                                    @foreach ($allergenOptions as $key => $label)
                                                                        <option value="{{ $key }}" {{ in_array($key, old('allergens', [])) ? 'selected' : '' }}>
                                                                            {{ $label }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sidebar -->
                                        <div class="col-lg-4">
                                            <!-- Thumbnail Image -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Thumbnail Image') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <input type="file" name="image" class="form-control" id="image" accept="image/*">
                                                        @error('image')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                        <small class="text-muted">{{ __('Main display image for listings') }}</small>
                                                    </div>
                                                    <div id="image-preview" class="mt-2"></div>
                                                </div>
                                            </div>

                                            <!-- Gallery Images -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Gallery Images') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <input type="file" name="gallery[]" class="form-control" id="gallery" accept="image/*" multiple>
                                                        @error('gallery.*')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                        <small class="text-muted">{{ __('Additional images for detail page (max 5)') }}</small>
                                                    </div>
                                                    <div id="gallery-preview" class="mt-2 d-flex flex-wrap gap-2"></div>
                                                </div>
                                            </div>

                                            <!-- Visibility -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Visibility') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="status">{{ __('Status') }}<span class="text-danger">*</span></label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="is_available">{{ __('Available') }}</label>
                                                        <select name="is_available" id="is_available" class="form-control">
                                                            <option value="1" {{ old('is_available', 1) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                            <option value="0" {{ old('is_available') == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="available_in_pos">{{ __('Show in POS') }}</label>
                                                                <select name="available_in_pos" id="available_in_pos" class="form-control">
                                                                    <option value="1" {{ old('available_in_pos', 1) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                    <option value="0" {{ old('available_in_pos') == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="available_in_website">{{ __('Show on Website') }}</label>
                                                                <select name="available_in_website" id="available_in_website" class="form-control">
                                                                    <option value="1" {{ old('available_in_website', 1) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                    <option value="0" {{ old('available_in_website') == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="display_order">{{ __('Display Order') }}</label>
                                                        <input type="number" name="display_order" class="form-control" id="display_order" value="{{ old('display_order', 0) }}" min="0">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Marketing -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Marketing Badges') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="is_featured">{{ __('Featured') }}</label>
                                                                <select name="is_featured" id="is_featured" class="form-control">
                                                                    <option value="0" {{ old('is_featured', 0) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                    <option value="1" {{ old('is_featured') == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="form-group">
                                                                <label for="is_new">{{ __('New') }}</label>
                                                                <select name="is_new" id="is_new" class="form-control">
                                                                    <option value="0" {{ old('is_new', 0) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                    <option value="1" {{ old('is_new') == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="is_popular">{{ __('Popular') }}</label>
                                                                <select name="is_popular" id="is_popular" class="form-control">
                                                                    <option value="0" {{ old('is_popular', 0) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                    <option value="1" {{ old('is_popular') == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="card mt-3">
                                                <div class="card-body text-center">
                                                    <x-admin.save-button :text="__('Save Menu Item')"></x-admin.save-button>
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
        (function($) {
            "use strict";

            // Ingredients data for ingredient selection
            var ingredients = @json($ingredients ?? []);
            var units = @json($units ?? []);
            var ingredientIndex = 0;

            $(document).ready(function() {
                // Auto-generate slug from name
                $('[name="name"]').on('input', function() {
                    var name = $(this).val();
                    var slug = convertToSlug(name);
                    $("[name='slug']").val(slug);
                });

                // Generate SKU button
                $('#generate-sku').on('click', function() {
                    var prefix = 'MENU-';
                    var timestamp = Date.now().toString(36).toUpperCase();
                    var random = Math.random().toString(36).substring(2, 6).toUpperCase();
                    var sku = prefix + timestamp.slice(-4) + random;
                    $('#sku').val(sku);
                });

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

                // Gallery preview
                $('#gallery').on('change', function() {
                    var files = this.files;
                    $('#gallery-preview').html('');
                    if (files.length > 5) {
                        alert('{{ __("Maximum 5 gallery images allowed") }}');
                        this.value = '';
                        return;
                    }
                    for (var i = 0; i < files.length; i++) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#gallery-preview').append('<img src="' + e.target.result + '" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">');
                        }
                        reader.readAsDataURL(files[i]);
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
            });

            function addIngredientRow() {
                $('#no-ingredients-row').hide();

                var ingredientOptions = '<option value="">{{ __("Select Ingredient") }}</option>';
                ingredients.forEach(function(ingredient) {
                    var unitName = ingredient.consumption_unit ? ingredient.consumption_unit.name : (ingredient.unit ? ingredient.unit.name : '');
                    var cost = ingredient.consumption_unit_cost || ingredient.cost || 0;
                    ingredientOptions += '<option value="' + ingredient.id + '" data-cost="' + cost + '" data-unit="' + unitName + '" data-unit-id="' + (ingredient.consumption_unit_id || ingredient.unit_id || '') + '">' + ingredient.name + ' (' + cost + '/' + unitName + ')</option>';
                });

                var row = `
                    <tr class="ingredient-row">
                        <td>
                            <select name="recipes[${ingredientIndex}][ingredient_id]" class="form-control ingredient-select ingredient-product" required>
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

                // Initialize select2 for the new ingredient row with search
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
                $('#cost_price_display').val(total.toFixed(2));
            }

            function checkEmptyIngredients() {
                if ($('.ingredient-row').length === 0) {
                    $('#no-ingredients-row').show();
                }
            }
        })(jQuery);
    </script>
@endpush
