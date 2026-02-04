@extends('admin.layouts.master')
@section('title', __('Reservations'))
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
                                        @foreach ($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <select name="table_id" class="form-control">
                                        <option value="">{{ __('All Tables') }}</option>
                                        @foreach ($tables as $table)
                                            <option value="{{ $table->id }}" {{ request('table_id') == $table->id ? 'selected' : '' }}>
                                                {{ $table->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
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
    <div class="row g-3 my-3 reservation-stats-row">
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-primary h-100">
                <div class="card-body py-3">
                    <h6 class="text-white mb-1 small">{{ __("Today's Total") }}</h6>
                    <h3 class="mb-0 text-white">{{ $stats['today_total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-warning h-100">
                <div class="card-body py-3">
                    <h6 class="text-white mb-1 small">{{ __('Pending') }}</h6>
                    <h3 class="mb-0 text-white">{{ $stats['today_pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-info h-100">
                <div class="card-body py-3">
                    <h6 class="text-white mb-1 small">{{ __('Confirmed') }}</h6>
                    <h3 class="mb-0 text-white">{{ $stats['today_confirmed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-primary h-100">
                <div class="card-body py-3">
                    <h6 class="text-white mb-1 small">{{ __('Seated') }}</h6>
                    <h3 class="mb-0 text-white">{{ $stats['today_seated'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-success h-100">
                <div class="card-body py-3">
                    <h6 class="text-white mb-1 small">{{ __('Completed') }}</h6>
                    <h3 class="mb-0 text-white">{{ $stats['today_completed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-secondary h-100">
                <div class="card-body py-3">
                    <h6 class="text-white mb-1 small">{{ __('Upcoming') }}</h6>
                    <h3 class="mb-0 text-white">{{ $stats['upcoming'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Reservations') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.reservations.today') }}" class="btn btn-info mr-2">
                    <i class="fa fa-clock"></i> {{ __("Today's View") }}
                </a>
                <a href="{{ route('admin.reservations.calendar') }}" class="btn btn-secondary mr-2">
                    <i class="fa fa-calendar"></i> {{ __('Calendar') }}
                </a>
                <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> {{ __('New Reservation') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SL.') }}</th>
                            <th>{{ __('Reservation #') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Table') }}</th>
                            <th>{{ __('Date & Time') }}</th>
                            <th>{{ __('Party Size') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reservations as $reservation)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $reservation->reservation_number }}</strong>
                                </td>
                                <td>
                                    <div>{{ $reservation->customer_name }}</div>
                                    <small class="text-muted">{{ $reservation->customer_phone }}</small>
                                </td>
                                <td>{{ $reservation->table->name ?? 'N/A' }}</td>
                                <td>
                                    <div>{{ $reservation->reservation_date->format('d M, Y') }}</div>
                                    <small class="text-muted">{{ $reservation->reservation_time->format('H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $reservation->party_size }} {{ __('guests') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $reservation->status_badge }}">
                                        {{ $reservation->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $reservation->id }}" type="button"
                                            class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $reservation->id }}">
                                            <a href="{{ route('admin.reservations.show', $reservation->id) }}"
                                                class="dropdown-item">{{ __('View') }}</a>
                                            <a href="{{ route('admin.reservations.edit', $reservation->id) }}"
                                                class="dropdown-item">{{ __('Edit') }}</a>
                                            <div class="dropdown-divider"></div>
                                            @if ($reservation->status == 'pending')
                                                <a href="javascript:void(0)" class="dropdown-item action-btn"
                                                    data-action="confirm" data-id="{{ $reservation->id }}">
                                                    <i class="fas fa-check text-info"></i> {{ __('Confirm') }}
                                                </a>
                                            @endif
                                            @if (in_array($reservation->status, ['pending', 'confirmed']))
                                                <a href="javascript:void(0)" class="dropdown-item action-btn"
                                                    data-action="seat" data-id="{{ $reservation->id }}">
                                                    <i class="fas fa-chair text-primary"></i> {{ __('Seat Guests') }}
                                                </a>
                                            @endif
                                            @if ($reservation->status == 'seated')
                                                <a href="javascript:void(0)" class="dropdown-item action-btn"
                                                    data-action="complete" data-id="{{ $reservation->id }}">
                                                    <i class="fas fa-check-double text-success"></i> {{ __('Complete') }}
                                                </a>
                                            @endif
                                            @if (in_array($reservation->status, ['pending', 'confirmed']))
                                                <a href="javascript:void(0)" class="dropdown-item action-btn"
                                                    data-action="cancel" data-id="{{ $reservation->id }}">
                                                    <i class="fas fa-times text-danger"></i> {{ __('Cancel') }}
                                                </a>
                                                <a href="javascript:void(0)" class="dropdown-item action-btn"
                                                    data-action="no-show" data-id="{{ $reservation->id }}">
                                                    <i class="fas fa-user-slash text-dark"></i> {{ __('No Show') }}
                                                </a>
                                            @endif
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0)"
                                                class="trigger--fire-modal-1 deleteForm dropdown-item"
                                                data-url="{{ route('admin.reservations.destroy', $reservation->id) }}"
                                                data-form="deleteForm">{{ __('Delete') }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('No reservations found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="float-right">
                {{ $reservations->links() }}
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

            $('.action-btn').on('click', function() {
                var action = $(this).data('action');
                var id = $(this).data('id');
                var actionLabels = {
                    'confirm': '{{ __("Confirm this reservation?") }}',
                    'seat': '{{ __("Mark guests as seated?") }}',
                    'complete': '{{ __("Complete this reservation?") }}',
                    'cancel': '{{ __("Cancel this reservation?") }}',
                    'no-show': '{{ __("Mark as no-show?") }}'
                };

                Swal.fire({
                    title: actionLabels[action],
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __("Yes") }}',
                    cancelButtonText: '{{ __("No") }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('admin/reservations') }}/" + id + "/" + action,
                            type: 'POST',
                            data: { _token: "{{ csrf_token() }}" },
                            success: function(response) {
                                toastr.success('{{ __("Action completed successfully") }}');
                                setTimeout(() => location.reload(), 1000);
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseJSON?.message || '{{ __("An error occurred") }}');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
