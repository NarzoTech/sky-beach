@extends('admin.layouts.master')
@section('title', __('Reservation Details'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ $reservation->reservation_number }}</h4>
                                <div>
                                    <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> {{ __('Edit') }}
                                    </a>
                                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">{{ __('Customer Information') }}</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">{{ __('Name') }}</th>
                                                <td>{{ $reservation->customer_name }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Phone') }}</th>
                                                <td>{{ $reservation->customer_phone }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Email') }}</th>
                                                <td>{{ $reservation->customer_email ?? '-' }}</td>
                                            </tr>
                                            @if ($reservation->customer)
                                                <tr>
                                                    <th>{{ __('Registered Customer') }}</th>
                                                    <td>
                                                        <span class="badge bg-success">{{ __('Yes') }}</span>
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">{{ __('Reservation Details') }}</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">{{ __('Table') }}</th>
                                                <td>{{ $reservation->table->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <td>{{ $reservation->reservation_date->format('d M, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Time') }}</th>
                                                <td>{{ $reservation->reservation_time->format('H:i') }} - {{ $reservation->end_time->format('H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Party Size') }}</th>
                                                <td>{{ $reservation->party_size }} {{ __('guests') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Duration') }}</th>
                                                <td>{{ $reservation->duration_minutes }} {{ __('minutes') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                @if ($reservation->special_requests)
                                    <div class="mt-3">
                                        <h6 class="text-muted">{{ __('Special Requests') }}</h6>
                                        <p>{{ $reservation->special_requests }}</p>
                                    </div>
                                @endif

                                @if ($reservation->notes)
                                    <div class="mt-3">
                                        <h6 class="text-muted">{{ __('Internal Notes') }}</h6>
                                        <p class="text-muted">{{ $reservation->notes }}</p>
                                    </div>
                                @endif

                                <hr>

                                <h6 class="text-muted mb-3">{{ __('Activity Log') }}</h6>
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <span class="badge bg-secondary">{{ __('Created') }}</span>
                                        <span class="ms-2">{{ $reservation->created_at->format('d M, Y H:i') }}</span>
                                        @if ($reservation->createdBy)
                                            <span class="text-muted ms-2">{{ __('by') }} {{ $reservation->createdBy->name }}</span>
                                        @endif
                                    </div>
                                    @if ($reservation->confirmed_at)
                                        <div class="timeline-item mt-2">
                                            <span class="badge bg-info">{{ __('Confirmed') }}</span>
                                            <span class="ms-2">{{ $reservation->confirmed_at->format('d M, Y H:i') }}</span>
                                            @if ($reservation->confirmedBy)
                                                <span class="text-muted ms-2">{{ __('by') }} {{ $reservation->confirmedBy->name }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($reservation->seated_at)
                                        <div class="timeline-item mt-2">
                                            <span class="badge bg-primary">{{ __('Seated') }}</span>
                                            <span class="ms-2">{{ $reservation->seated_at->format('d M, Y H:i') }}</span>
                                        </div>
                                    @endif
                                    @if ($reservation->completed_at)
                                        <div class="timeline-item mt-2">
                                            <span class="badge bg-success">{{ __('Completed') }}</span>
                                            <span class="ms-2">{{ $reservation->completed_at->format('d M, Y H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Current Status -->
                        <div class="card bg-{{ $reservation->status_badge }} text-white">
                            <div class="card-body text-center">
                                <h5 class="text-white">{{ __('Status') }}</h5>
                                <h2 class="text-white">{{ $reservation->status_label }}</h2>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>{{ __('Quick Actions') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    @if ($reservation->status == 'pending')
                                        <button class="btn btn-info action-btn" data-action="confirm">
                                            <i class="fas fa-check"></i> {{ __('Confirm Reservation') }}
                                        </button>
                                    @endif
                                    @if (in_array($reservation->status, ['pending', 'confirmed']))
                                        <button class="btn btn-primary action-btn" data-action="seat">
                                            <i class="fas fa-chair"></i> {{ __('Seat Guests') }}
                                        </button>
                                        <button class="btn btn-warning action-btn" data-action="no-show">
                                            <i class="fas fa-user-slash"></i> {{ __('Mark No-Show') }}
                                        </button>
                                        <button class="btn btn-danger action-btn" data-action="cancel">
                                            <i class="fas fa-times"></i> {{ __('Cancel Reservation') }}
                                        </button>
                                    @endif
                                    @if ($reservation->status == 'seated')
                                        <button class="btn btn-success action-btn" data-action="complete">
                                            <i class="fas fa-check-double"></i> {{ __('Complete') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Table Info -->
                        @if ($reservation->table)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>{{ __('Table Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>{{ $reservation->table->name }}</strong></p>
                                    <p class="mb-1">{{ __('Capacity') }}: {{ $reservation->table->capacity }} {{ __('seats') }}</p>
                                    <p class="mb-1">{{ __('Floor') }}: {{ $reservation->table->floor ?? '-' }}</p>
                                    <p class="mb-0">{{ __('Section') }}: {{ $reservation->table->section ?? '-' }}</p>
                                    <a href="{{ route('admin.tables.show', $reservation->table->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                                        {{ __('View Table') }}
                                    </a>
                                </div>
                            </div>
                        @endif
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

            $('.action-btn').on('click', function() {
                var action = $(this).data('action');
                var id = {{ $reservation->id }};

                Swal.fire({
                    title: '{{ __("Are you sure?") }}',
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
