@extends('admin.layouts.master')
@section('title', __('Table Layout'))

@push('css')
<style>
    .floor-layout {
        position: relative;
        min-height: 500px;
        background: #f8f9fa;
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 20px;
    }

    .table-item {
        position: absolute;
        cursor: move;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        min-width: 80px;
        min-height: 80px;
    }

    .table-item:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .table-item.square {
        border-radius: 8px;
    }

    .table-item.round {
        border-radius: 50%;
    }

    .table-item.rectangle {
        border-radius: 8px;
        min-width: 120px;
    }

    .table-item.available {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .table-item.occupied {
        background: linear-gradient(135deg, #dc3545, #e74c3c);
        color: white;
    }

    .table-item.reserved {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: white;
    }

    .table-item.maintenance {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
    }

    .table-name {
        font-weight: bold;
        font-size: 12px;
    }

    .table-number {
        font-size: 10px;
        opacity: 0.9;
    }

    .table-capacity {
        font-size: 10px;
        margin-top: 2px;
    }

    .floor-tabs .nav-link {
        font-weight: 600;
    }

    .legend-item {
        display: inline-flex;
        align-items: center;
        margin-right: 15px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 5px;
    }

    .save-positions-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
</style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="section_title mb-0">{{ __('Table Layout') }}</h4>
                    <div>
                        <a href="{{ route('admin.tables.index') }}" class="btn btn-primary">
                            <i class="fa fa-list"></i> {{ __('List View') }}
                        </a>
                        <a href="{{ route('admin.tables.create') }}" class="btn btn-success">
                            <i class="fa fa-plus"></i> {{ __('Add Table') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-0">{{ __('Total') }}</h6>
                            <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-chair fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-0">{{ __('Available') }}</h6>
                            <h3 class="mb-0">{{ $stats['available'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-0">{{ __('Occupied') }}</h6>
                            <h3 class="mb-0">{{ $stats['occupied'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white mb-0">{{ __('Reserved') }}</h6>
                            <h3 class="mb-0">{{ $stats['reserved'] ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(135deg, #28a745, #20c997);"></div>
                        <span>{{ __('Available') }}</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(135deg, #dc3545, #e74c3c);"></div>
                        <span>{{ __('Occupied') }}</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(135deg, #ffc107, #fd7e14);"></div>
                        <span>{{ __('Reserved') }}</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: linear-gradient(135deg, #6c757d, #495057);"></div>
                        <span>{{ __('Maintenance') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floor Tabs -->
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs floor-tabs card-header-tabs" role="tablist">
                @php $firstFloor = true; @endphp
                @forelse ($tablesGrouped as $floor => $tables)
                    <li class="nav-item">
                        <a class="nav-link {{ $firstFloor ? 'active' : '' }}" data-bs-toggle="tab" href="#floor-{{ Str::slug($floor ?: 'main') }}" role="tab">
                            {{ $floor ?: __('Main Floor') }}
                            <span class="badge bg-secondary">{{ $tables->count() }}</span>
                        </a>
                    </li>
                    @php $firstFloor = false; @endphp
                @empty
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#floor-main" role="tab">
                            {{ __('Main Floor') }}
                        </a>
                    </li>
                @endforelse
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                @php $firstFloor = true; @endphp
                @forelse ($tablesGrouped as $floor => $tables)
                    <div class="tab-pane fade {{ $firstFloor ? 'show active' : '' }}" id="floor-{{ Str::slug($floor ?: 'main') }}" role="tabpanel">
                        <div class="floor-layout" data-floor="{{ $floor }}">
                            @foreach ($tables as $table)
                                <div class="table-item {{ $table->shape }} {{ $table->status }}"
                                     data-id="{{ $table->id }}"
                                     style="left: {{ $table->position_x }}px; top: {{ $table->position_y }}px;"
                                     title="{{ $table->name }} - {{ $table->status_label }}">
                                    <span class="table-name">{{ $table->name }}</span>
                                    <span class="table-number">{{ $table->table_number }}</span>
                                    <span class="table-capacity"><i class="fas fa-user"></i> {{ $table->capacity }}</span>
                                    @if ($table->upcomingReservation())
                                        <span class="table-capacity"><i class="fas fa-clock"></i> {{ $table->upcomingReservation()->reservation_time->format('H:i') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @php $firstFloor = false; @endphp
                @empty
                    <div class="tab-pane fade show active" id="floor-main" role="tabpanel">
                        <div class="floor-layout">
                            <div class="text-center py-5">
                                <i class="fas fa-chair fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">{{ __('No tables found') }}</h5>
                                <a href="{{ route('admin.tables.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> {{ __('Add First Table') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <button class="btn btn-primary btn-lg save-positions-btn d-none" id="savePositions">
        <i class="fas fa-save"></i> {{ __('Save Positions') }}
    </button>
@endsection

@push('js')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        'use strict';

        var positionsChanged = false;

        // Make tables draggable
        $('.table-item').draggable({
            containment: '.floor-layout',
            stop: function(event, ui) {
                positionsChanged = true;
                $('#savePositions').removeClass('d-none');
            }
        });

        // Click to view table details
        $('.table-item').on('click', function(e) {
            if (!$(this).hasClass('ui-draggable-dragging')) {
                var id = $(this).data('id');
                window.location.href = "{{ url('admin/tables') }}/" + id;
            }
        });

        // Save positions
        $('#savePositions').on('click', function() {
            var positions = [];
            $('.table-item').each(function() {
                positions.push({
                    id: $(this).data('id'),
                    x: parseInt($(this).css('left')),
                    y: parseInt($(this).css('top'))
                });
            });

            $.ajax({
                url: "{{ route('admin.tables.positions') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    positions: positions
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#savePositions').addClass('d-none');
                        positionsChanged = false;
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('{{ __("Failed to save positions") }}');
                }
            });
        });

        // Warn before leaving if positions changed
        $(window).on('beforeunload', function() {
            if (positionsChanged) {
                return '{{ __("You have unsaved position changes. Are you sure you want to leave?") }}';
            }
        });
    });
</script>
@endpush
