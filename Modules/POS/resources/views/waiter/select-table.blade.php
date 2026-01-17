@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Select Table') }}</title>
@endsection

@push('css')
<style>
    .table-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 15px;
        padding: 15px;
    }
    .table-item {
        aspect-ratio: 1;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
        position: relative;
    }
    .table-item:hover {
        opacity: 0.85;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .table-item.available {
        background-color: #71dd37;
        color: #fff;
    }
    .table-item.partial {
        background-color: #ffab00;
        color: #fff;
    }
    .table-item.occupied {
        background-color: #ff3e1d;
        color: #fff;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .table-item.reserved {
        background-color: #8592a3;
        color: #fff;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .table-item.maintenance {
        background-color: #8592a3;
        color: #fff;
        cursor: not-allowed;
        opacity: 0.5;
    }
    .table-name {
        font-size: 1.25rem;
        font-weight: 600;
    }
    .table-capacity {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 5px;
    }
    .status-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 4px;
        background: rgba(255,255,255,0.25);
    }
    .seats-info {
        font-size: 0.8rem;
        margin-top: 3px;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h4 class="mb-1">{{ __('Select Table') }}</h4>
                    <p class="text-muted mb-0">{{ __('Choose a table to start a new order') }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.waiter.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex justify-content-center gap-4 flex-wrap">
                    <span><span class="badge bg-success">&nbsp;&nbsp;</span> {{ __('Available') }}</span>
                    <span><span class="badge bg-warning">&nbsp;&nbsp;</span> {{ __('Partial') }}</span>
                    <span><span class="badge bg-danger">&nbsp;&nbsp;</span> {{ __('Full') }}</span>
                    <span><span class="badge bg-secondary">&nbsp;&nbsp;</span> {{ __('Unavailable') }}</span>
                </div>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="card">
            <div class="card-body">
                <div class="table-grid">
                    @foreach($tables as $table)
                    @php
                        $availableSeats = $table->capacity - ($table->occupied_seats ?? 0);
                        $canTakeOrder = $table->status === 'available' ||
                                       ($table->status === 'occupied' && $availableSeats > 0);
                        $tableClass = $table->status;
                        if ($table->status === 'occupied' && $availableSeats > 0) {
                            $tableClass = 'partial';
                        }
                    @endphp
                    <div class="table-item {{ $tableClass }}"
                         @if($canTakeOrder)
                         onclick="selectTable({{ $table->id }})"
                         @endif
                         data-table-id="{{ $table->id }}">
                        <span class="status-badge">
                            @if($tableClass === 'partial')
                                {{ __('Partial') }}
                            @elseif($table->status === 'occupied')
                                {{ __('Full') }}
                            @elseif($table->status === 'available')
                                {{ __('Open') }}
                            @else
                                {{ ucfirst($table->status) }}
                            @endif
                        </span>
                        <i class="bx bx-chair bx-md mb-1"></i>
                        <div class="table-name">{{ $table->name }}</div>
                        <div class="table-capacity">
                            <i class="bx bx-user"></i> {{ $table->capacity }} {{ __('seats') }}
                        </div>
                        @if($table->occupied_seats > 0)
                        <div class="seats-info">
                            @if($availableSeats > 0)
                                {{ $availableSeats }} {{ __('available') }}
                            @else
                                {{ __('Full') }}
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                @php
                    $availableTables = $tables->filter(function($table) {
                        $availableSeats = $table->capacity - ($table->occupied_seats ?? 0);
                        return $table->status === 'available' ||
                               ($table->status === 'occupied' && $availableSeats > 0);
                    })->count();
                @endphp

                @if($availableTables === 0)
                <div class="text-center py-5 text-muted">
                    <i class="bx bx-error-circle bx-lg mb-2"></i>
                    <h5>{{ __('No tables available') }}</h5>
                    <p>{{ __('All tables are currently full or reserved.') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function selectTable(tableId) {
        window.location.href = "{{ url('admin/waiter/create-order') }}/" + tableId;
    }
</script>
@endpush
