{{--
    Take-Away Setup Modal

    Purpose: Quick info collection for take-away orders
    Shows when user clicks checkout with Take-Away order type selected

    Data required:
    - $total: Order total amount
    - $itemCount: Number of items in cart
--}}

@php
    $total = $total ?? 0;
    $itemCount = $itemCount ?? 0;
@endphp

<div class="modal fade payment-modal" id="takeawaySetupModal" tabindex="-1" aria-labelledby="takeawaySetupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header header-takeaway">
                <h5 class="modal-title" id="takeawaySetupModalLabel">
                    <i class="bx bx-shopping-bag"></i>
                    {{ __('Take-Away Order') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="takeawaySetupForm">
                    {{-- Customer Info Section --}}
                    <div class="pm-section-title">
                        <i class="bx bx-user me-2"></i>{{ __('Customer Info') }}
                        <span class="text-muted fw-normal ms-2">({{ __('Optional') }})</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">{{ __('Customer Name') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                <input type="text"
                                       name="customer_name"
                                       class="form-control pm-input"
                                       placeholder="{{ __('Name for the order...') }}"
                                       autocomplete="off">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ __('Phone') }} <small class="text-muted">({{ __('for pickup notification') }})</small></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                <input type="tel"
                                       name="customer_phone"
                                       class="form-control pm-input"
                                       placeholder="{{ __('Phone number...') }}"
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="pm-divider"></div>

                    {{-- Estimated Pickup Time --}}
                    <div class="pm-section-title">
                        <i class="bx bx-time me-2"></i>{{ __('Estimated Pickup Time') }}
                    </div>

                    <div class="pickup-time-selector">
                        <div class="pickup-time-options">
                            <label class="pickup-time-option active">
                                <input type="radio" name="pickup_time" value="10" checked>
                                <div class="pickup-time-box">
                                    <span class="time-value">10</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                            <label class="pickup-time-option">
                                <input type="radio" name="pickup_time" value="15">
                                <div class="pickup-time-box">
                                    <span class="time-value">15</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                            <label class="pickup-time-option">
                                <input type="radio" name="pickup_time" value="20">
                                <div class="pickup-time-box">
                                    <span class="time-value">20</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                            <label class="pickup-time-option">
                                <input type="radio" name="pickup_time" value="30">
                                <div class="pickup-time-box">
                                    <span class="time-value">30</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                            <label class="pickup-time-option">
                                <input type="radio" name="pickup_time" value="45">
                                <div class="pickup-time-box">
                                    <span class="time-value">45</span>
                                    <span class="time-unit">{{ __('min') }}</span>
                                </div>
                            </label>
                        </div>
                        <div class="estimated-pickup-display mt-3">
                            <i class="bx bx-check-circle text-success me-2"></i>
                            {{ __('Ready by') }}: <strong id="estimatedPickupTime">--:--</strong>
                        </div>
                    </div>

                    <div class="pm-divider"></div>

                    {{-- Special Instructions --}}
                    <div class="pm-section-title">
                        <i class="bx bx-note me-2"></i>{{ __('Special Instructions') }}
                        <span class="text-muted fw-normal ms-2">({{ __('Optional') }})</span>
                    </div>

                    <textarea name="special_instructions"
                              class="form-control pm-textarea"
                              rows="2"
                              placeholder="{{ __('Any special requests or packaging instructions...') }}"></textarea>

                    <div class="pm-divider-dashed"></div>

                    {{-- Order Total Display --}}
                    @include('pos::components.total-display', [
                        'total' => 0,
                        'label' => __('Order Total'),
                        'variant' => 'success',
                        'size' => 'medium',
                        'id' => 'takeawayTotalDisplay'
                    ])
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-complete-payment w-100" onclick="proceedToPayment('take_away')">
                    <i class="bx bx-credit-card me-2"></i>
                    {{ __('Proceed to Payment') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.pickup-time-options {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.pickup-time-option {
    cursor: pointer;
    margin: 0;
    flex: 1;
    min-width: 70px;
}

.pickup-time-option input {
    display: none;
}

.pickup-time-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 8px;
    border: 2px solid var(--pm-border);
    border-radius: 10px;
    background: white;
    transition: all 0.2s ease;
}

.pickup-time-box:hover {
    border-color: var(--pm-success);
    transform: translateY(-2px);
}

.pickup-time-option.active .pickup-time-box,
.pickup-time-option input:checked + .pickup-time-box {
    border-color: var(--pm-success);
    background: var(--pm-success);
    color: white;
}

.pickup-time-box .time-value {
    font-size: 20px;
    font-weight: 700;
    line-height: 1;
}

.pickup-time-box .time-unit {
    font-size: 11px;
    text-transform: uppercase;
    opacity: 0.8;
}

.estimated-pickup-display {
    background: var(--pm-success-light);
    padding: 12px 16px;
    border-radius: 8px;
    color: var(--pm-dark);
    font-size: 14px;
}

.estimated-pickup-display strong {
    color: var(--pm-success);
    font-size: 16px;
}

@media (max-width: 576px) {
    .pickup-time-option {
        min-width: calc(33.33% - 10px);
    }
}
</style>

<script>
// Initialize Takeaway Setup Modal
function initTakeawaySetupModal(total, itemCount) {
    // Update total display
    const totalDisplay = document.querySelector('#takeawayTotalDisplay .total-amount');
    if (totalDisplay) {
        totalDisplay.textContent = currencyIcon + ' ' + parseFloat(total).toFixed(2);
    }

    // Reset form
    const form = document.getElementById('takeawaySetupForm');
    if (form) {
        form.reset();
    }

    // Reset pickup time selection
    document.querySelectorAll('.pickup-time-option').forEach(opt => {
        opt.classList.remove('active');
    });
    const defaultPickup = document.querySelector('.pickup-time-option input[value="10"]');
    if (defaultPickup) {
        defaultPickup.checked = true;
        defaultPickup.closest('.pickup-time-option').classList.add('active');
    }

    // Update estimated pickup time
    updateEstimatedPickupTime(10);

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('takeawaySetupModal'));
    modal.show();
}

// Update estimated pickup time display
function updateEstimatedPickupTime(minutes) {
    const now = new Date();
    now.setMinutes(now.getMinutes() + parseInt(minutes));

    const hours = now.getHours();
    const mins = now.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    const displayMins = mins.toString().padStart(2, '0');

    const timeString = displayHours + ':' + displayMins + ' ' + ampm;
    document.getElementById('estimatedPickupTime').textContent = timeString;
}

// Handle pickup time selection
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.pickup-time-option input').forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Update active state
            document.querySelectorAll('.pickup-time-option').forEach(opt => opt.classList.remove('active'));
            this.closest('.pickup-time-option').classList.add('active');

            // Update estimated time
            updateEstimatedPickupTime(this.value);
        });
    });
});

// Get takeaway form data
function getTakeawayFormData() {
    return {
        customer_name: document.querySelector('#takeawaySetupForm input[name="customer_name"]')?.value || '',
        customer_phone: document.querySelector('#takeawaySetupForm input[name="customer_phone"]')?.value || '',
        pickup_time: document.querySelector('#takeawaySetupForm input[name="pickup_time"]:checked')?.value || '15',
        special_instructions: document.querySelector('#takeawaySetupForm textarea[name="special_instructions"]')?.value || ''
    };
}
</script>
