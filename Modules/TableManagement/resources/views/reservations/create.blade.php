@extends('admin.layouts.master')
@section('title')
    <title>{{ __('New Reservation') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('New Reservation') }}</h4>
                                <div>
                                    <a href="{{ route('admin.reservations.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.reservations.store') }}" method="post" id="reservationForm">
                                    @csrf
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
                                                                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                                            {{ $customer->name }} - {{ $customer->phone }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="customer_name">{{ __('Customer Name') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="customer_name" class="form-control" id="customer_name" required value="{{ old('customer_name') }}">
                                                                @error('customer_name')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="customer_phone">{{ __('Phone') }}<span class="text-danger">*</span></label>
                                                                <input type="text" name="customer_phone" class="form-control" id="customer_phone" required value="{{ old('customer_phone') }}">
                                                                @error('customer_phone')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="customer_email">{{ __('Email') }}</label>
                                                                <input type="email" name="customer_email" class="form-control" id="customer_email" value="{{ old('customer_email') }}">
                                                                @error('customer_email')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
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
                                                                            {{ (old('table_id') ?? request('table_id')) == $table->id ? 'selected' : '' }}>
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
                                                                <input type="number" name="party_size" class="form-control" id="party_size" required value="{{ old('party_size', 2) }}" min="1">
                                                                @error('party_size')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="reservation_date">{{ __('Date') }}<span class="text-danger">*</span></label>
                                                                <input type="date" name="reservation_date" class="form-control" id="reservation_date" required value="{{ old('reservation_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                                                                @error('reservation_date')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="reservation_time">{{ __('Time') }}<span class="text-danger">*</span></label>
                                                                <select name="reservation_time" id="reservation_time" class="form-control" required>
                                                                    <option value="">{{ __('Select Table & Date First') }}</option>
                                                                </select>
                                                                @error('reservation_time')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="duration_minutes">{{ __('Duration (minutes)') }}</label>
                                                                <select name="duration_minutes" id="duration_minutes" class="form-control">
                                                                    <option value="60" {{ old('duration_minutes') == 60 ? 'selected' : '' }}>1 {{ __('hour') }}</option>
                                                                    <option value="90" {{ old('duration_minutes') == 90 ? 'selected' : '' }}>1.5 {{ __('hours') }}</option>
                                                                    <option value="120" {{ old('duration_minutes', 120) == 120 ? 'selected' : '' }}>2 {{ __('hours') }}</option>
                                                                    <option value="180" {{ old('duration_minutes') == 180 ? 'selected' : '' }}>3 {{ __('hours') }}</option>
                                                                    <option value="240" {{ old('duration_minutes') == 240 ? 'selected' : '' }}>4 {{ __('hours') }}</option>
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
                                                        <textarea name="special_requests" class="form-control" id="special_requests" rows="3" placeholder="e.g., Birthday celebration, high chair needed, allergies...">{{ old('special_requests') }}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="notes">{{ __('Internal Notes') }}</label>
                                                        <textarea name="notes" class="form-control" id="notes" rows="2" placeholder="Notes visible only to staff...">{{ old('notes') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <!-- Availability Check -->
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>{{ __('Availability') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div id="availability-status" class="text-center py-3">
                                                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted">{{ __('Select table and date to check availability') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Options -->
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h5>{{ __('Options') }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" name="auto_confirm" value="1" id="auto_confirm" {{ old('auto_confirm') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="auto_confirm">
                                                            {{ __('Auto-confirm reservation') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="card mt-3">
                                                <div class="card-body text-center">
                                                    <x-admin.save-button :text="__('Create Reservation')"></x-admin.save-button>
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

            // Auto-fill customer details when selecting existing customer
            $('#customer_id').on('change', function() {
                var selected = $(this).find(':selected');
                if (selected.val()) {
                    $('#customer_name').val(selected.data('name'));
                    $('#customer_phone').val(selected.data('phone'));
                    $('#customer_email').val(selected.data('email'));
                } else {
                    $('#customer_name').val('');
                    $('#customer_phone').val('');
                    $('#customer_email').val('');
                }
            });

            // Load available timeslots when table or date changes
            $('#table_id, #reservation_date, #duration_minutes').on('change', function() {
                loadTimeslots();
            });

            function loadTimeslots() {
                var tableId = $('#table_id').val();
                var date = $('#reservation_date').val();
                var duration = $('#duration_minutes').val();

                if (!tableId || !date) {
                    return;
                }

                $('#availability-status').html('<i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2">{{ __("Checking availability...") }}</p>');

                $.ajax({
                    url: "{{ route('admin.reservations.timeslots') }}",
                    type: 'GET',
                    data: {
                        table_id: tableId,
                        date: date,
                        duration: duration
                    },
                    success: function(response) {
                        if (response.success) {
                            var timeslots = response.timeslots;
                            var select = $('#reservation_time');
                            select.empty();

                            if (timeslots.length > 0) {
                                select.append('<option value="">{{ __("Select Time") }}</option>');
                                timeslots.forEach(function(slot) {
                                    select.append('<option value="' + slot + '">' + slot + '</option>');
                                });

                                $('#availability-status').html(
                                    '<i class="fas fa-check-circle fa-2x text-success"></i>' +
                                    '<p class="mt-2 text-success"><strong>' + timeslots.length + '</strong> {{ __("slots available") }}</p>'
                                );
                            } else {
                                select.append('<option value="">{{ __("No slots available") }}</option>');
                                $('#availability-status').html(
                                    '<i class="fas fa-times-circle fa-2x text-danger"></i>' +
                                    '<p class="mt-2 text-danger">{{ __("No available slots for this date") }}</p>'
                                );
                            }
                        }
                    },
                    error: function() {
                        $('#availability-status').html(
                            '<i class="fas fa-exclamation-circle fa-2x text-warning"></i>' +
                            '<p class="mt-2 text-warning">{{ __("Failed to check availability") }}</p>'
                        );
                    }
                });
            }

            // Initial load if table is pre-selected
            if ($('#table_id').val()) {
                loadTimeslots();
            }
        });
    </script>
@endpush
