@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Reservation') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Edit Reservation') }}: {{ $reservation->reservation_number }}</h4>
                                <div>
                                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.reservations.update', $reservation->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <!-- Customer Information -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Customer Information') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="customer_id">{{ __('Select Existing Customer') }}</label>
                                                                <select name="customer_id" id="customer_id" class="form-control select2">
                                                                    <option value="">{{ __('Walk-in Customer') }}</option>
                                                                    @foreach ($customers as $customer)
                                                                        <option value="{{ $customer->id }}"
                                                                            data-name="{{ $customer->name }}"
                                                                            data-phone="{{ $customer->phone }}"
                                                                            data-email="{{ $customer->email }}"
                                                                            {{ old('customer_id', $reservation->customer_id) == $customer->id ? 'selected' : '' }}>
                                                                            {{ $customer->name }} - {{ $customer->phone }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="customer_name">{{ __('Customer Name') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="customer_name" class="form-control" id="customer_name" required value="{{ old('customer_name', $reservation->customer_name) }}">
                                                                @error('customer_name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="customer_phone">{{ __('Phone') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="customer_phone" class="form-control" id="customer_phone" required value="{{ old('customer_phone', $reservation->customer_phone) }}">
                                                                @error('customer_phone')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="customer_email">{{ __('Email') }}</label>
                                                                <input type="email" name="customer_email" class="form-control" id="customer_email" value="{{ old('customer_email', $reservation->customer_email) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reservation Details -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Reservation Details') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="table_id">{{ __('Table') }}<span class="text-danger">*</span></label>
                                                                <select name="table_id" id="table_id" class="form-control select2" required>
                                                                    <option value="">{{ __('Select Table') }}</option>
                                                                    @foreach ($tables as $table)
                                                                        <option value="{{ $table->id }}"
                                                                            data-capacity="{{ $table->capacity }}"
                                                                            {{ old('table_id', $reservation->table_id) == $table->id ? 'selected' : '' }}>
                                                                            {{ $table->name }} ({{ $table->capacity }} {{ __('seats') }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @error('table_id')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="party_size">{{ __('Party Size') }}<span class="text-danger">*</span></label>
                                                                <input type="number" name="party_size" class="form-control" id="party_size" required value="{{ old('party_size', $reservation->party_size) }}" min="1">
                                                                @error('party_size')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="reservation_date">{{ __('Date') }}<span class="text-danger">*</span></label>
                                                                <input type="date" name="reservation_date" class="form-control" id="reservation_date" required value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d')) }}">
                                                                @error('reservation_date')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="reservation_time">{{ __('Time') }}<span class="text-danger">*</span></label>
                                                                <input type="time" name="reservation_time" class="form-control" id="reservation_time" required value="{{ old('reservation_time', $reservation->reservation_time->format('H:i')) }}">
                                                                @error('reservation_time')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="duration_minutes">{{ __('Duration (minutes)') }}</label>
                                                                <select name="duration_minutes" id="duration_minutes" class="form-control">
                                                                    <option value="60" {{ old('duration_minutes', $reservation->duration_minutes) == 60 ? 'selected' : '' }}>1 {{ __('hour') }}</option>
                                                                    <option value="90" {{ old('duration_minutes', $reservation->duration_minutes) == 90 ? 'selected' : '' }}>1.5 {{ __('hours') }}</option>
                                                                    <option value="120" {{ old('duration_minutes', $reservation->duration_minutes) == 120 ? 'selected' : '' }}>2 {{ __('hours') }}</option>
                                                                    <option value="180" {{ old('duration_minutes', $reservation->duration_minutes) == 180 ? 'selected' : '' }}>3 {{ __('hours') }}</option>
                                                                    <option value="240" {{ old('duration_minutes', $reservation->duration_minutes) == 240 ? 'selected' : '' }}>4 {{ __('hours') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Special Requests -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Additional Information') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="special_requests">{{ __('Special Requests') }}</label>
                                                        <textarea name="special_requests" class="form-control" id="special_requests" rows="3">{{ old('special_requests', $reservation->special_requests) }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="notes">{{ __('Internal Notes') }}</label>
                                                        <textarea name="notes" class="form-control" id="notes" rows="2">{{ old('notes', $reservation->notes) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <!-- Current Status -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Current Status') }}</h5>
                                                </div>
                                                <div class="card-body text-center">
                                                    <span class="badge bg-{{ $reservation->status_badge }} fs-5 p-2">
                                                        {{ $reservation->status_label }}
                                                    </span>
                                                    @if ($reservation->confirmed_at)
                                                        <p class="mt-2 mb-0 text-muted small">
                                                            {{ __('Confirmed') }}: {{ $reservation->confirmed_at->format('d M, Y H:i') }}
                                                        </p>
                                                    @endif
                                                    @if ($reservation->seated_at)
                                                        <p class="mb-0 text-muted small">
                                                            {{ __('Seated') }}: {{ $reservation->seated_at->format('H:i') }}
                                                        </p>
                                                    @endif
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
                                                            <button type="button" class="btn btn-info action-btn" data-action="confirm">
                                                                <i class="fas fa-check"></i> {{ __('Confirm') }}
                                                            </button>
                                                        @endif
                                                        @if (in_array($reservation->status, ['pending', 'confirmed']))
                                                            <button type="button" class="btn btn-primary action-btn" data-action="seat">
                                                                <i class="fas fa-chair"></i> {{ __('Seat Guests') }}
                                                            </button>
                                                            <button type="button" class="btn btn-danger action-btn" data-action="cancel">
                                                                <i class="fas fa-times"></i> {{ __('Cancel') }}
                                                            </button>
                                                        @endif
                                                        @if ($reservation->status == 'seated')
                                                            <button type="button" class="btn btn-success action-btn" data-action="complete">
                                                                <i class="fas fa-check-double"></i> {{ __('Complete') }}
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="card mt-3">
                                                <div class="card-body text-center">
                                                    <x-admin.save-button :text="__('Update Reservation')"></x-admin.save-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
