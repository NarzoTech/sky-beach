@extends('admin.layouts.master')
@section('title', __('View Menu Item'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ $item->name }}</h4>
                                <div>
                                    @adminCan('menu.item.edit')
                                    <a href="{{ route('admin.menu-item.edit', $item->id) }}" class="btn btn-warning">
                                        <i class="bx bx-edit"></i> {{ __('Edit') }}
                                    </a>
                                    @endadminCan
                                    <a href="{{ route('admin.menu-item.index') }}" class="btn btn-primary">
                                        <i class="bx bx-arrow-back"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="img-fluid rounded" style="max-height: 300px; width: 100%; object-fit: cover;">

                                        <div class="mt-3">
                                            <h5>{{ __('Status') }}</h5>
                                            <span class="badge {{ $item->status ? 'bg-success' : 'bg-danger' }}">
                                                {{ $item->status ? __('Active') : __('Inactive') }}
                                            </span>
                                            <span class="badge {{ $item->is_available ? 'bg-success' : 'bg-warning' }}">
                                                {{ $item->is_available ? __('Available') : __('Unavailable') }}
                                            </span>
                                            @if ($item->is_featured)
                                                <span class="badge bg-primary">{{ __('Featured') }}</span>
                                            @endif
                                        </div>

                                        <div class="mt-3 mb-3">
                                            <h5>{{ __('Dietary Info') }}</h5>
                                            @if ($item->is_vegetarian)
                                                <span class="badge bg-success">{{ __('Vegetarian') }}</span>
                                            @endif
                                            @if ($item->is_vegan)
                                                <span class="badge bg-success">{{ __('Vegan') }}</span>
                                            @endif
                                            @if ($item->is_spicy)
                                                <span class="badge bg-danger">{{ __('Spicy') }} ({{ $item->spice_level }}/5)</span>
                                            @endif
                                            @if ($item->calories)
                                                <span class="badge bg-info">{{ $item->calories }} {{ __('cal') }}</span>
                                            @endif
                                        </div>

                                        @if ($item->allergens && count($item->allergens) > 0)
                                            <div class="mt-3">
                                                <h5>{{ __('Allergens') }}</h5>
                                                @foreach ($item->allergens as $allergen)
                                                    <span class="badge bg-warning text-dark">{{ ucfirst($allergen) }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-lg-8">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="200">{{ __('Category') }}</th>
                                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('SKU') }}</th>
                                                <td>{{ $item->sku ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Base Price') }}</th>
                                                <td><strong>{{ number_format($item->base_price, 2) }}</strong></td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Cost Price') }}</th>
                                                <td>{{ number_format($item->cost_price, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Profit Margin') }}</th>
                                                <td>{{ $item->profit_margin }}%</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Preparation Time') }}</th>
                                                <td>{{ $item->preparation_time ? $item->preparation_time . ' min' : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Short Description') }}</th>
                                                <td>{{ $item->short_description ?? 'N/A' }}</td>
                                            </tr>
                                        </table>

                                        @if ($item->long_description)
                                            <div class="mt-3">
                                                <h5>{{ __('Full Description') }}</h5>
                                                <div class="border p-3 rounded">{!! $item->long_description !!}</div>
                                            </div>
                                        @endif

                                        <!-- Variants -->
                                        @if ($item->variants->count() > 0)
                                            <div class="mt-4">
                                                <h5>{{ __('Variants') }} ({{ $item->variants->count() }})</h5>
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Name') }}</th>
                                                            <th>{{ __('Price Adjustment') }}</th>
                                                            <th>{{ __('Final Price') }}</th>
                                                            <th>{{ __('Default') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($item->variants as $variant)
                                                            <tr>
                                                                <td>{{ $variant->name }}</td>
                                                                <td>{{ $variant->price_adjustment >= 0 ? '+' : '' }}{{ number_format($variant->price_adjustment, 2) }}</td>
                                                                <td>{{ number_format($variant->final_price, 2) }}</td>
                                                                <td>{!! $variant->is_default ? '<span class="badge bg-primary">Yes</span>' : 'No' !!}</td>
                                                                <td>{!! $variant->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif

                                        <!-- Add-ons -->
                                        @if ($item->addons->count() > 0)
                                            <div class="mt-4">
                                                <h5>{{ __('Add-ons') }} ({{ $item->addons->count() }})</h5>
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Name') }}</th>
                                                            <th>{{ __('Price') }}</th>
                                                            <th>{{ __('Max Qty') }}</th>
                                                            <th>{{ __('Required') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($item->addons as $addon)
                                                            <tr>
                                                                <td>{{ $addon->name }}</td>
                                                                <td>{{ number_format($addon->price, 2) }}</td>
                                                                <td>{{ $addon->pivot->max_quantity }}</td>
                                                                <td>{!! $addon->pivot->is_required ? '<span class="badge bg-danger">Yes</span>' : 'No' !!}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif

                                        <!-- Recipe/Ingredients -->
                                        @if ($item->recipes->count() > 0)
                                            <div class="mt-4">
                                                <h5>{{ __('Recipe / Ingredients') }} ({{ $item->recipes->count() }})</h5>
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Ingredient') }}</th>
                                                            <th>{{ __('Quantity') }}</th>
                                                            <th>{{ __('Unit') }}</th>
                                                            <th>{{ __('Cost') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $totalCost = 0; @endphp
                                                        @foreach ($item->recipes as $recipe)
                                                            @php $totalCost += $recipe->ingredient_cost; @endphp
                                                            <tr>
                                                                <td>{{ $recipe->product->name ?? 'N/A' }}</td>
                                                                <td>{{ $recipe->quantity_required }}</td>
                                                                <td>{{ $recipe->unit->name ?? 'N/A' }}</td>
                                                                <td>{{ number_format($recipe->ingredient_cost, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-secondary">
                                                            <th colspan="3">{{ __('Total Recipe Cost') }}</th>
                                                            <th>{{ number_format($totalCost, 2) }}</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @endif
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
