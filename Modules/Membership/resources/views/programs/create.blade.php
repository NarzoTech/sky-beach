@extends('admin.layouts.master')
@section('title', __('Create Loyalty Program'))
@section('content')
    <form action="{{ route('membership.programs.store') }}" method="POST">
        @csrf

        {{-- Program Info --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="section_title">{{ __('Program Information') }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name">{{ __('Program Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required placeholder="{{ __('e.g. Sky Beach Rewards') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="description">{{ __('Description') }}</label>
                            <input type="text" name="description" id="description" class="form-control" value="{{ old('description') }}" placeholder="{{ __('Optional description') }}">
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Earning Rule --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="section_title">{{ __('Points Earning Rule') }}</h4>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">{{ __('Set how customers earn points based on their spending.') }}</p>
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="spend_amount">{{ __('For Every (Amount Spent)') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="1" name="spend_amount" id="spend_amount" class="form-control" value="{{ old('spend_amount') }}" required placeholder="100">
                                <span class="input-group-text">{{ currency_icon() }}</span>
                            </div>
                            @error('spend_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-1 text-center mb-3">
                        <span class="fw-bold">=</span>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="points_earned">{{ __('Points Earned') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="1" min="1" name="points_earned" id="points_earned" class="form-control" value="{{ old('points_earned') }}" required placeholder="1">
                                <span class="input-group-text">{{ __('Points') }}</span>
                            </div>
                            @error('points_earned')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="alert alert-light border" id="earning-preview" style="display:none;">
                    <i class="bx bx-info-circle"></i> <span id="earning-preview-text"></span>
                </div>
            </div>
        </div>

        {{-- Discount Coupon Tiers --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="section_title mb-0">{{ __('Discount Coupon Tiers') }}</h4>
                <button type="button" class="btn btn-sm btn-primary" id="add-tier-btn">
                    <i class="bx bx-plus"></i> {{ __('Add Tier') }}
                </button>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">{{ __('Define how many points are needed to get a discount coupon.') }}</p>
                <div class="table-responsive">
                    <table class="table" id="coupon-tiers-table">
                        <thead>
                            <tr>
                                <th style="width:35%">{{ __('Points Required') }}</th>
                                <th style="width:10%" class="text-center">=</th>
                                <th style="width:35%">{{ __('Discount Amount') }}</th>
                                <th style="width:20%" class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody id="coupon-tiers-body">
                            @if(old('tier_points'))
                                @foreach(old('tier_points') as $i => $tp)
                                    <tr class="tier-row">
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="tier_points[]" class="form-control" min="1" value="{{ $tp }}" required placeholder="50">
                                                <span class="input-group-text">{{ __('Points') }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle"><i class="bx bx-right-arrow-alt fs-4"></i></td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" name="tier_discounts[]" class="form-control" min="1" step="0.01" value="{{ old('tier_discounts')[$i] ?? '' }}" required placeholder="500">
                                                <span class="input-group-text">{{ currency_icon() }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-tier-btn"><i class="bx bx-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="tier-row">
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="tier_points[]" class="form-control" min="1" required placeholder="50">
                                            <span class="input-group-text">{{ __('Points') }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle"><i class="bx bx-right-arrow-alt fs-4"></i></td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" name="tier_discounts[]" class="form-control" min="1" step="0.01" required placeholder="500">
                                            <span class="input-group-text">{{ currency_icon() }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-danger remove-tier-btn"><i class="bx bx-trash"></i></button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @error('tier_points')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                @error('tier_discounts')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Status & Actions --}}
        <div class="card mb-5">
            <div class="card-body d-flex justify-content-between align-items-center">
                <label class="switch switch-square mb-0">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" class="switch-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="switch-toggle-slider">
                        <span class="switch-on"><i class="bx bx-check"></i></span>
                        <span class="switch-off"><i class="bx bx-x"></i></span>
                    </span>
                    <span class="switch-label">{{ __('Active') }}</span>
                </label>
                <div>
                    <a href="{{ route('membership.programs.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Create Program') }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add tier row
    $('#add-tier-btn').on('click', function() {
        var row = `<tr class="tier-row">
            <td>
                <div class="input-group">
                    <input type="number" name="tier_points[]" class="form-control" min="1" required placeholder="50">
                    <span class="input-group-text">{{ __('Points') }}</span>
                </div>
            </td>
            <td class="text-center align-middle"><i class="bx bx-right-arrow-alt fs-4"></i></td>
            <td>
                <div class="input-group">
                    <input type="number" name="tier_discounts[]" class="form-control" min="1" step="0.01" required placeholder="500">
                    <span class="input-group-text">{{ currency_icon() }}</span>
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-tier-btn"><i class="bx bx-trash"></i></button>
            </td>
        </tr>`;
        $('#coupon-tiers-body').append(row);
    });

    // Remove tier row
    $(document).on('click', '.remove-tier-btn', function() {
        if ($('.tier-row').length > 1) {
            $(this).closest('tr').remove();
        }
    });

    // Earning preview
    function updateEarningPreview() {
        var spend = $('#spend_amount').val();
        var points = $('#points_earned').val();
        if (spend && points) {
            $('#earning-preview').show();
            $('#earning-preview-text').text("{{ __('Customer spends') }} " + spend + " {{ currency_icon() }} → {{ __('earns') }} " + points + " {{ __('point(s)') }}");
        } else {
            $('#earning-preview').hide();
        }
    }

    $('#spend_amount, #points_earned').on('input', updateEarningPreview);
    updateEarningPreview();
});
</script>
@endpush
