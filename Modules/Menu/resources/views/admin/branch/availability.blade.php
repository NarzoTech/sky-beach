@extends('admin.layouts.master')
@section('title', __('Branch Availability'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Branch Menu Availability') }}</h4>
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
                                            <button type="button" class="btn btn-info btn-block" id="loadAvailability">
                                                <i class="fa fa-refresh"></i> {{ __('Load Availability') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if ($branches->isEmpty())
                                    <div class="alert alert-warning">
                                        <i class="fa fa-exclamation-triangle"></i> {{ __('No branches (warehouses) found. Please create warehouses first.') }}
                                    </div>
                                @else
                                    <form id="availabilityForm">
                                        @csrf
                                        <input type="hidden" name="branch_id" id="branchIdInput" value="{{ $selectedBranch }}">

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="alert alert-info mb-0">
                                                    <i class="fa fa-info-circle"></i> {{ __('Toggle items on/off to control which menu items are available at this branch.') }}
                                                </div>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                <button type="button" class="btn btn-sm btn-success" id="selectAll">
                                                    <i class="fa fa-check-square"></i> {{ __('Select All') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" id="deselectAll">
                                                    <i class="fa fa-square"></i> {{ __('Deselect All') }}
                                                </button>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="60">{{ __('Available') }}</th>
                                                        <th width="80">{{ __('Image') }}</th>
                                                        <th>{{ __('Menu Item') }}</th>
                                                        <th>{{ __('Category') }}</th>
                                                        <th width="120">{{ __('Price') }}</th>
                                                        <th width="100">{{ __('Global Status') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($menuItems->groupBy('category_id') as $categoryId => $items)
                                                        @php
                                                            $category = $items->first()->category;
                                                        @endphp
                                                        <tr class="table-secondary">
                                                            <td colspan="6">
                                                                <strong>{{ $category->name ?? __('Uncategorized') }}</strong>
                                                                <span class="badge bg-info ml-2">{{ $items->count() }} {{ __('items') }}</span>
                                                            </td>
                                                        </tr>
                                                        @foreach ($items as $item)
                                                            @php
                                                                // Default to available if not set
                                                                $isAvailable = !isset($branchAvailability[$item->id]) || $branchAvailability[$item->id];
                                                            @endphp
                                                            <tr>
                                                                <td class="text-center">
                                                                    <div class="custom-control custom-switch">
                                                                        <input type="checkbox"
                                                                            class="custom-control-input availability-toggle"
                                                                            id="avail{{ $item->id }}"
                                                                            name="availability[{{ $item->id }}]"
                                                                            value="1"
                                                                            {{ $isAvailable ? 'checked' : '' }}>
                                                                        <label class="custom-control-label" for="avail{{ $item->id }}"></label>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @if ($item->image)
                                                                        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                                    @else
                                                                        <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                                            <i class="fa fa-image text-muted"></i>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <strong>{{ $item->name }}</strong>
                                                                    @if ($item->variants->count() > 0)
                                                                        <br><small class="text-muted">{{ $item->variants->count() }} {{ __('variants') }}</small>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                                                <td>{{ number_format($item->base_price, 2) }}</td>
                                                                <td>
                                                                    <span class="badge {{ $item->status ? 'bg-success' : 'bg-danger' }}">
                                                                        {{ $item->status ? __('Active') : __('Inactive') }}
                                                                    </span>
                                                                    @if (!$item->is_available)
                                                                        <span class="badge bg-warning">{{ __('Unavailable') }}</span>
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
                                                <i class="fa fa-save"></i> {{ __('Save Availability') }}
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

            // Load availability for selected branch
            $('#loadAvailability, #branchSelect').on('change click', function(e) {
                if (e.type === 'change' || $(this).is('#loadAvailability')) {
                    var branchId = $('#branchSelect').val();
                    window.location.href = "{{ route('admin.branch-menu.availability') }}?branch_id=" + branchId;
                }
            });

            // Select all
            $('#selectAll').on('click', function() {
                $('.availability-toggle').prop('checked', true);
            });

            // Deselect all
            $('#deselectAll').on('click', function() {
                $('.availability-toggle').prop('checked', false);
            });

            // Save availability via AJAX
            $('#availabilityForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('admin.branch-menu.availability.save') }}",
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
