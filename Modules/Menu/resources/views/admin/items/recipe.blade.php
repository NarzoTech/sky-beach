@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Manage Recipe') }} - {{ $item->name }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Manage Recipe') }}: {{ $item->name }}</h4>
                                <div>
                                    <a href="{{ route('admin.menu-item.edit', $item->id) }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back to Item') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between">
                                                <h5>{{ __('Ingredients') }}</h5>
                                                <button type="button" class="btn btn-sm btn-primary" id="addIngredient">
                                                    <i class="fa fa-plus"></i> {{ __('Add Ingredient') }}
                                                </button>
                                            </div>
                                            <div class="card-body">
                                                <form id="recipeForm">
                                                    @csrf
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id="ingredientsTable">
                                                            <thead>
                                                                <tr>
                                                                    <th width="35%">{{ __('Ingredient') }}</th>
                                                                    <th width="15%">{{ __('Quantity') }}</th>
                                                                    <th width="15%">{{ __('Unit') }}</th>
                                                                    <th width="15%">{{ __('Cost') }}</th>
                                                                    <th width="20%">{{ __('Notes') }}</th>
                                                                    <th width="10%">{{ __('Action') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="ingredientsList">
                                                                @foreach ($item->recipes as $index => $recipe)
                                                                    <tr class="ingredient-row">
                                                                        <td>
                                                                            <select name="recipes[{{ $index }}][ingredient_id]" class="form-control select2 ingredient-select" required>
                                                                                <option value="">{{ __('Select Ingredient') }}</option>
                                                                                @foreach ($ingredients as $ingredient)
                                                                                    <option value="{{ $ingredient->id }}"
                                                                                        data-cost="{{ $ingredient->consumption_unit_cost ?? $ingredient->cost }}"
                                                                                        data-unit="{{ $ingredient->consumptionUnit->ShortName ?? '' }}"
                                                                                        {{ $recipe->ingredient_id == $ingredient->id ? 'selected' : '' }}>
                                                                                        {{ $ingredient->name }} ({{ currency($ingredient->consumption_unit_cost ?? $ingredient->cost) }}/{{ $ingredient->consumptionUnit->ShortName ?? 'unit' }})
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="number" name="recipes[{{ $index }}][quantity_required]"
                                                                                class="form-control quantity-input"
                                                                                value="{{ $recipe->quantity_required }}"
                                                                                step="0.0001" min="0" required>
                                                                        </td>
                                                                        <td>
                                                                            <select name="recipes[{{ $index }}][unit_id]" class="form-control">
                                                                                <option value="">{{ __('Select') }}</option>
                                                                                @foreach ($units as $unit)
                                                                                    <option value="{{ $unit->id }}" {{ $recipe->unit_id == $unit->id ? 'selected' : '' }}>
                                                                                        {{ $unit->name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </td>
                                                                        <td class="ingredient-cost">{{ number_format($recipe->ingredient_cost, 2) }}</td>
                                                                        <td>
                                                                            <input type="text" name="recipes[{{ $index }}][notes]"
                                                                                class="form-control" value="{{ $recipe->notes }}"
                                                                                placeholder="Optional notes">
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-sm btn-danger remove-ingredient">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                                <tr class="table-secondary">
                                                                    <th colspan="3">{{ __('Total Recipe Cost') }}</th>
                                                                    <th id="totalCost">{{ number_format($item->calculateCostFromRecipe(), 2) }}</th>
                                                                    <th colspan="2"></th>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>

                                                    <div class="text-center mt-3">
                                                        <button type="submit" class="btn btn-success btn-lg">
                                                            <i class="fa fa-save"></i> {{ __('Save Recipe') }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('Item Information') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="img-fluid rounded mb-3">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>{{ __('Base Price') }}</th>
                                                        <td>{{ number_format($item->base_price, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('Current Cost') }}</th>
                                                        <td>{{ number_format($item->cost_price, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('Recipe Cost') }}</th>
                                                        <td id="recipeCostDisplay">{{ number_format($item->calculateCostFromRecipe(), 2) }}</td>
                                                    </tr>
                                                    <tr class="table-info">
                                                        <th>{{ __('Profit Margin') }}</th>
                                                        <td id="profitMarginDisplay">{{ $item->profit_margin }}%</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h5>{{ __('Tips') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li><i class="fa fa-info-circle text-info"></i> {{ __('Select ingredients used in this menu item') }}</li>
                                                    <li class="mt-2"><i class="fa fa-info-circle text-info"></i> {{ __('Cost is calculated from ingredient consumption unit cost') }}</li>
                                                    <li class="mt-2"><i class="fa fa-info-circle text-info"></i> {{ __('Stock will be deducted when menu item is sold') }}</li>
                                                </ul>
                                            </div>
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

@push('js')
    <script>
        $(document).ready(function() {
            'use strict';
            var rowIndex = {{ $item->recipes->count() }};
            var basePrice = {{ $item->base_price }};

            // Add ingredient row
            $('#addIngredient').on('click', function() {
                var newRow = `
                    <tr class="ingredient-row">
                        <td>
                            <select name="recipes[${rowIndex}][ingredient_id]" class="form-control ingredient-select" required>
                                <option value="">{{ __('Select Ingredient') }}</option>
                                @foreach ($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}" data-cost="{{ $ingredient->consumption_unit_cost ?? $ingredient->cost }}">
                                        {{ $ingredient->name }} ({{ currency($ingredient->consumption_unit_cost ?? $ingredient->cost) }}/{{ $ingredient->consumptionUnit->ShortName ?? 'unit' }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="recipes[${rowIndex}][quantity_required]"
                                class="form-control quantity-input" value="1" step="0.0001" min="0" required>
                        </td>
                        <td>
                            <select name="recipes[${rowIndex}][unit_id]" class="form-control">
                                <option value="">{{ __('Select') }}</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="ingredient-cost">0.00</td>
                        <td>
                            <input type="text" name="recipes[${rowIndex}][notes]" class="form-control" placeholder="Optional notes">
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger remove-ingredient">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#ingredientsList').append(newRow);
                rowIndex++;
                updateTotalCost();
            });

            // Remove ingredient row
            $(document).on('click', '.remove-ingredient', function() {
                $(this).closest('tr').remove();
                updateTotalCost();
            });

            // Update cost on ingredient or quantity change
            $(document).on('change', '.ingredient-select, .quantity-input', function() {
                var row = $(this).closest('tr');
                var ingredientSelect = row.find('.ingredient-select');
                var quantity = parseFloat(row.find('.quantity-input').val()) || 0;
                var cost = parseFloat(ingredientSelect.find(':selected').data('cost')) || 0;
                var totalCost = cost * quantity;
                row.find('.ingredient-cost').text(totalCost.toFixed(2));
                updateTotalCost();
            });

            function updateTotalCost() {
                var total = 0;
                $('.ingredient-cost').each(function() {
                    total += parseFloat($(this).text()) || 0;
                });
                $('#totalCost').text(total.toFixed(2));
                $('#recipeCostDisplay').text(total.toFixed(2));

                // Update profit margin
                if (basePrice > 0) {
                    var margin = ((basePrice - total) / basePrice) * 100;
                    $('#profitMarginDisplay').text(margin.toFixed(2) + '%');
                }
            }

            // Save recipe
            $('#recipeForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('admin.menu-item.recipe.save', $item->id) }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong');
                    }
                });
            });
        });
    </script>
@endpush
