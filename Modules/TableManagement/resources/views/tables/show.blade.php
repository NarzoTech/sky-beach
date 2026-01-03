@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Table Details') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ $table->name }}</h4>
                                <div>
                                    <a href="{{ route('admin.tables.edit', $table->id) }}" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> {{ __('Edit') }}
                                    </a>
                                    <a href="{{ route('admin.tables.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">{{ __('Table Number') }}</th>
                                                <td>{{ $table->table_number }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Capacity') }}</th>
                                                <td>{{ $table->capacity }} {{ __('seats') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Floor') }}</th>
                                                <td>{{ $table->floor ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Section') }}</th>
                                                <td>{{ $table->section ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Shape') }}</th>
                                                <td>{{ ucfirst($table->shape) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="40%">{{ __('Status') }}</th>
                                                <td>
                                                    <span class="badge bg-{{ $table->status_badge }}">
                                                        {{ $table->status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Active') }}</th>
                                                <td>
                                                    @if ($table->is_active)
                                                        <span class="badge bg-success">{{ __('Yes') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">{{ __('No') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Position') }}</th>
                                                <td>X: {{ $table->position_x }}, Y: {{ $table->position_y }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Sort Order') }}</th>
                                                <td>{{ $table->sort_order }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                @if ($table->notes)
                                    <div class="mt-3">
                                        <h6>{{ __('Notes') }}</h6>
                                        <p class="text-muted">{{ $table->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Today's Reservations -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>{{ __("Today's Reservations") }}</h5>
                            </div>
                            <div class="card-body">
                                @if ($table->todayReservations->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Time') }}</th>
                                                    <th>{{ __('Customer') }}</th>
                                                    <th>{{ __('Party Size') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($table->todayReservations as $res)
                                                    <tr>
                                                        <td>{{ $res->reservation_time->format('H:i') }}</td>
                                                        <td>{{ $res->customer_name }}</td>
                                                        <td>{{ $res->party_size }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $res->status_badge }}">
                                                                {{ $res->status_label }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">{{ __('No reservations for today.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Current Sale -->
                        @if ($table->currentSale)
                            <div class="card bg-danger text-white">
                                <div class="card-header">
                                    <h5 class="text-white">{{ __('Current Order') }}</h5>
                                </div>
                                <div class="card-body">
                                    <h4>{{ $table->currentSale->invoice }}</h4>
                                    <p>{{ __('Total') }}: {{ number_format($table->currentSale->grand_total, 2) }}</p>
                                    <a href="{{ route('admin.sales.show', $table->currentSale->id) }}" class="btn btn-light btn-sm">
                                        {{ __('View Order') }}
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x mb-2"></i>
                                    <h5 class="text-white">{{ __('Table Available') }}</h5>
                                </div>
                            </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5>{{ __('Quick Actions') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.reservations.create') }}?table_id={{ $table->id }}" class="btn btn-info">
                                        <i class="fas fa-calendar-plus"></i> {{ __('Create Reservation') }}
                                    </a>
                                    @if ($table->isOccupied())
                                        <button class="btn btn-warning release-table" data-id="{{ $table->id }}">
                                            <i class="fas fa-sign-out-alt"></i> {{ __('Release Table') }}
                                        </button>
                                    @endif
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
