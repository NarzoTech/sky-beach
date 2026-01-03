@extends('admin.layouts.master')
@section('title')
    <title>{{ __("Today's Reservations") }}</title>
@endsection

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
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="section_title mb-0">
                        <i class="fas fa-calendar-day me-2"></i>
                        {{ __("Today's Reservations") }} - {{ now()->format('l, d F Y') }}
                    </h4>
                    <div>
                        <a href="{{ route('admin.reservations.calendar') }}" class="btn btn-secondary">
                            <i class="fa fa-calendar"></i> {{ __('Calendar') }}
                        </a>
                        <a href="{{ route('admin.reservations.index') }}" class="btn btn-primary">
                            <i class="fa fa-list"></i> {{ __('All Reservations') }}
                        </a>
                        <a href="{{ route('admin.reservations.create') }}" class="btn btn-success">
                            <i class="fa fa-plus"></i> {{ __('New Reservation') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0">{{ $stats['today_total'] ?? 0 }}</h3>
                    <small>{{ __('Total') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0">{{ $stats['today_pending'] ?? 0 }}</h3>
                    <small>{{ __('Pending') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0">{{ $stats['today_confirmed'] ?? 0 }}</h3>
                    <small>{{ __('Confirmed') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0">{{ $stats['today_seated'] ?? 0 }}</h3>
                    <small>{{ __('Seated') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0">{{ $stats['today_completed'] ?? 0 }}</h3>
                    <small>{{ __('Completed') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body py-3 text-center">
                    <h3 class="mb-0">{{ $stats['upcoming'] ?? 0 }}</h3>
                    <small>{{ __('Upcoming') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservations Timeline -->
    <div class="row">
        <div class="col-12">
            @if ($reservations->count() > 0)
                <div class="row">
                    @foreach ($reservations as $reservation)
                        <div class="col-md-4 mb-4">
                            <div class="card reservation-card border-{{ $reservation->status_badge }}">
                                <div class="card-header bg-{{ $reservation->status_badge }} text-white d-flex justify-content-between align-items-center">
                                    <span class="time-slot">{{ $reservation->reservation_time->format('H:i') }}</span>
                                    <span class="badge bg-light text-dark">{{ $reservation->status_label }}</span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $reservation->customer_name }}</h5>
                                    <p class="card-text mb-1">
                                        <i class="fas fa-phone me-2"></i>{{ $reservation->customer_phone }}
                                    </p>
                                    <p class="card-text mb-1">
                                        <i class="fas fa-chair me-2"></i>{{ $reservation->table->name ?? 'N/A' }}
                                    </p>
                                    <p class="card-text mb-1">
                                        <i class="fas fa-users me-2"></i>{{ $reservation->party_size }} {{ __('guests') }}
                                    </p>
                                    <p class="card-text mb-0 text-muted">
                                        <i class="fas fa-clock me-2"></i>{{ $reservation->duration_minutes }} {{ __('min') }}
                                    </p>
                                    @if ($reservation->special_requests)
                                        <p class="card-text mt-2 small text-muted">
                                            <i class="fas fa-comment me-2"></i>{{ Str::limit($reservation->special_requests, 50) }}
                                        </p>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="btn-group w-100" role="group">
                                        <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($reservation->status == 'pending')
                                            <button class="btn btn-sm btn-outline-info action-btn" data-action="confirm" data-id="{{ $reservation->id }}">
                                                <i class="fas fa-check"></i> {{ __('Confirm') }}
                                            </button>
                                        @endif
                                        @if (in_array($reservation->status, ['pending', 'confirmed']))
                                            <button class="btn btn-sm btn-outline-success action-btn" data-action="seat" data-id="{{ $reservation->id }}">
                                                <i class="fas fa-chair"></i> {{ __('Seat') }}
                                            </button>
                                        @endif
                                        @if ($reservation->status == 'seated')
                                            <button class="btn btn-sm btn-outline-success action-btn" data-action="complete" data-id="{{ $reservation->id }}">
                                                <i class="fas fa-check-double"></i> {{ __('Complete') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('No reservations for today') }}</h5>
                        <a href="{{ route('admin.reservations.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus"></i> {{ __('Create Reservation') }}
                        </a>
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
