@extends('admin.layouts.master')
@section('title', __('Manage Variants') . ' - ' . $item->name)
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Manage Variants') }}: {{ $item->name }}</h4>
                                <div>
                                    <a href="{{ route('admin.menu-item.edit', $item->id) }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back to Item') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('Add New Variant') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <form id="addVariantForm">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label>{{ __('Variant Name') }}<span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" required placeholder="e.g., Small, Medium, Large">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('Price Adjustment') }}</label>
                                                        <input type="number" name="price_adjustment" class="form-control" value="0" step="0.01">
                                                        <small class="text-muted">{{ __('Base Price') }}: {{ number_format($item->base_price, 2) }}</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('Is Default') }}</label>
                                                        <select name="is_default" class="form-control">
                                                            <option value="0">{{ __('No') }}</option>
                                                            <option value="1">{{ __('Yes') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('Status') }}</label>
                                                        <select name="status" class="form-control">
                                                            <option value="1">{{ __('Active') }}</option>
                                                            <option value="0">{{ __('Inactive') }}</option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-block">
                                                        <i class="fa fa-plus"></i> {{ __('Add Variant') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-8">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('Existing Variants') }} ({{ $item->variants->count() }})</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="variantsTable">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('Name') }}</th>
                                                                <th>{{ __('Price Adj.') }}</th>
                                                                <th>{{ __('Final Price') }}</th>
                                                                <th>{{ __('Default') }}</th>
                                                                <th>{{ __('Status') }}</th>
                                                                <th>{{ __('Action') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($item->variants as $variant)
                                                                <tr data-id="{{ $variant->id }}">
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm variant-name" value="{{ $variant->name }}">
                                                                    </td>
                                                                    <td>
                                                                        <input type="number" class="form-control form-control-sm variant-price" value="{{ $variant->price_adjustment }}" step="0.01">
                                                                    </td>
                                                                    <td class="final-price">{{ number_format($variant->final_price, 2) }}</td>
                                                                    <td>
                                                                        <select class="form-control form-control-sm variant-default">
                                                                            <option value="0" {{ !$variant->is_default ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                            <option value="1" {{ $variant->is_default ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control form-control-sm variant-status">
                                                                            <option value="1" {{ $variant->status ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                                            <option value="0" {{ !$variant->status ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <button class="btn btn-sm btn-success save-variant" title="{{ __('Save') }}">
                                                                            <i class="fa fa-save"></i>
                                                                        </button>
                                                                        <button class="btn btn-sm btn-danger delete-variant" title="{{ __('Delete') }}">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
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
            var basePrice = {{ $item->base_price }};
            var menuItemId = {{ $item->id }};

            // Add new variant
            $('#addVariantForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('admin.menu-item.variants.store', $item->id) }}",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong');
                    }
                });
            });

            // Save variant
            $('.save-variant').on('click', function() {
                var row = $(this).closest('tr');
                var variantId = row.data('id');
                var data = {
                    _token: "{{ csrf_token() }}",
                    name: row.find('.variant-name').val(),
                    price_adjustment: row.find('.variant-price').val(),
                    is_default: row.find('.variant-default').val(),
                    status: row.find('.variant-status').val()
                };

                $.ajax({
                    url: "/admin/menu-item/" + menuItemId + "/variants/" + variantId,
                    type: 'PUT',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            var finalPrice = basePrice + parseFloat(data.price_adjustment);
                            row.find('.final-price').text(finalPrice.toFixed(2));
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong');
                    }
                });
            });

            // Delete variant
            $('.delete-variant').on('click', function() {
                var row = $(this).closest('tr');
                var variantId = row.data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This variant will be deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/admin/menu-item/" + menuItemId + "/variants/" + variantId,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    row.remove();
                                }
                            },
                            error: function() {
                                toastr.error('Something went wrong');
                            }
                        });
                    }
                });
            });

            // Update final price on price adjustment change
            $('.variant-price').on('change', function() {
                var row = $(this).closest('tr');
                var priceAdj = parseFloat($(this).val()) || 0;
                var finalPrice = basePrice + priceAdj;
                row.find('.final-price').text(finalPrice.toFixed(2));
            });
        });
    </script>
@endpush
