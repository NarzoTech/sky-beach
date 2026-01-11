@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Menu Add-ons') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Menu Add-ons') }}</h4>
                                <div>
                                    <a href="{{ route('admin.menu-addon.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> {{ __('Add New') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Filters -->
                                <form method="GET" action="{{ route('admin.menu-addon.index') }}" class="mb-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <input type="text" name="search" class="form-control" placeholder="{{ __('Search add-ons...') }}" value="{{ $filters['search'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select name="status" class="form-control">
                                                    <option value="">{{ __('All Status') }}</option>
                                                    <option value="1" {{ isset($filters['status']) && $filters['status'] === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value="0" {{ isset($filters['status']) && $filters['status'] === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select name="sort_by" class="form-control">
                                                    <option value="created_at" {{ ($filters['sort_by'] ?? '') === 'created_at' ? 'selected' : '' }}>{{ __('Date Created') }}</option>
                                                    <option value="name" {{ ($filters['sort_by'] ?? '') === 'name' ? 'selected' : '' }}>{{ __('Name') }}</option>
                                                    <option value="price" {{ ($filters['sort_by'] ?? '') === 'price' ? 'selected' : '' }}>{{ __('Price') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-info btn-block">
                                                <i class="fa fa-search"></i> {{ __('Filter') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <!-- Bulk Actions -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-danger btn-sm" id="bulkDeleteBtn" disabled>
                                        <i class="fa fa-trash"></i> {{ __('Delete Selected') }}
                                    </button>
                                </div>

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="40">
                                                    <input type="checkbox" id="selectAll">
                                                </th>
                                                <th width="80">{{ __('Image') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Description') }}</th>
                                                <th width="100">{{ __('Price') }}</th>
                                                <th width="100">{{ __('Items') }}</th>
                                                <th width="100">{{ __('Status') }}</th>
                                                <th width="120">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($addons as $addon)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="addon-checkbox" value="{{ $addon->id }}">
                                                    </td>
                                                    <td>
                                                        @if ($addon->image)
                                                            <img src="{{ asset('storage/' . $addon->image) }}" alt="{{ $addon->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                        @else
                                                            <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fa fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>{{ $addon->name }}</td>
                                                    <td>{{ Str::limit($addon->description, 50) }}</td>
                                                    <td><strong>{{ number_format($addon->price, 2) }}</strong></td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $addon->menu_items_count ?? $addon->menuItems->count() }} {{ __('items') }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <input type="checkbox" class="form-check-input status-toggle" role="switch" id="status{{ $addon->id }}" data-id="{{ $addon->id }}" {{ $addon->status ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.menu-addon.edit', $addon->id) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger delete-addon" data-id="{{ $addon->id }}" title="{{ __('Delete') }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <i class="fa fa-plus-circle fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">{{ __('No add-ons found') }}</p>
                                                        <a href="{{ route('admin.menu-addon.create') }}" class="btn btn-primary">
                                                            <i class="fa fa-plus"></i> {{ __('Create First Add-on') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-3">
                                    {{ $addons->links() }}
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

            // Select All
            $('#selectAll').on('change', function() {
                $('.addon-checkbox').prop('checked', $(this).prop('checked'));
                updateBulkButton();
            });

            $('.addon-checkbox').on('change', function() {
                updateBulkButton();
            });

            function updateBulkButton() {
                var checked = $('.addon-checkbox:checked').length;
                $('#bulkDeleteBtn').prop('disabled', checked === 0);
            }

            // Toggle Status
            $('.status-toggle').on('change', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "/admin/menu-addon/" + id + "/toggle-status",
                    type: 'POST',
                    data: { _token: "{{ csrf_token() }}" },
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

            // Delete Single
            $('.delete-addon').on('click', function() {
                var id = $(this).data('id');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This add-on will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/admin/menu-addon/" + id,
                            type: 'DELETE',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    row.fadeOut(function() { $(this).remove(); });
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
                    }
                });
            });

            // Bulk Delete
            $('#bulkDeleteBtn').on('click', function() {
                var ids = [];
                $('.addon-checkbox:checked').each(function() {
                    ids.push($(this).val());
                });

                if (ids.length === 0) return;

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Selected add-ons will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.menu-addon.bulk-delete') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: ids
                            },
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
                    }
                });
            });
        });
    </script>
@endpush
