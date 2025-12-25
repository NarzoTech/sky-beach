@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Branch Pricing') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Branch-Specific Pricing') }}</h4>
                            </div>
                            <div class="card-body">
                                <!-- Branch Selection -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __('Select Branch') }}</label>
                                            <select id="branchSelect" class="form-control select2">
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                                                        {{ $branch->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-info btn-block" id="loadPrices">
                                                <i class="fa fa-refresh"></i> {{ __('Load Prices') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if ($branches->isEmpty())
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i> {{ __('No branches (warehouses) found. Please create warehouses first.') }}
                                    </div>
                                @else
                                    <form id="pricingForm">
                                        @csrf
                                        <input type="hidden" name="branch_id" id="branchIdInput" value="{{ $selectedBranch }}">

                                        <div class="alert alert-info mb-3">
                                            <i class="fa fa-info-circle"></i> {{ __('Leave fields empty to use the default price. Only enter values for items you want to override.') }}
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>{{ __('Menu Item') }}</th>
                                                        <th>{{ __('Variant') }}</th>
                                                        <th width="150">{{ __('Default Price') }}</th>
                                                        <th width="150">{{ __('Branch Price') }}</th>
                                                        <th width="100">{{ __('Diff') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($menuItems as $item)
                                                        @php
                                                            $priceKey = $item->id . '_0';
                                                            $branchPrice = $branchPrices[$priceKey] ?? null;
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $item->name }}</strong>
                                                                <br><small class="text-muted">{{ $item->category->name ?? 'N/A' }}</small>
                                                            </td>
                                                            <td><em>{{ __('Base Item') }}</em></td>
                                                            <td>{{ number_format($item->base_price, 2) }}</td>
                                                            <td>
                                                                <input type="number" name="prices[{{ $priceKey }}]"
                                                                    class="form-control form-control-sm price-input"
                                                                    value="{{ $branchPrice ? $branchPrice->price : '' }}"
                                                                    step="0.01" min="0"
                                                                    data-default="{{ $item->base_price }}"
                                                                    placeholder="{{ number_format($item->base_price, 2) }}">
                                                            </td>
                                                            <td class="price-diff">
                                                                @if ($branchPrice)
                                                                    @php $diff = $branchPrice->price - $item->base_price; @endphp
                                                                    <span class="{{ $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : '') }}">
                                                                        {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @foreach ($item->variants as $variant)
                                                            @php
                                                                $variantPriceKey = $item->id . '_' . $variant->id;
                                                                $defaultPrice = $item->base_price + $variant->price_adjustment;
                                                                $variantBranchPrice = $branchPrices[$variantPriceKey] ?? null;
                                                            @endphp
                                                            <tr class="table-light">
                                                                <td></td>
                                                                <td>
                                                                    <i class="fa fa-arrow-right text-muted"></i>
                                                                    {{ $variant->name }}
                                                                    <small class="text-muted">({{ $variant->price_adjustment >= 0 ? '+' : '' }}{{ number_format($variant->price_adjustment, 2) }})</small>
                                                                </td>
                                                                <td>{{ number_format($defaultPrice, 2) }}</td>
                                                                <td>
                                                                    <input type="number" name="prices[{{ $variantPriceKey }}]"
                                                                        class="form-control form-control-sm price-input"
                                                                        value="{{ $variantBranchPrice ? $variantBranchPrice->price : '' }}"
                                                                        step="0.01" min="0"
                                                                        data-default="{{ $defaultPrice }}"
                                                                        placeholder="{{ number_format($defaultPrice, 2) }}">
                                                                </td>
                                                                <td class="price-diff">
                                                                    @if ($variantBranchPrice)
                                                                        @php $diff = $variantBranchPrice->price - $defaultPrice; @endphp
                                                                        <span class="{{ $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : '') }}">
                                                                            {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <i class="fa fa-save"></i> {{ __('Save Branch Prices') }}
                                            </button>
                                        </div>
                                    </form>
                                @endif
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

            // Load prices for selected branch
            $('#loadPrices, #branchSelect').on('change click', function(e) {
                if (e.type === 'change' || $(this).is('#loadPrices')) {
                    var branchId = $('#branchSelect').val();
                    window.location.href = "{{ route('admin.branch-menu.pricing') }}?branch_id=" + branchId;
                }
            });

            // Update diff on price input change
            $('.price-input').on('input', function() {
                var row = $(this).closest('tr');
                var defaultPrice = parseFloat($(this).data('default')) || 0;
                var branchPrice = parseFloat($(this).val());
                var diffCell = row.find('.price-diff');

                if ($(this).val() === '' || isNaN(branchPrice)) {
                    diffCell.html('<span class="text-muted">-</span>');
                } else {
                    var diff = branchPrice - defaultPrice;
                    var colorClass = diff > 0 ? 'text-success' : (diff < 0 ? 'text-danger' : '');
                    var sign = diff >= 0 ? '+' : '';
                    diffCell.html('<span class="' + colorClass + '">' + sign + diff.toFixed(2) + '</span>');
                }
            });

            // Save prices via AJAX
            $('#pricingForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('admin.branch-menu.pricing.save') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            toastr.error(xhr.responseJSON.message);
                        } else {
                            toastr.error('Something went wrong');
                        }
                    }
                });
            });
        });
    </script>
@endpush
