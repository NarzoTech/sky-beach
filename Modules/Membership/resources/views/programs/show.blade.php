@extends('admin.layouts.master')
@section('title', __('Program Details') . ' - ' . $program->name)
@section('content')
    @php
        $earningRules = $program->earning_rules ?? [];
        $redemptionRules = $program->redemption_rules ?? [];
        $couponTiers = $redemptionRules['coupon_tiers'] ?? [];
    @endphp

    <div class="card mb-4">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ $program->name }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('membership.programs.edit', $program) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> {{ __('Edit') }}
                </a>
                <a href="{{ route('membership.programs.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <td>{{ $program->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <td>{{ $program->description ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if ($program->is_active)
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Created By') }}</th>
                            <td>{{ $program->createdBy->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Created At') }}</th>
                            <td>{{ $program->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    {{-- Earning Rule Card --}}
                    <div class="card border mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bx bx-coin-stack"></i> {{ __('Points Earning Rule') }}</h6>
                        </div>
                        <div class="card-body text-center">
                            @if(isset($earningRules['spend_amount']))
                                <h4 class="text-primary mb-0">
                                    {{ $earningRules['spend_amount'] }} {{ currency_icon() }}
                                    <i class="bx bx-right-arrow-alt"></i>
                                    {{ $earningRules['points_earned'] }} {{ __('Point(s)') }}
                                </h4>
                                <small class="text-muted">{{ __('For every :amount spent, customer earns :points point(s)', ['amount' => $earningRules['spend_amount'] . ' ' . currency_icon(), 'points' => $earningRules['points_earned']]) }}</small>
                            @else
                                <span class="text-muted">{{ __('Not configured') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Coupon Tiers --}}
    <div class="card mb-5">
        <div class="card-header">
            <h5 class="section_title mb-0"><i class="bx bx-gift"></i> {{ __('Discount Coupon Tiers') }}</h5>
        </div>
        <div class="card-body">
            @if(count($couponTiers) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:10%">{{ __('Tier') }}</th>
                                <th class="text-center" style="width:35%">{{ __('Points Required') }}</th>
                                <th class="text-center" style="width:10%"></th>
                                <th class="text-center" style="width:35%">{{ __('Discount Coupon') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($couponTiers as $i => $tier)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary fs-6">{{ $tier['points_required'] }} {{ __('Points') }}</span>
                                    </td>
                                    <td class="text-center"><i class="bx bx-right-arrow-alt fs-4"></i></td>
                                    <td class="text-center">
                                        <span class="badge bg-success fs-6">{{ $tier['discount_amount'] }} {{ currency_icon() }} {{ __('Discount') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted">{{ __('No coupon tiers configured') }}</p>
            @endif
        </div>
    </div>
@endsection
