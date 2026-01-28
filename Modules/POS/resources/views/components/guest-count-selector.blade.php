{{--
    Guest Count Selector Component

    Usage:
    @include('pos::components.guest-count-selector', [
        'maxGuests' => 20,
        'selected' => 2,
        'name' => 'guest_count'
    ])
--}}

@php
    $maxGuests = (int) ($maxGuests ?? 20);
    $selected = (int) ($selected ?? 1);
    $name = $name ?? 'guest_count';
    $showQuickNumbers = min($maxGuests, 8); // Show first 8 as quick buttons
@endphp

<div class="guest-count-wrapper">
    <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $selected }}">

    <div class="guest-quick-buttons">
        @for($i = 1; $i <= $showQuickNumbers; $i++)
        <button type="button"
                class="guest-btn {{ $selected == $i ? 'selected' : '' }}"
                data-count="{{ $i }}">
            {{ $i }}
        </button>
        @endfor

        @if($maxGuests > $showQuickNumbers)
        <div class="guest-more-dropdown">
            <button type="button" class="guest-btn guest-more-btn {{ $selected > $showQuickNumbers ? 'selected' : '' }}">
                {{ $selected > $showQuickNumbers ? $selected : ($showQuickNumbers + 1) . '+' }}
                <i class="bx bx-chevron-down"></i>
            </button>
            <div class="guest-dropdown-menu">
                @for($i = $showQuickNumbers + 1; $i <= $maxGuests; $i++)
                <button type="button"
                        class="guest-dropdown-item {{ $selected == $i ? 'selected' : '' }}"
                        data-count="{{ $i }}">
                    {{ $i }} {{ __('guests') }}
                </button>
                @endfor
            </div>
        </div>
        @endif
    </div>

    <div class="guest-display mt-2">
        <i class="bx bx-user"></i>
        <span class="guest-count-text">{{ $selected }}</span>
        <span class="guest-label">{{ $selected == 1 ? __('Guest') : __('Guests') }}</span>
    </div>
</div>

<style>
.guest-count-wrapper {
    text-align: center;
}

.guest-quick-buttons {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

.guest-btn {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    background: #fff;
    font-size: 18px;
    font-weight: 600;
    color: #697a8d;
    cursor: pointer;
    transition: all 0.2s ease;
}

.guest-btn:hover {
    border-color: #696cff;
    color: #696cff;
    transform: translateY(-2px);
}

.guest-btn.selected {
    background: #696cff;
    border-color: #696cff;
    color: white;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
}

.guest-more-dropdown {
    position: relative;
}

.guest-more-btn {
    width: auto;
    min-width: 60px;
    padding: 0 12px;
    gap: 4px;
}

.guest-more-btn i {
    font-size: 14px;
    transition: transform 0.2s;
}

.guest-more-dropdown:hover .guest-more-btn i {
    transform: rotate(180deg);
}

.guest-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-top: 8px;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    z-index: 100;
    display: none;
    max-height: 200px;
    overflow-y: auto;
    min-width: 120px;
}

.guest-more-dropdown:hover .guest-dropdown-menu,
.guest-dropdown-menu:hover {
    display: block;
}

.guest-dropdown-item {
    display: block;
    width: 100%;
    padding: 10px 16px;
    border: none;
    background: none;
    text-align: left;
    font-size: 14px;
    color: #697a8d;
    cursor: pointer;
    transition: background 0.2s;
}

.guest-dropdown-item:hover {
    background: #f5f5f9;
    color: #696cff;
}

.guest-dropdown-item.selected {
    background: #696cff;
    color: white;
}

.guest-display {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    color: #697a8d;
    font-size: 14px;
}

.guest-display i {
    font-size: 18px;
}

.guest-count-text {
    font-weight: 700;
    font-size: 16px;
    color: #696cff;
}

@media (max-width: 576px) {
    .guest-btn {
        width: 44px;
        height: 44px;
        font-size: 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initGuestCountSelector();
});

function initGuestCountSelector() {
    const wrapper = document.querySelector('.guest-count-wrapper');
    if (!wrapper) return;

    const input = wrapper.querySelector('input[type="hidden"]');
    const countText = wrapper.querySelector('.guest-count-text');
    const labelText = wrapper.querySelector('.guest-label');
    const allButtons = wrapper.querySelectorAll('.guest-btn:not(.guest-more-btn), .guest-dropdown-item');
    const moreBtn = wrapper.querySelector('.guest-more-btn');

    function updateGuestCount(count) {
        // Update hidden input
        if (input) input.value = count;

        // Update display
        if (countText) countText.textContent = count;
        if (labelText) labelText.textContent = count == 1 ? '{{ __("Guest") }}' : '{{ __("Guests") }}';

        // Update button states
        wrapper.querySelectorAll('.guest-btn, .guest-dropdown-item').forEach(btn => {
            btn.classList.remove('selected');
        });

        // Find and select the right button
        const targetBtn = wrapper.querySelector(`[data-count="${count}"]`);
        if (targetBtn) {
            targetBtn.classList.add('selected');
            if (targetBtn.classList.contains('guest-dropdown-item') && moreBtn) {
                moreBtn.classList.add('selected');
                moreBtn.innerHTML = count + ' <i class="bx bx-chevron-down"></i>';
            } else if (moreBtn) {
                moreBtn.classList.remove('selected');
                moreBtn.innerHTML = '{{ $showQuickNumbers + 1 }}+ <i class="bx bx-chevron-down"></i>';
            }
        }

        // Trigger custom event
        document.dispatchEvent(new CustomEvent('guestCountChanged', {
            detail: { count: count }
        }));
    }

    // Attach click handlers
    allButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const count = parseInt(this.dataset.count);
            updateGuestCount(count);
        });
    });
}

// Function to set guest count limit based on table capacity
function setGuestCountMax(maxCapacity) {
    const wrapper = document.querySelector('.guest-count-wrapper');
    if (!wrapper) return;

    const currentCount = parseInt(wrapper.querySelector('input[type="hidden"]').value) || 1;
    const buttons = wrapper.querySelectorAll('.guest-btn:not(.guest-more-btn), .guest-dropdown-item');

    buttons.forEach(function(btn) {
        const count = parseInt(btn.dataset.count);
        if (count > maxCapacity) {
            btn.style.display = 'none';
            btn.disabled = true;
        } else {
            btn.style.display = '';
            btn.disabled = false;
        }
    });

    // If current count exceeds max, reset to max
    if (currentCount > maxCapacity) {
        const input = wrapper.querySelector('input[type="hidden"]');
        if (input) {
            input.value = maxCapacity;
            // Trigger update
            const targetBtn = wrapper.querySelector(`[data-count="${maxCapacity}"]`);
            if (targetBtn) targetBtn.click();
        }
    }
}
</script>
