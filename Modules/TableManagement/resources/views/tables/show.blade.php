@extends('admin.layouts.master')
@section('title', __('Table Details'))

@push('css')
<style>
    .table-visual {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 2.5rem;
        font-weight: 700;
        color: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .table-visual.available {
        background: linear-gradient(135deg, #71dd37 0%, #5fc52e 100%);
    }
    .table-visual.occupied {
        background: linear-gradient(135deg, #ff3e1d 0%, #ff6b4d 100%);
    }
    .table-visual.reserved {
        background: linear-gradient(135deg, #ffab00 0%, #ffc107 100%);
    }
    .info-item {
        padding: 1rem;
        border-radius: 8px;
        background: #f8f9fa;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
    }
    .info-item .info-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.25rem;
    }
    .info-item .info-icon.primary { background: rgba(105, 108, 255, 0.1); color: #696cff; }
    .info-item .info-icon.success { background: rgba(113, 221, 55, 0.1); color: #71dd37; }
    .info-item .info-icon.info { background: rgba(3, 195, 236, 0.1); color: #03c3ec; }
    .info-item .info-icon.warning { background: rgba(255, 171, 0, 0.1); color: #ffab00; }
    .info-item .info-label {
        font-size: 0.75rem;
        color: #8592a3;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-item .info-value {
        font-weight: 600;
        color: #566a7f;
    }
    .status-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    .quick-action-btn {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .reservation-item {
        padding: 1rem;
        border-radius: 8px;
        background: #f8f9fa;
        margin-bottom: 0.75rem;
        border-left: 4px solid #696cff;
    }
    .reservation-item:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-2">
                                <li class="breadcrumb-item"><a href="{{ route('admin.tables.index') }}">{{ __('Tables') }}</a></li>
                                <li class="breadcrumb-item active">{{ $table->name }}</li>
                            </ol>
                        </nav>
                        <h4 class="mb-0">{{ __('Table Details') }}</h4>
                    </div>
                    <div class="d-flex gap-2">
                        @adminCan('table.edit')
                        <a href="{{ route('admin.tables.edit', $table->id) }}" class="btn btn-warning">
                            <i class="bx bx-edit me-1"></i>{{ __('Edit') }}
                        </a>
                        @endadminCan
                        <a href="{{ route('admin.tables.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
                        </a>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Left Column - Status & Actions -->
                    <div class="col-lg-4 col-md-5 order-md-2 order-lg-1">
                        <!-- Table Status Card -->
                        <div class="card status-card mb-4">
                            <div class="card-body text-center py-4">
                                <div class="table-visual {{ $table->status }} mb-3">
                                    {{ $table->table_number }}
                                </div>
                                <h4 class="mb-2">{{ $table->name }}</h4>
                                <span class="badge bg-{{ $table->status_badge }} fs-6 px-3 py-2">
                                    {{ $table->status_label }}
                                </span>
                                <div class="mt-3">
                                    <span class="text-muted">
                                        <i class="bx bx-user me-1"></i>{{ $table->capacity }} {{ __('seats') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Order -->
                        @if ($table->currentSale)
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary">
                                <h6 class="mb-0 text-white">
                                    <i class="bx bx-receipt me-2"></i>{{ __('Current Order') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">{{ __('Invoice') }}</span>
                                    <strong class="text-primary">{{ $table->currentSale->invoice }}</strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted">{{ __('Total') }}</span>
                                    <strong class="fs-5">{{ number_format($table->currentSale->grand_total, 2) }}</strong>
                                </div>
                                @adminCan('sale.view')
                                <a href="{{ route('admin.sales.show', $table->currentSale->id) }}" class="btn btn-primary w-100 quick-action-btn">
                                    <i class="bx bx-show"></i>{{ __('View Order') }}
                                </a>
                                @endadminCan
                            </div>
                        </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bx bx-zap me-2 text-warning"></i>{{ __('Quick Actions') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.reservations.create') }}?table_id={{ $table->id }}" class="btn btn-outline-info quick-action-btn">
                                        <i class="bx bx-calendar-plus"></i>{{ __('Create Reservation') }}
                                    </a>
                                    @if ($table->isOccupied())
                                    <button class="btn btn-outline-warning quick-action-btn release-table" data-id="{{ $table->id }}">
                                        <i class="bx bx-log-out"></i>{{ __('Release Table') }}
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Information -->
                    <div class="col-lg-8 col-md-7 order-md-1 order-lg-2">
                        <!-- Table Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="bx bx-info-circle me-2 text-primary"></i>{{ __('Table Information') }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-icon primary">
                                                <i class="bx bx-hash"></i>
                                            </div>
                                            <div>
                                                <div class="info-label">{{ __('Table Number') }}</div>
                                                <div class="info-value">{{ $table->table_number }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-icon success">
                                                <i class="bx bx-user"></i>
                                            </div>
                                            <div>
                                                <div class="info-label">{{ __('Capacity') }}</div>
                                                <div class="info-value">{{ $table->capacity }} {{ __('seats') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-icon info">
                                                <i class="bx bx-building"></i>
                                            </div>
                                            <div>
                                                <div class="info-label">{{ __('Floor') }}</div>
                                                <div class="info-value">{{ $table->floor ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-icon warning">
                                                <i class="bx bx-map"></i>
                                            </div>
                                            <div>
                                                <div class="info-label">{{ __('Section') }}</div>
                                                <div class="info-value">{{ $table->section ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-icon primary">
                                                <i class="bx bx-shape-circle"></i>
                                            </div>
                                            <div>
                                                <div class="info-label">{{ __('Shape') }}</div>
                                                <div class="info-value">{{ ucfirst($table->shape) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-icon {{ $table->is_active ? 'success' : 'warning' }}">
                                                <i class="bx bx-{{ $table->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                            </div>
                                            <div>
                                                <div class="info-label">{{ __('Active Status') }}</div>
                                                <div class="info-value">
                                                    @if ($table->is_active)
                                                        <span class="text-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="text-danger">{{ __('Inactive') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if ($table->notes)
                                <hr class="my-3">
                                <div class="info-item" style="background: #fff8e1; border-left: 4px solid #ffab00;">
                                    <div class="info-icon warning">
                                        <i class="bx bx-note"></i>
                                    </div>
                                    <div>
                                        <div class="info-label">{{ __('Notes') }}</div>
                                        <div class="info-value">{{ $table->notes }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Today's Reservations -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="bx bx-calendar me-2 text-info"></i>{{ __("Today's Reservations") }}
                                </h6>
                                <span class="badge bg-label-info">{{ $table->todayReservations->count() }} {{ __('bookings') }}</span>
                            </div>
                            <div class="card-body">
                                @if ($table->todayReservations->count() > 0)
                                    @foreach ($table->todayReservations as $res)
                                    <div class="reservation-item" style="border-left-color:
                                        @if($res->status == 'confirmed') #71dd37
                                        @elseif($res->status == 'pending') #ffab00
                                        @elseif($res->status == 'seated') #03c3ec
                                        @else #696cff
                                        @endif
                                    ;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">{{ $res->customer_name }}</h6>
                                                <div class="text-muted small">
                                                    <i class="bx bx-phone me-1"></i>{{ $res->customer_phone }}
                                                </div>
                                            </div>
                                            <span class="badge bg-{{ $res->status_badge }}">{{ $res->status_label }}</span>
                                        </div>
                                        <div class="d-flex gap-3 mt-2 text-muted small">
                                            <span><i class="bx bx-time-five me-1"></i>{{ $res->reservation_time->format('H:i') }}</span>
                                            <span><i class="bx bx-group me-1"></i>{{ $res->party_size }} {{ __('guests') }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="bx bx-calendar-check text-muted" style="font-size: 4rem;"></i>
                                        </div>
                                        <h6 class="text-muted mb-1">{{ __('No Reservations Today') }}</h6>
                                        <p class="text-muted small mb-3">{{ __('This table is free for walk-in guests') }}</p>
                                        <a href="{{ route('admin.reservations.create') }}?table_id={{ $table->id }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-plus me-1"></i>{{ __('Add Reservation') }}
                                        </a>
                                    </div>
                                @endif
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
                    title: '{{ __("Release Table?") }}',
                    text: '{{ __("This will mark the table as available.") }}',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#696cff',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: '{{ __("Yes, release it!") }}',
                    cancelButtonText: '{{ __("Cancel") }}'
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
