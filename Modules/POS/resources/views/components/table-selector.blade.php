{{--
    Table Selector Component

    Usage:
    @include('pos::components.table-selector', [
        'tables' => $tables,
        'selected' => null,
        'name' => 'table_id'
    ])
--}}

@php
    $tables = $tables ?? collect();
    $selected = $selected ?? null;
    $name = $name ?? 'table_id';
@endphp

<div class="table-selector-wrapper" data-input-name="{{ $name }}">
    <input type="hidden" name="{{ $name }}" id="tableSelector_{{ $name }}" value="{{ $selected }}">

    <div class="table-status-legend mb-3">
        <span class="legend-item">
            <span class="status-dot available"></span> {{ __('Available') }}
        </span>
        <span class="legend-item">
            <span class="status-dot partial"></span> {{ __('Partial') }}
        </span>
        <span class="legend-item">
            <span class="status-dot occupied"></span> {{ __('Occupied') }}
        </span>
    </div>

    <div class="table-grid" id="tableGrid">
        @forelse($tables as $table)
            @php
                $availableSeats = $table->capacity - ($table->occupied_seats ?? 0);
                $status = 'available';
                if ($table->status === 'occupied' || $availableSeats <= 0) {
                    $status = 'occupied';
                } elseif ($table->occupied_seats > 0) {
                    $status = 'partial';
                } elseif ($table->status === 'reserved') {
                    $status = 'reserved';
                } elseif ($table->status === 'maintenance') {
                    $status = 'maintenance';
                }
                $isSelectable = in_array($status, ['available', 'partial']);
            @endphp
            <div class="table-card {{ $status }} {{ $selected == $table->id ? 'selected' : '' }} {{ !$isSelectable ? 'disabled' : '' }}"
                 data-table-id="{{ $table->id }}"
                 data-capacity="{{ $table->capacity }}"
                 data-available="{{ $availableSeats }}"
                 data-status="{{ $status }}"
                 {{ !$isSelectable ? 'data-disabled=true' : '' }}>
                <div class="table-icon">
                    <i class="bx bx-chair"></i>
                </div>
                <div class="table-name">{{ $table->name }}</div>
                <div class="table-capacity">
                    <i class="bx bx-user"></i>
                    <span class="available-seats">{{ $availableSeats }}</span>/{{ $table->capacity }}
                </div>
                <div class="table-status-badge">
                    @if($status === 'available')
                        {{ __('Available') }}
                    @elseif($status === 'partial')
                        {{ __('Partial') }}
                    @elseif($status === 'occupied')
                        {{ __('Occupied') }}
                    @elseif($status === 'reserved')
                        {{ __('Reserved') }}
                    @else
                        {{ __('Unavailable') }}
                    @endif
                </div>
            </div>
        @empty
            <div class="no-tables-message col-span-full">
                <i class="bx bx-info-circle"></i>
                <p>{{ __('No tables available') }}</p>
            </div>
        @endforelse
    </div>
</div>

<style>
.table-selector-wrapper {
    width: 100%;
}

.table-status-legend {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #697a8d;
}

.status-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.status-dot.available { background: #71dd37; }
.status-dot.partial { background: #ffab00; }
.status-dot.occupied { background: #ff3e1d; }

.table-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

@media (max-width: 768px) {
    .table-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 576px) {
    .table-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.table-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
    position: relative;
}

.table-card:hover:not(.disabled) {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
}

.table-card.selected {
    border-color: #696cff;
    background: linear-gradient(135deg, #f0f0ff, #e8e8ff);
    box-shadow: 0 4px 15px rgba(105, 108, 255, 0.3);
}

.table-card.selected .table-name {
    color: #696cff;
}

/* Status border colors */
.table-card.available {
    border-left: 4px solid #71dd37;
}

.table-card.partial {
    border-left: 4px solid #ffab00;
}

.table-card.occupied,
.table-card.reserved,
.table-card.maintenance {
    border-left: 4px solid #ff3e1d;
    opacity: 0.6;
    cursor: not-allowed;
}

.table-card.disabled {
    pointer-events: none;
}

.table-icon {
    font-size: 28px;
    color: #697a8d;
    margin-bottom: 8px;
}

.table-card.selected .table-icon {
    color: #696cff;
}

.table-name {
    font-size: 16px;
    font-weight: 700;
    color: #232333;
    margin-bottom: 4px;
}

.table-capacity {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    color: #697a8d;
    margin-bottom: 6px;
}

.table-capacity i {
    font-size: 14px;
}

.table-status-badge {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 3px 8px;
    border-radius: 4px;
    background: #f5f5f9;
    color: #697a8d;
}

.table-card.available .table-status-badge {
    background: rgba(113, 221, 55, 0.15);
    color: #71dd37;
}

.table-card.partial .table-status-badge {
    background: rgba(255, 171, 0, 0.15);
    color: #e69a00;
}

.table-card.occupied .table-status-badge,
.table-card.reserved .table-status-badge {
    background: rgba(255, 62, 29, 0.15);
    color: #ff3e1d;
}

.no-tables-message {
    grid-column: 1 / -1;
    text-align: center;
    padding: 40px 20px;
    color: #697a8d;
}

.no-tables-message i {
    font-size: 48px;
    margin-bottom: 10px;
    display: block;
}

.no-tables-message p {
    margin: 0;
    font-size: 16px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initTableSelector();
});

function initTableSelector() {
    document.querySelectorAll('.table-selector-wrapper').forEach(function(wrapper) {
        const tableCards = wrapper.querySelectorAll('.table-card:not(.disabled)');
        const tableInput = wrapper.querySelector('input[type="hidden"]');

        tableCards.forEach(function(card) {
            card.addEventListener('click', function() {
                // Remove selected from all in this wrapper
                wrapper.querySelectorAll('.table-card').forEach(c => c.classList.remove('selected'));

                // Add selected to clicked
                this.classList.add('selected');

                // Update hidden input
                const tableId = this.dataset.tableId;
                if (tableInput) {
                    tableInput.value = tableId;
                }

                // Trigger custom event
                document.dispatchEvent(new CustomEvent('tableSelected', {
                    detail: {
                        tableId: tableId,
                        tableName: this.querySelector('.table-name').textContent,
                        capacity: parseInt(this.dataset.capacity),
                        availableSeats: parseInt(this.dataset.available),
                        status: this.dataset.status
                    }
                }));
            });
        });
    });
}

// Function to refresh tables via AJAX
function refreshTableSelector(url) {
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.tables) {
                // Update table grid HTML
                const grid = document.getElementById('tableGrid');
                // Re-render tables...
            }
        });
}
</script>
