{{--
    Waiter Selector Component

    Usage:
    @include('pos::components.waiter-selector', [
        'waiters' => $waiters,
        'selected' => null,
        'name' => 'waiter_id',
        'required' => false
    ])
--}}

@php
    $waiters = $waiters ?? collect();
    $selected = $selected ?? null;
    $name = $name ?? 'waiter_id';
    $required = $required ?? false;
@endphp

<div class="waiter-selector-wrapper">
    <label class="form-label fw-semibold">
        {{ __('Assign Waiter') }}
        @if(!$required)
        <span class="text-muted fw-normal">({{ __('Optional') }})</span>
        @endif
    </label>

    <div class="waiter-grid" id="waiterGrid">
        @if(!$required)
        <label class="waiter-option {{ !$selected ? 'active' : '' }}">
            <input type="radio"
                   name="{{ $name }}"
                   value=""
                   {{ !$selected ? 'checked' : '' }}>
            <div class="waiter-card no-waiter">
                <div class="waiter-avatar">
                    <i class="bx bx-user-x"></i>
                </div>
                <span class="waiter-name">{{ __('No Waiter') }}</span>
            </div>
        </label>
        @endif

        @foreach($waiters as $waiter)
        <label class="waiter-option {{ $selected == $waiter->id ? 'active' : '' }}">
            <input type="radio"
                   name="{{ $name }}"
                   value="{{ $waiter->id }}"
                   {{ $selected == $waiter->id ? 'checked' : '' }}
                   {{ $required && $loop->first && !$selected ? 'checked' : '' }}>
            <div class="waiter-card">
                <div class="waiter-avatar">
                    @if($waiter->image)
                    <img src="{{ asset($waiter->image) }}" alt="{{ $waiter->name }}">
                    @else
                    <i class="bx bx-user"></i>
                    @endif
                </div>
                <span class="waiter-name">{{ $waiter->name }}</span>
                @if(isset($waiter->active_orders_count))
                <span class="waiter-orders">{{ $waiter->active_orders_count }} {{ __('orders') }}</span>
                @endif
            </div>
        </label>
        @endforeach
    </div>

    @if($waiters->isEmpty())
    <div class="no-waiters-message">
        <i class="bx bx-info-circle"></i>
        <span>{{ __('No waiters available') }}</span>
    </div>
    @endif
</div>

<style>
.waiter-selector-wrapper {
    width: 100%;
}

.waiter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;
}

.waiter-option {
    cursor: pointer;
    margin: 0;
}

.waiter-option input {
    display: none;
}

.waiter-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 8px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: #fff;
    transition: all 0.2s ease;
    text-align: center;
}

.waiter-card:hover {
    border-color: #696cff;
    transform: translateY(-2px);
}

.waiter-option.active .waiter-card,
.waiter-option input:checked + .waiter-card {
    border-color: #696cff;
    background: linear-gradient(135deg, #f0f0ff, #e8e8ff);
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.2);
}

.waiter-card.no-waiter {
    background: #f8f9fa;
}

.waiter-option.active .waiter-card.no-waiter {
    background: #fff;
}

.waiter-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #f0f0ff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
    overflow: hidden;
}

.waiter-avatar i {
    font-size: 24px;
    color: #696cff;
}

.waiter-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.waiter-option.active .waiter-avatar,
.waiter-option input:checked + .waiter-card .waiter-avatar {
    background: #696cff;
}

.waiter-option.active .waiter-avatar i,
.waiter-option input:checked + .waiter-card .waiter-avatar i {
    color: white;
}

.waiter-name {
    font-size: 13px;
    font-weight: 600;
    color: #232333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.waiter-orders {
    font-size: 11px;
    color: #697a8d;
    margin-top: 2px;
}

.no-waiters-message {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 20px;
    color: #697a8d;
    background: #f8f9fa;
    border-radius: 8px;
}

.no-waiters-message i {
    font-size: 20px;
}

@media (max-width: 576px) {
    .waiter-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .waiter-avatar {
        width: 40px;
        height: 40px;
    }

    .waiter-avatar i {
        font-size: 20px;
    }

    .waiter-name {
        font-size: 12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initWaiterSelector();
});

function initWaiterSelector() {
    const waiterOptions = document.querySelectorAll('.waiter-option');

    waiterOptions.forEach(function(option) {
        option.addEventListener('click', function() {
            // Update active states
            document.querySelectorAll('.waiter-option').forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');

            // Trigger custom event
            const input = this.querySelector('input');
            document.dispatchEvent(new CustomEvent('waiterSelected', {
                detail: {
                    waiterId: input.value,
                    waiterName: this.querySelector('.waiter-name').textContent
                }
            }));
        });
    });
}
</script>
