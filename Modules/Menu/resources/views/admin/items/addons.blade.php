@extends('admin.layouts.master')
@section('title', __('Manage Add-ons') . ' - ' . $item->name)
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Manage Add-ons') }}: {{ $item->name }}</h4>
                                <div>
                                    <a href="{{ route('admin.menu-item.edit', $item->id) }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back to Item') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('Add Add-on to Item') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <form id="attachAddonForm">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label>{{ __('Select Add-on') }}<span class="text-danger">*</span></label>
                                                        <select name="addon_id" class="form-control select2" required>
                                                            <option value="">{{ __('Select Add-on') }}</option>
                                                            @foreach ($availableAddons as $addon)
                                                                <option value="{{ $addon->id }}" data-price="{{ $addon->price }}">
                                                                    {{ $addon->name }} ({{ number_format($addon->price, 2) }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('Max Quantity') }}</label>
                                                        <input type="number" name="max_quantity" class="form-control" value="5" min="1">
                                                        <small class="text-muted">{{ __('Maximum quantity customer can add') }}</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>{{ __('Is Required?') }}</label>
                                                        <select name="is_required" class="form-control">
                                                            <option value="0">{{ __('No') }}</option>
                                                            <option value="1">{{ __('Yes') }}</option>
                                                        </select>
                                                        <small class="text-muted">{{ __('Customer must select this add-on') }}</small>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-block">
                                                        <i class="fa fa-plus"></i> {{ __('Attach Add-on') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h5>{{ __('Item Information') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="img-fluid rounded mb-3" style="max-height: 150px;">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th>{{ __('Base Price') }}</th>
                                                        <td>{{ number_format($item->base_price, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('Category') }}</th>
                                                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('Status') }}</th>
                                                        <td>
                                                            <span class="badge {{ $item->status ? 'bg-success' : 'bg-danger' }}">
                                                                {{ $item->status ? __('Active') : __('Inactive') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-7">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>{{ __('Attached Add-ons') }} ({{ $item->addons->count() }})</h5>
                                            </div>
                                            <div class="card-body">
                                                @if ($item->addons->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered" id="addonsTable">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{ __('Add-on') }}</th>
                                                                    <th>{{ __('Price') }}</th>
                                                                    <th>{{ __('Max Qty') }}</th>
                                                                    <th>{{ __('Required') }}</th>
                                                                    <th>{{ __('Action') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($item->addons as $addon)
                                                                    <tr data-id="{{ $addon->id }}">
                                                                        <td>
                                                                            @if ($addon->image)
                                                                                <img src="{{ asset($addon->image) }}" alt="{{ $addon->name }}" style="width: 30px; height: 30px; object-fit: cover; border-radius: 4px;" class="mr-2">
                                                                            @endif
                                                                            {{ $addon->name }}
                                                                        </td>
                                                                        <td>{{ number_format($addon->price, 2) }}</td>
                                                                        <td>
                                                                            <input type="number" class="form-control form-control-sm addon-max-qty" value="{{ $addon->pivot->max_quantity }}" min="1" style="width: 70px;">
                                                                        </td>
                                                                        <td>
                                                                            <select class="form-control form-control-sm addon-required">
                                                                                <option value="0" {{ !$addon->pivot->is_required ? 'selected' : '' }}>{{ __('No') }}</option>
                                                                                <option value="1" {{ $addon->pivot->is_required ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <button class="btn btn-sm btn-success update-addon" title="{{ __('Update') }}">
                                                                                <i class="fa fa-save"></i>
                                                                            </button>
                                                                            <button class="btn btn-sm btn-danger detach-addon" title="{{ __('Remove') }}">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="text-center py-4">
                                                        <i class="fa fa-plus-circle fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">{{ __('No add-ons attached to this item yet.') }}</p>
                                                        <p class="text-muted">{{ __('Use the form on the left to attach add-ons.') }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h5>{{ __('Tips') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-unstyled mb-0">
                                                    <li><i class="fa fa-info-circle text-info"></i> {{ __('Add-ons are extras that customers can add to their order') }}</li>
                                                    <li class="mt-2"><i class="fa fa-info-circle text-info"></i> {{ __('Required add-ons must be selected by the customer') }}</li>
                                                    <li class="mt-2"><i class="fa fa-info-circle text-info"></i> {{ __('Max quantity limits how many of each add-on can be added') }}</li>
                                                    <li class="mt-2"><i class="fa fa-info-circle text-info"></i> <a href="{{ route('admin.menu-addon.create') }}" target="_blank">{{ __('Create new add-ons here') }}</a></li>
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
            var menuItemId = {{ $item->id }};

            // Attach addon to item
            $('#attachAddonForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "/admin/menu-item/" + menuItemId + "/addons",
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
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

            // Update addon pivot data
            $('.update-addon').on('click', function() {
                var row = $(this).closest('tr');
                var addonId = row.data('id');
                var data = {
                    _token: "{{ csrf_token() }}",
                    max_quantity: row.find('.addon-max-qty').val(),
                    is_required: row.find('.addon-required').val()
                };

                $.ajax({
                    url: "/admin/menu-item/" + menuItemId + "/addons/" + addonId,
                    type: 'PUT',
                    data: data,
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

            // Detach addon from item
            $('.detach-addon').on('click', function() {
                var row = $(this).closest('tr');
                var addonId = row.data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This add-on will be removed from this item!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/admin/menu-item/" + menuItemId + "/addons/" + addonId,
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
        });
    </script>
@endpush
