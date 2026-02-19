@extends('website::layouts.master')

@section('title', __('Rewards') . ' - ' . config('app.name'))

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ __('Rewards') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a href="#">{{ __('Rewards') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========REWARDS AREA START===========-->
        <section class="rewards_area pt_110 xs_pt_90 pb_120 xs_pb_100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <!-- Phone Lookup -->
                        <div class="rewards_card text-center wow fadeInUp">
                            <div class="rewards_icon">
                                <i class="fas fa-gift"></i>
                            </div>
                            <h3>{{ __('Redeem Your Loyalty Points') }}</h3>
                            <p class="mb-4 text-muted">{{ __('Enter your phone number to check your points balance and redeem for discount coupons.') }}</p>

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="input-group mb-3">
                                        <input type="text" id="rewards-phone" class="form-control form-control-lg"
                                               placeholder="{{ __('Enter your phone number') }}"
                                               @auth value="{{ auth()->user()->phone ?? '' }}" @endauth>
                                        <button class="btn btn-primary btn-lg" onclick="checkRewardsPoints()" id="check-points-btn">
                                            <i class="fas fa-search me-1"></i> {{ __('Check') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Points Info (hidden until phone lookup) -->
                        <div id="rewards-info" style="display: none;" class="mt-4 wow fadeInUp">
                            <div class="points_balance_card text-center">
                                <h5>{{ __('Welcome') }}, <span id="rewards-customer-name"></span>!</h5>
                                <div class="points_display">
                                    <span class="points_number" id="rewards-points-balance">0</span>
                                    <span class="points_label">{{ __('Available Points') }}</span>
                                </div>
                            </div>

                            <!-- Coupon Tiers -->
                            <div class="tiers_section mt-4">
                                <h5 class="text-center mb-3">{{ __('Available Rewards') }}</h5>
                                <div class="row" id="rewards-tiers"></div>
                            </div>
                        </div>

                        <!-- Redeemed Coupon Result (hidden until redemption) -->
                        <div id="rewards-result" style="display: none;" class="mt-4 wow fadeInUp">
                            <div class="coupon_result_card text-center">
                                <div class="result_icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h4>{{ __('Coupon Generated!') }}</h4>
                                <p class="text-muted">{{ __('Use this code at checkout to get your discount.') }}</p>
                                <div class="coupon_code_display">
                                    <span id="generated-coupon-code"></span>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyCouponCode()">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <p class="mt-2">
                                    <strong id="generated-discount-amount"></strong> {{ __('discount') }}
                                </p>
                                <p class="text-muted small">{{ __('Valid for 24 hours. One-time use only.') }}</p>
                                <p class="mt-2">{{ __('Remaining Points') }}: <strong id="remaining-points-after"></strong></p>
                                <div class="mt-4">
                                    <a href="{{ route('website.checkout.index') }}" class="common_btn me-2">
                                        <i class="fas fa-shopping-cart me-1"></i> {{ __('Go to Checkout') }}
                                    </a>
                                    <button class="common_btn btn_outline" onclick="resetRewards()">
                                        {{ __('Redeem More') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========REWARDS AREA END===========-->
@endsection

@push('styles')
<style>
    .rewards_card {
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.1);
    }

    .rewards_icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--colorPrimary, #AB162C), var(--colorYellow, #F2A22A));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .rewards_icon i {
        font-size: 36px;
        color: #fff;
    }

    .points_balance_card {
        background: linear-gradient(135deg, #FFF8E1 0%, #FFECB3 100%);
        border: 2px solid var(--colorYellow, #F2A22A);
        border-radius: 15px;
        padding: 30px;
    }

    .points_display {
        margin-top: 15px;
    }

    .points_number {
        display: block;
        font-size: 48px;
        font-weight: 700;
        color: var(--colorYellow, #F2A22A);
        line-height: 1;
    }

    .points_label {
        display: block;
        font-size: 14px;
        color: #666;
        margin-top: 5px;
    }

    .tier_card {
        background: #fff;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }

    .tier_card:hover {
        border-color: var(--colorPrimary, #AB162C);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .tier_card.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    .tier_points {
        font-size: 24px;
        font-weight: 700;
        color: var(--colorYellow, #F2A22A);
    }

    .tier_discount {
        font-size: 20px;
        font-weight: 600;
        color: var(--colorPrimary, #AB162C);
        margin: 10px 0;
    }

    .coupon_result_card {
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.1);
    }

    .result_icon {
        width: 80px;
        height: 80px;
        background: var(--colorGreen, #0F9043);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .result_icon i {
        font-size: 40px;
        color: #fff;
    }

    .coupon_code_display {
        background: #f8f9fa;
        border: 2px dashed var(--colorPrimary, #AB162C);
        border-radius: 10px;
        padding: 15px 25px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin: 15px 0;
    }

    .coupon_code_display span {
        font-size: 24px;
        font-weight: 700;
        letter-spacing: 2px;
        color: var(--colorPrimary, #AB162C);
    }

    .common_btn.btn_outline {
        background: transparent;
        border: 2px solid var(--colorPrimary, #AB162C);
        color: var(--colorPrimary, #AB162C);
    }

    .common_btn.btn_outline:hover {
        background: var(--colorPrimary, #AB162C);
        color: #fff;
    }

    @media (max-width: 768px) {
        .rewards_card, .coupon_result_card {
            padding: 20px;
        }

        .points_number {
            font-size: 36px;
        }

        .coupon_code_display span {
            font-size: 18px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Simple toast notification
    function showToast(message, type) {
        const existing = document.querySelector('.rewards_toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = 'rewards_toast';
        const bg = type === 'success' ? 'var(--colorGreen, #0F9043)' : 'var(--colorPrimary, #AB162C)';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;padding:12px 20px;border-radius:8px;color:#fff;font-size:14px;box-shadow:0 4px 12px rgba(0,0,0,0.15);display:flex;align-items:center;gap:8px;animation:slideIn 0.3s ease;background:' + bg;
        toast.innerHTML = '<i class="fas ' + icon + '"></i> ' + message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.3s'; setTimeout(() => toast.remove(), 300); }, 3000);
    }

    function checkRewardsPoints() {
        const phone = document.getElementById('rewards-phone').value.trim();
        if (!phone) {
            showToast('{{ __("Please enter your phone number.") }}', 'error');
            return;
        }

        const btn = document.getElementById('check-points-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch('{{ route("website.rewards.check") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone: phone })
        })
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search me-1"></i> {{ __("Check") }}';

            if (data.success) {
                document.getElementById('rewards-customer-name').textContent = data.customer.name;
                document.getElementById('rewards-points-balance').textContent = Math.floor(data.customer.total_points);

                // Build tier cards
                const tiersContainer = document.getElementById('rewards-tiers');
                tiersContainer.innerHTML = '';

                data.tiers.forEach((tier, index) => {
                    const canRedeem = tier.can_redeem;
                    tiersContainer.innerHTML += `
                        <div class="col-md-4 mb-3">
                            <div class="tier_card ${canRedeem ? '' : 'disabled'}">
                                <div class="tier_points">${tier.points_required} pts</div>
                                <div class="tier_discount">${tier.discount_amount} {{ currency_icon() }} {{ __('off') }}</div>
                                <button class="common_btn btn-sm w-100 mt-2" ${canRedeem ? '' : 'disabled'}
                                        onclick="redeemTier(${index})">
                                    ${canRedeem ? '{{ __("Redeem") }}' : '{{ __("Not enough points") }}'}
                                </button>
                            </div>
                        </div>
                    `;
                });

                document.getElementById('rewards-info').style.display = 'block';
                document.getElementById('rewards-result').style.display = 'none';
            } else {
                showToast(data.message || '{{ __("No loyalty account found.") }}', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search me-1"></i> {{ __("Check") }}';
            showToast('{{ __("Something went wrong. Please try again.") }}', 'error');
        });
    }

    function redeemTier(tierIndex) {
        const phone = document.getElementById('rewards-phone').value.trim();

        if (!confirm('{{ __("Are you sure you want to redeem these points?") }}')) {
            return;
        }

        fetch('{{ route("website.rewards.redeem") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone: phone, tier_index: tierIndex })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('generated-coupon-code').textContent = data.coupon_code;
                document.getElementById('generated-discount-amount').textContent = data.discount_amount + ' {{ currency_icon() }}';
                document.getElementById('remaining-points-after').textContent = Math.floor(data.remaining_points);

                document.getElementById('rewards-info').style.display = 'none';
                document.getElementById('rewards-result').style.display = 'block';
            } else {
                showToast(data.message || '{{ __("Redemption failed.") }}', 'error');
            }
        })
        .catch(err => {
            showToast('{{ __("Something went wrong. Please try again.") }}', 'error');
        });
    }

    function copyCouponCode() {
        const code = document.getElementById('generated-coupon-code').textContent;
        navigator.clipboard.writeText(code).then(() => {
            showToast('{{ __("Coupon code copied!") }}', 'success');
        });
    }

    function resetRewards() {
        document.getElementById('rewards-result').style.display = 'none';
        checkRewardsPoints();
    }

    // Enter key support
    document.getElementById('rewards-phone').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            checkRewardsPoints();
        }
    });
</script>
@endpush
