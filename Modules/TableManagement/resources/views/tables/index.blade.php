@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Restaurant Tables') }}</title>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">{{ __('All Status') }}</option>
                                        @foreach ($statuses ?? [] as $key => $label)
                                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <select name="floor" class="form-control">
                                        <option value="">{{ __('All Floors') }}</option>
                                        @foreach ($floors ?? [] as $floor)
                                            <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>
                                                {{ $floor }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <select name="section" class="form-control">
                                        <option value="">{{ __('All Sections') }}</option>
                                        @foreach ($sections ?? [] as $section)
                                            <option value="{{ $section }}" {{ request('section') == $section ? 'selected' : '' }}>
                                                {{ $section }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">{{ __('Total Tables') }}</h5>
                    <h2 class="mb-0">{{ $stats['total'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">{{ __('Available') }}</h5>
                    <h2 class="mb-0">{{ $stats['available'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">{{ __('Occupied') }}</h5>
                    <h2 class="mb-0">{{ $stats['occupied'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title text-white">{{ __('Reserved') }}</h5>
                    <h2 class="mb-0">{{ $stats['reserved'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Restaurant Tables') }}</h4>
            </div>
            @if(!auth('admin')->user()->hasRole('Waiter'))
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.tables.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> {{ __('Add Table') }}
                </a>
            </div>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SL.') }}</th>
                            <th>{{ __('Table') }}</th>
                            <th>{{ __('Number') }}</th>
                            <th>{{ __('Capacity') }}</th>
                            <th>{{ __('Floor') }}</th>
                            <th>{{ __('Section') }}</th>
                            <th>{{ __('Shape') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Active') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tables as $table)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $table->name }}</strong>
                                </td>
                                <td>{{ $table->table_number }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $table->capacity }} {{ __('seats') }}</span>
                                </td>
                                <td>{{ $table->floor ?? '-' }}</td>
                                <td>{{ $table->section ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($table->shape) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $table->status_badge }}">
                                        {{ $table->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if ($table->is_active)
                                        <span class="badge bg-success">{{ __('Yes') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $table->id }}" type="button"
                                            class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $table->id }}">
                                            <a href="{{ route('admin.tables.show', $table->id) }}"
                                                class="dropdown-item">{{ __('View') }}</a>
                                            @if(!auth('admin')->user()->hasRole('Waiter'))
                                            <a href="{{ route('admin.tables.edit', $table->id) }}"
                                                class="dropdown-item">{{ __('Edit') }}</a>
                                            @endif
                                            @if ($table->isOccupied())
                                                <a href="javascript:void(0)"
                                                    class="dropdown-item release-table"
                                                    data-id="{{ $table->id }}">{{ __('Release Table') }}</a>
                                            @endif
                                            @if(!auth('admin')->user()->hasRole('Waiter'))
                                            <a href="javascript:void(0)"
                                                class="trigger--fire-modal-1 deleteForm dropdown-item"
                                                data-url="{{ route('admin.tables.destroy', $table->id) }}"
                                                data-form="deleteForm">{{ __('Delete') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">{{ __('No tables found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="float-right">
                {{ $tables->links() }}
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            'use strict';
            $('.deleteForm').on('click', function() {
                var url = $(this).data('url');
                $('#deleteForm').attr('action', url);
                $('#deleteModal').modal('show');
            });

            $('.release-table').on('click', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Release Table?',
                    text: 'This will mark the table as available.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, release it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/tables/release') }}/" + id,
                            type: 'POST',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    toastr.error(response.message);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
