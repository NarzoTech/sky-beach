@extends('admin.layouts.master')
@section('title', __('Combo Deals'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Combo Deals') }}</h4>
                                <div>
                                    <a href="{{ route('admin.combo.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> {{ __('Add New') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Filters -->
                                <form method="GET" action="{{ route('admin.combo.index') }}" class="mb-4">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="text" name="search" class="form-control" placeholder="{{ __('Search combos...') }}" value="{{ $filters['search'] ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select name="status" class="form-control">
                                                    <option value="">{{ __('All Status') }}</option>
                                                    <option value="1" {{ isset($filters['status']) && $filters['status'] === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value="0" {{ isset($filters['status']) && $filters['status'] === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <select name="is_active" class="form-control">
                                                    <option value="">{{ __('All Active') }}</option>
                                                    <option value="1" {{ isset($filters['is_active']) && $filters['is_active'] === '1' ? 'selected' : '' }}>{{ __('Running') }}</option>
                                                    <option value="0" {{ isset($filters['is_active']) && $filters['is_active'] === '0' ? 'selected' : '' }}>{{ __('Paused') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select name="sort_by" class="form-control">
                                                    <option value="created_at" {{ ($filters['sort_by'] ?? '') === 'created_at' ? 'selected' : '' }}>{{ __('Date Created') }}</option>
                                                    <option value="name" {{ ($filters['sort_by'] ?? '') === 'name' ? 'selected' : '' }}>{{ __('Name') }}</option>
                                                    <option value="combo_price" {{ ($filters['sort_by'] ?? '') === 'combo_price' ? 'selected' : '' }}>{{ __('Price') }}</option>
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

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="80">{{ __('Image') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th width="100">{{ __('Items') }}</th>
                                                <th width="120">{{ __('Original') }}</th>
                                                <th width="120">{{ __('Combo Price') }}</th>
                                                <th width="100">{{ __('Savings') }}</th>
                                                <th width="120">{{ __('Duration') }}</th>
                                                <th width="80">{{ __('Active') }}</th>
                                                <th width="80">{{ __('Status') }}</th>
                                                <th width="120">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($combos as $combo)
                                                <tr>
                                                    <td>
                                                        @if ($combo->image)
                                                            <img src="{{ asset('storage/' . $combo->image) }}" alt="{{ $combo->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                        @else
                                                            <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fa fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $combo->name }}</strong>
                                                        <br><small class="text-muted">{{ Str::limit($combo->description, 50) }}</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $combo->items->count() }} {{ __('items') }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-muted text-decoration-line-through">{{ number_format($combo->original_price, 2) }}</span>
                                                    </td>
                                                    <td>
                                                        <strong class="text-success">{{ number_format($combo->combo_price, 2) }}</strong>
                                                    </td>
                                                    <td>
                                                        @if ($combo->savings > 0)
                                                            <span class="badge bg-success">{{ number_format($combo->savings_percentage, 0) }}% off</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($combo->start_date || $combo->end_date)
                                                            <small>
                                                                @if ($combo->start_date)
                                                                    {{ $combo->start_date->format('M d') }}
                                                                @endif
                                                                -
                                                                @if ($combo->end_date)
                                                                    {{ $combo->end_date->format('M d') }}
                                                                @endif
                                                            </small>
                                                        @else
                                                            <small class="text-muted">{{ __('Always') }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input active-toggle" id="active{{ $combo->id }}" data-id="{{ $combo->id }}" {{ $combo->is_active ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="active{{ $combo->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input status-toggle" id="status{{ $combo->id }}" data-id="{{ $combo->id }}" {{ $combo->status ? 'checked' : '' }}>
                                                            <label class="custom-control-label" for="status{{ $combo->id }}"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.combo.edit', $combo->id) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('admin.combo.show', $combo->id) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger delete-combo" data-id="{{ $combo->id }}" title="{{ __('Delete') }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center py-4">
                                                        <i class="fa fa-gift fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">{{ __('No combo deals found') }}</p>
                                                        <a href="{{ route('admin.combo.create') }}" class="btn btn-primary">
                                                            <i class="fa fa-plus"></i> {{ __('Create First Combo') }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-3">
                                    {{ $combos->links() }}
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

            // Toggle Status
            $('.status-toggle').on('change', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "/admin/combo/" + id + "/toggle-status",
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

            // Toggle Active
            $('.active-toggle').on('change', function() {
                var id = $(this).data('id');
                $.ajax({
                    url: "/admin/combo/" + id + "/toggle-active",
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
            $('.delete-combo').on('click', function() {
                var id = $(this).data('id');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This combo will be permanently deleted!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/admin/combo/" + id,
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
        });
    </script>
@endpush
