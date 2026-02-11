@extends('admin.layouts.master')

@section('title')
    {{ __('Bookings') }}
@endsection

@section('content')
    <div class="card">
        <div class="card-body pb-0">
            <form class="search_form" action="" method="GET">
                <div class="row">
                    <div class="col-xxl-2 col-md-6 col-lg-4">
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="">{{ __('All Status') }}</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-6 col-lg-4">
                        <div class="form-group">
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                    </div>
                    <div class="col-xxl-2 col-md-6 col-lg-4">
                        <div class="form-group">
                            <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                            <button type="submit" class="btn bg-label-primary">{{ __('Search') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Bookings') }}</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Booking #') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Date & Time') }}</th>
                            <th>{{ __('Guests') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td><strong>{{ $booking->booking_number }}</strong></td>
                                <td>{{ $booking->name }}</td>
                                <td>
                                    {{ $booking->booking_date->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ $booking->booking_time->format('h:i A') }}</small>
                                </td>
                                <td>{{ $booking->number_of_guests }}</td>
                                <td>{{ $booking->phone }}</td>
                                <td>
                                    @switch($booking->status)
                                        @case('pending')
                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @break
                                        @case('confirmed')
                                            <span class="badge bg-success">{{ __('Confirmed') }}</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-info">{{ __('Completed') }}</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('admin.restaurant.bookings.show', $booking) }}" class="btn btn-sm btn-icon btn-info">
                                        <i class="bx bx-show"></i>
                                    </a>
                                    <form action="{{ route('admin.restaurant.bookings.destroy', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">{{ __('No bookings found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $bookings->links() }}</div>
        </div>
    </div>
@endsection
