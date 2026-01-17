@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Select Table') }}</title>
@endsection

@push('css')
<style>
    .table-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 20px;
        padding: 20px;
    }
    .table-item {
        aspect-ratio: 1;
        border-radius: 15px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        border: 3px solid transparent;
        position: relative;
    }
    .table-item:hover {
        transform: scale(1.08);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    .table-item.available {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }
    .table-item.available:hover {
        border-color: #fff;
    }
    .table-item.partial {
        background: linear-gradient(135deg, #fd7e14, #ffc107);
        color: white;
    }
    .table-item.partial:hover {
        border-color: #fff;
    }
    .table-item.occupied {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        cursor: not-allowed;
        opacity: 0.7;
    }
    .table-item.reserved {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
        cursor: not-allowed;
        opacity: 0.7;
    }
    .table-item.maintenance {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
        cursor: not-allowed;
        opacity: 0.5;
    }
    .table-name {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .table-capacity {
        font-size: 1rem;
        opacity: 0.9;
        margin-top: 5px;
    }
    .status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 0.7rem;
        padding: 3px 8px;
        border-radius: 10px;
        background: rgba(255,255,255,0.3);
    }
    .seats-available {
        font-size: 0.8rem;
        font-weight: bold;
        margin-top: 3px;
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="page-title mb-0">
                        <i class="fas fa-chair me-2"></i>{{ __('Select a Table') }}
                    </h3>
                    <p class="text-muted mb-0">{{ __('Choose a table to start a new order') }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.waiter.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Dashboard') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex justify-content-center gap-4 flex-wrap">
                    <span><span class="badge bg-success px-3">&nbsp;</span> {{ __('Available') }}</span>
                    <span><span class="badge px-3" style="background: #fd7e14;">&nbsp;</span> {{ __('Partial') }}</span>
                    <span><span class="badge bg-danger px-3">&nbsp;</span> {{ __('Full') }}</span>
                    <span><span class="badge bg-secondary px-3">&nbsp;</span> {{ __('Reserved/Maintenance') }}</span>
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
                            @else
                                {{ ucfirst($table->status) }}
                            @endif
                        </span>
                        <i class="fas fa-utensils fa-2x mb-2 opacity-75"></i>
                        <div class="table-name">{{ $table->name }}</div>
                        <div class="table-capacity">
                            <i class="fas fa-users"></i> {{ $table->capacity }} {{ __('seats') }}
                        </div>
                        @if($table->occupied_seats > 0)
                        <div class="seats-available">
                            @if($availableSeats > 0)
                                <i class="fas fa-chair"></i> {{ $availableSeats }} {{ __('available') }}
                            @else
                                <i class="fas fa-ban"></i> {{ __('Full') }}
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
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
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
