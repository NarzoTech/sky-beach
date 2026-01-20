@extends('website::layouts.master')

@section('title', __('My Reservations') . ' - ' . config('app.name'))

@section('content')
<div id="smooth-wrapper">
    <div id="smooth-content">

        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('My Reservations') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="#">{{ __('My Reservations') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========MY RESERVATIONS START===========-->
        <section class="my_reservations pt_110 xs_pt_90 pb_120 xs_pb_100">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Filter Section -->
                        <div class="filter_section mb-4 wow fadeInUp">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="filter_tabs">
                                        <a href="{{ route('website.reservations.index') }}"
                                           class="filter_tab {{ !request('filter') ? 'active' : '' }}">
                                            {{ __('All') }}
                                        </a>
                                        <a href="{{ route('website.reservations.index', ['filter' => 'upcoming']) }}"
                                           class="filter_tab {{ request('filter') === 'upcoming' ? 'active' : '' }}">
                                            {{ __('Upcoming') }}
                                        </a>
                                        <a href="{{ route('website.reservations.index', ['filter' => 'past']) }}"
                                           class="filter_tab {{ request('filter') === 'past' ? 'active' : '' }}">
                                            {{ __('Past') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                    <a href="{{ route('website.reservation.index') }}" class="common_btn btn_sm">
                                        <i class="fas fa-plus me-2"></i>{{ __('New Reservation') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Reservations List -->
                        @forelse($reservations as $reservation)
                            <div class="reservation_card wow fadeInUp mb-4 {{ $reservation->isUpcoming() ? '' : 'past' }}">
                                <div class="card_header">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <div class="booking_info">
                                                <span class="booking_number">#{{ $reservation->booking_number }}</span>
                                                <span class="confirmation_code">
                                                    <i class="fas fa-key me-1"></i>{{ $reservation->confirmation_code }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                            <span class="badge {{ $reservation->status_badge_class }}">
                                                {{ $reservation->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card_body">
                                    <div class="row">
                                        <div class="col-md-3 col-6">
                                            <div class="info_item">
                                                <i class="far fa-calendar-alt"></i>
                                                <label>{{ __('Date') }}</label>
                                                <span>{{ $reservation->booking_date->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="info_item">
                                                <i class="far fa-clock"></i>
                                                <label>{{ __('Time') }}</label>
                                                <span>{{ $reservation->booking_time->format('h:i A') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="info_item">
                                                <i class="fas fa-users"></i>
                                                <label>{{ __('Guests') }}</label>
                                                <span>{{ $reservation->number_of_guests }} {{ __('person(s)') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-6">
                                            <div class="info_item">
                                                <i class="fas fa-chair"></i>
                                                <label>{{ __('Table') }}</label>
                                                <span>{{ $reservation->table_preference_label }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($reservation->special_request)
                                        <div class="special_request mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-sticky-note me-1"></i>
                                                {{ Str::limit($reservation->special_request, 100) }}
                                            </small>
                                        </div>
                                    @endif
                                </div>

                                <div class="card_footer">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                {{ __('Booked on') }} {{ $reservation->created_at->format('M d, Y h:i A') }}
                                            </small>
                                        </div>
                                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                                            @if($reservation->canBeCancelled())
                                                <button type="button"
                                                        class="common_btn btn_sm btn_danger cancel-btn"
                                                        data-reservation-id="{{ $reservation->id }}"
                                                        data-booking-number="{{ $reservation->booking_number }}">
                                                    <i class="fas fa-times me-1"></i> {{ __('Cancel') }}
                                                </button>
                                            @endif
                                            @if($reservation->status === 'cancelled')
                                                <span class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ __('Cancelled') }}
                                                    @if($reservation->cancelled_at)
                                                        {{ $reservation->cancelled_at->diffForHumans() }}
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty_state text-center py-5 wow fadeInUp">
                                <div class="empty_icon mb-4">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <h3>{{ __('No Reservations Found') }}</h3>
                                <p class="text-muted mb-4">
                                    @if(request('filter') === 'upcoming')
                                        {{ __("You don't have any upcoming reservations.") }}
                                    @elseif(request('filter') === 'past')
                                        {{ __("You don't have any past reservations.") }}
                                    @else
                                        {{ __("You haven't made any reservations yet.") }}
                                    @endif
                                </p>
                                <a href="{{ route('website.reservation.index') }}" class="common_btn">
                                    <i class="fas fa-calendar-plus me-2"></i>{{ __('Make a Reservation') }}
                                </a>
                            </div>
                        @endforelse

                        @if($reservations->hasPages())
                            <div class="pagination_wrap mt-4 wow fadeInUp">
                                {{ $reservations->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        <!--==========MY RESERVATIONS END===========-->

        <!-- Cancel Reservation Modal -->
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Cancel Reservation') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Are you sure you want to cancel reservation') }} <strong id="cancelBookingNumber"></strong>?</p>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Reason (Optional)') }}</label>
                            <textarea id="cancelReason" class="form-control" rows="3" placeholder="{{ __('Let us know why you are cancelling...') }}"></textarea>
                        </div>
                        <p class="text-muted small">{{ __('This action cannot be undone.') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Keep Reservation') }}</button>
                        <button type="button" class="btn btn-danger" id="confirmCancelBtn">{{ __('Yes, Cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .filter_section {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }

    .filter_tabs {
        display: flex;
        gap: 10px;
    }

    .filter_tab {
        padding: 8px 20px;
        border-radius: 20px;
        background: #f8f9fa;
        color: #666;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s;
    }

    .filter_tab:hover,
    .filter_tab.active {
        background: #ff6b35;
        color: #fff;
    }

    .reservation_card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: transform 0.3s;
    }

    .reservation_card:hover {
        transform: translateY(-3px);
    }

    .reservation_card.past {
        opacity: 0.8;
    }

    .card_header {
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
    }

    .booking_number {
        font-weight: 700;
        color: #333;
        font-size: 18px;
    }

    .confirmation_code {
        margin-left: 15px;
        color: #888;
        font-size: 14px;
        font-family: monospace;
    }

    .card_body {
        padding: 25px;
    }

    .info_item {
        text-align: center;
        padding: 15px 10px;
    }

    .info_item i {
        font-size: 24px;
        color: #ff6b35;
        display: block;
        margin-bottom: 8px;
    }

    .info_item label {
        display: block;
        font-size: 12px;
        color: #888;
        margin-bottom: 3px;
    }

    .info_item span {
        font-weight: 600;
        color: #333;
    }

    .special_request {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
    }

    .card_footer {
        padding: 15px 20px;
        background: #fafafa;
        border-top: 1px solid #eee;
    }

    .common_btn.btn_sm {
        padding: 8px 15px;
        font-size: 13px;
    }

    .common_btn.btn_danger {
        background: #dc3545;
        border-color: #dc3545;
    }

    .common_btn.btn_danger:hover {
        background: #c82333;
    }

    .empty_state {
        background: #fff;
        padding: 60px 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .empty_icon {
        width: 100px;
        height: 100px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    .empty_icon i {
        font-size: 40px;
        color: #ddd;
    }

    @media (max-width: 768px) {
        .filter_tabs {
            justify-content: center;
        }

        .info_item {
            margin-bottom: 15px;
        }

        .booking_info {
            text-align: center;
        }

        .confirmation_code {
            display: block;
            margin-left: 0;
            margin-top: 5px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let cancelReservationId = null;

    // Cancel reservation modal
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            cancelReservationId = this.dataset.reservationId;
            document.getElementById('cancelBookingNumber').textContent = '#' + this.dataset.bookingNumber;
            document.getElementById('cancelReason').value = '';
            new bootstrap.Modal(document.getElementById('cancelModal')).show();
        });
    });

    // Confirm cancel
    document.getElementById('confirmCancelBtn').addEventListener('click', function() {
        if (!cancelReservationId) return;

        const reason = document.getElementById('cancelReason').value;
        const button = this;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> {{ __("Cancelling...") }}';

        fetch(`/reservation/${cancelReservationId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '{{ __("Failed to cancel reservation") }}');
                button.disabled = false;
                button.innerHTML = '{{ __("Yes, Cancel") }}';
            }
        })
        .catch(error => {
            alert('{{ __("An error occurred. Please try again.") }}');
            button.disabled = false;
            button.innerHTML = '{{ __("Yes, Cancel") }}';
        });
    });
</script>
@endpush
