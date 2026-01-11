@foreach ($availableTables ?? [] as $table)
    @php
        $availableSeats = $table->capacity - ($table->occupied_seats ?? 0);
        $isPartial = $table->occupied_seats > 0 && $availableSeats > 0;
        $isFullyOccupied = $availableSeats <= 0;
        $tableClass = $isFullyOccupied ? 'occupied' : ($isPartial ? 'partial' : $table->status);
        $canSelect = $availableSeats > 0 && $table->status !== 'reserved' && $table->status !== 'maintenance';
    @endphp
    <div class="table-card {{ $tableClass }} {{ !$canSelect ? 'disabled' : '' }}"
         data-table-id="{{ $table->id }}"
         data-table-name="{{ $table->name }}"
         data-table-capacity="{{ $table->capacity }}"
         data-table-available-seats="{{ $availableSeats }}"
         data-table-occupied-seats="{{ $table->occupied_seats ?? 0 }}"
         data-table-status="{{ $table->status }}"
         onclick="{{ $canSelect ? 'selectTable(this)' : '' }}">
        <div class="table-shape {{ $table->shape ?? 'square' }} seats-{{ min($table->capacity, 8) }}">
            <div class="table-surface">
                <span class="table-number">{{ $table->table_number ?? $table->name }}</span>
            </div>
            <!-- Chairs based on capacity - occupied chairs marked differently -->
            @for ($i = 0; $i < min($table->capacity, 8); $i++)
                <div class="chair chair-{{ $i + 1 }} {{ $i < ($table->occupied_seats ?? 0) ? 'chair-occupied' : '' }}"></div>
            @endfor
        </div>
        <div class="table-info">
            <strong>{{ $table->name }}</strong>
            @if($isPartial)
                <small class="d-block text-warning">
                    <i class="fas fa-chair"></i> {{ $availableSeats }}/{{ $table->capacity }} {{ __('seats free') }}
                </small>
            @elseif($isFullyOccupied)
                <small class="d-block text-danger">
                    <i class="fas fa-ban"></i> {{ __('Fully occupied') }}
                </small>
            @else
                <small class="d-block text-success">
                    <i class="fas fa-users"></i> {{ $table->capacity }} {{ __('seats') }}
                </small>
            @endif
            @if($table->activeOrders && $table->activeOrders->count() > 0)
                <small class="d-block text-info">
                    <i class="fas fa-receipt"></i> {{ $table->activeOrders->count() }} {{ __('active order(s)') }}
                </small>
            @endif
        </div>
    </div>
@endforeach

@if(count($availableTables ?? []) === 0)
    <div class="text-center py-5">
        <i class="fas fa-chair fa-3x text-muted mb-3"></i>
        <p class="text-muted">{{ __('No tables available. Please add tables first.') }}</p>
    </div>
@endif
