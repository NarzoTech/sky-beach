@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Reservations Calendar') }}</title>
@endsection

@push('css')
<style>
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
        background: #dee2e6;
    }
    .calendar-header {
        background: #495057;
        color: white;
        padding: 10px;
        text-align: center;
        font-weight: bold;
    }
    .calendar-day {
        background: white;
        min-height: 120px;
        padding: 5px;
    }
    .calendar-day.other-month {
        background: #f8f9fa;
    }
    .calendar-day.today {
        background: #e7f1ff;
    }
    .day-number {
        font-weight: bold;
        margin-bottom: 5px;
    }
    .calendar-day.today .day-number {
        color: #0d6efd;
    }
    .reservation-badge {
        display: block;
        padding: 2px 5px;
        margin-bottom: 2px;
        font-size: 11px;
        border-radius: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
    }
    .reservation-badge.pending {
        background: #ffc107;
        color: #000;
    }
    .reservation-badge.confirmed {
        background: #0dcaf0;
        color: #000;
    }
    .reservation-badge.seated {
        background: #0d6efd;
        color: #fff;
    }
    .reservation-badge.completed {
        background: #198754;
        color: #fff;
    }
    .more-badge {
        background: #6c757d;
        color: white;
    }
</style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="section_title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ __('Reservations Calendar') }}
                    </h4>
                    <div>
                        <a href="{{ route('admin.reservations.today') }}" class="btn btn-info">
                            <i class="fa fa-clock"></i> {{ __('Today') }}
                        </a>
                        <a href="{{ route('admin.reservations.index') }}" class="btn btn-primary">
                            <i class="fa fa-list"></i> {{ __('List View') }}
                        </a>
                        <a href="{{ route('admin.reservations.create') }}" class="btn btn-success">
                            <i class="fa fa-plus"></i> {{ __('New Reservation') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Navigation -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                @php
                    $currentMonth = \Carbon\Carbon::parse($startDate);
                    $prevMonth = $currentMonth->copy()->subMonth();
                    $nextMonth = $currentMonth->copy()->addMonth();
                @endphp
                <a href="{{ route('admin.reservations.calendar', ['start' => $prevMonth->startOfMonth()->format('Y-m-d'), 'end' => $prevMonth->endOfMonth()->format('Y-m-d')]) }}" class="btn btn-outline-primary">
                    <i class="fas fa-chevron-left"></i> {{ $prevMonth->format('F Y') }}
                </a>
                <h4 class="mb-0">{{ $currentMonth->format('F Y') }}</h4>
                <a href="{{ route('admin.reservations.calendar', ['start' => $nextMonth->startOfMonth()->format('Y-m-d'), 'end' => $nextMonth->endOfMonth()->format('Y-m-d')]) }}" class="btn btn-outline-primary">
                    {{ $nextMonth->format('F Y') }} <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter by Table -->
    <div class="card mb-4">
        <div class="card-body py-2">
            <form action="" method="GET" class="d-flex align-items-center gap-3">
                <input type="hidden" name="start" value="{{ $startDate }}">
                <input type="hidden" name="end" value="{{ $endDate }}">
                <label class="mb-0">{{ __('Filter by Table:') }}</label>
                <select name="table_id" class="form-control form-control-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">{{ __('All Tables') }}</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}" {{ request('table_id') == $table->id ? 'selected' : '' }}>
                            {{ $table->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Calendar -->
    <div class="card">
        <div class="card-body p-0">
            <div class="calendar-grid">
                <!-- Headers -->
                <div class="calendar-header">{{ __('Sun') }}</div>
                <div class="calendar-header">{{ __('Mon') }}</div>
                <div class="calendar-header">{{ __('Tue') }}</div>
                <div class="calendar-header">{{ __('Wed') }}</div>
                <div class="calendar-header">{{ __('Thu') }}</div>
                <div class="calendar-header">{{ __('Fri') }}</div>
                <div class="calendar-header">{{ __('Sat') }}</div>

                <!-- Days -->
                @php
                    $startOfMonth = \Carbon\Carbon::parse($startDate)->startOfMonth();
                    $endOfMonth = \Carbon\Carbon::parse($startDate)->endOfMonth();
                    $startOfCalendar = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                    $endOfCalendar = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                    $today = now()->format('Y-m-d');
                @endphp

                @for ($date = $startOfCalendar->copy(); $date->lte($endOfCalendar); $date->addDay())
                    @php
                        $dateKey = $date->format('Y-m-d');
                        $dayReservations = $reservations[$dateKey] ?? collect([]);
                        $isCurrentMonth = $date->month == $startOfMonth->month;
                        $isToday = $dateKey == $today;
                    @endphp
                    <div class="calendar-day {{ !$isCurrentMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
                        <div class="day-number">{{ $date->day }}</div>
                        @foreach ($dayReservations->take(3) as $res)
                            <a href="{{ route('admin.reservations.show', $res->id) }}" class="reservation-badge {{ $res->status }}" title="{{ $res->customer_name }} - {{ $res->table->name ?? 'N/A' }}">
                                {{ $res->reservation_time->format('H:i') }} {{ $res->customer_name }}
                            </a>
                        @endforeach
                        @if ($dayReservations->count() > 3)
                            <span class="reservation-badge more-badge">
                                +{{ $dayReservations->count() - 3 }} {{ __('more') }}
                            </span>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="card mt-4">
        <div class="card-body py-2">
            <div class="d-flex gap-4 justify-content-center">
                <span><span class="reservation-badge pending d-inline px-2">{{ __('Pending') }}</span></span>
                <span><span class="reservation-badge confirmed d-inline px-2">{{ __('Confirmed') }}</span></span>
                <span><span class="reservation-badge seated d-inline px-2">{{ __('Seated') }}</span></span>
                <span><span class="reservation-badge completed d-inline px-2">{{ __('Completed') }}</span></span>
            </div>
        </div>
    </div>
@endsection
