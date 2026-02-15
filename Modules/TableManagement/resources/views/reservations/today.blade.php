@extends('admin.layouts.master')
@section('title', __("Today's Reservations"))

@push('css')
<style>
    .reservation-card {
        transition: transform 0.2s;
    }
    .reservation-card:hover {
        transform: translateY(-2px);
    }
    .time-slot {
        font-size: 1.5rem;
        font-weight: bold;
    }
</style>
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h4 class="section_title mb-0">
                        <i class="bx bx-calendar me-2"></i>
                        {{ __("Today's Reservations") }} - {{ now()->format('l, d F Y') }}
                    </h4>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.reservations.calendar') }}" class="btn btn-secondary">
                            <i class="bx bx-calendar me-1"></i> {{ __('Calendar') }}
                        </a>
                        <a href="{{ route('admin.reservations.index') }}" class="btn btn-primary">
                            <i class="bx bx-list-ul me-1"></i> {{ __('All Reservations') }}
                        </a>
                        @adminCan('reservation.create')
                        <a href="{{ route('admin.reservations.create') }}" class="btn btn-success">
                            <i class="bx bx-plus me-1"></i> {{ __('New Reservation') }}
                        </a>
                        @endadminCan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4 reservation-stats-row">
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-primary h-100">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0 text-white">{{ $stats['today_total'] ?? 0 }}</h3>
                    <small class="text-white">{{ __('Total') }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-warning h-100">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0 text-white">{{ $stats['today_pending'] ?? 0 }}</h3>
                    <small class="text-white">{{ __('Pending') }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-info h-100">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0 text-white">{{ $stats['today_confirmed'] ?? 0 }}</h3>
                    <small class="text-white">{{ __('Confirmed') }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-primary h-100">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0 text-white">{{ $stats['today_seated'] ?? 0 }}</h3>
                    <small class="text-white">{{ __('Seated') }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-success h-100">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0 text-white">{{ $stats['today_completed'] ?? 0 }}</h3>
                    <small class="text-white">{{ __('Completed') }}</small>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6 col-6">
            <div class="card bg-secondary h-100">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0 text-white">{{ $stats['upcoming'] ?? 0 }}</h3>
                    <small class="text-white">{{ __('Upcoming') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservations Timeline -->
    <div class="row">
        <div class="col-12">
            @if ($reservations->count() > 0)
                <div class="row g-3">
                    @foreach ($reservations as $reservation)
                        <div class="col-lg-4 col-md-6">
                            <div class="card reservation-card border-{{ $reservation->status_badge }} h-100">
                                <div class="card-header bg-{{ $reservation->status_badge }} text-white d-flex justify-content-between align-items-center">
                                    <span class="time-slot">{{ $reservation->reservation_time->format('H:i') }}</span>
                                    <span class="badge bg-light text-dark">{{ $reservation->status_label }}</span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-3">{{ $reservation->customer_name }}</h5>
                                    <p class="card-text mb-2">
                                        <i class="bx bx-phone me-2 text-muted"></i>{{ $reservation->customer_phone }}
                                    </p>
                                    <p class="card-text mb-2">
                                        <i class="bx bx-chair me-2 text-muted"></i>{{ $reservation->table->name ?? 'N/A' }}
                                    </p>
                                    <p class="card-text mb-2">
                                        <i class="bx bx-group me-2 text-muted"></i>{{ $reservation->party_size }} {{ __('guests') }}
                                    </p>
                                    <p class="card-text mb-0 text-muted">
                                        <i class="bx bx-time-five me-2"></i>{{ $reservation->duration_minutes }} {{ __('min') }}
                                    </p>
                                    @if ($reservation->special_requests)
                                        <p class="card-text mt-2 small text-muted">
                                            <i class="bx bx-comment-detail me-2"></i>{{ Str::limit($reservation->special_requests, 50) }}
                                        </p>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-show"></i>
                                        </a>
                                        @adminCan('reservation.status')
                                        @if ($reservation->status == 'pending')
                                            <button class="btn btn-sm btn-outline-info action-btn" data-action="confirm" data-id="{{ $reservation->id }}">
                                                <i class="bx bx-check me-1"></i>{{ __('Confirm') }}
                                            </button>
                                        @endif
                                        @if (in_array($reservation->status, ['pending', 'confirmed']))
                                            <button class="btn btn-sm btn-outline-success action-btn" data-action="seat" data-id="{{ $reservation->id }}">
                                                <i class="bx bx-log-in me-1"></i>{{ __('Seat') }}
                                            </button>
                                        @endif
                                        @if ($reservation->status == 'seated')
                                            <button class="btn btn-sm btn-outline-success action-btn" data-action="complete" data-id="{{ $reservation->id }}">
                                                <i class="bx bx-check-double me-1"></i>{{ __('Complete') }}
                                            </button>
                                        @endif
                                        @endadminCan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bx bx-calendar-x text-muted mb-3" style="font-size: 4rem;"></i>
                        <h5 class="text-muted">{{ __('No reservations for today') }}</h5>
                        @adminCan('reservation.create')
                        <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary mt-3">
                            <i class="bx bx-plus me-1"></i> {{ __('Create Reservation') }}
                        </a>
                        @endadminCan
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            'use strict';

            $('.action-btn').on('click', function() {
                var action = $(this).data('action');
                var id = $(this).data('id');

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
            });

            // Auto-refresh every 2 minutes
            setInterval(function() {
                location.reload();
            }, 120000);
        });
    </script>
@endpush
