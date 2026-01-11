@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Loyalty Program') }}</title>
@endsection
@section('content')
    <div class="card mb-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Edit Loyalty Program') }}: {{ $program->name }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('membership.programs.update', $program) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('Program Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $program->name) }}" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="earning_type">{{ __('Earning Type') }} <span class="text-danger">*</span></label>
                            <select name="earning_type" id="earning_type" class="form-control" required>
                                <option value="per_transaction" {{ old('earning_type', $program->earning_type) == 'per_transaction' ? 'selected' : '' }}>
                                    {{ __('Per Transaction') }}
                                </option>
                                <option value="per_amount" {{ old('earning_type', $program->earning_type) == 'per_amount' ? 'selected' : '' }}>
                                    {{ __('Per Amount') }}
                                </option>
                            </select>
                            <small class="text-muted">{{ __('Per Transaction: Fixed points per sale. Per Amount: Points based on spend amount.') }}</small>
                            @error('earning_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $program->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="earning_rate">{{ __('Earning Rate') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="earning_rate" id="earning_rate" class="form-control" value="{{ old('earning_rate', $program->earning_rate) }}" required>
                            <small class="text-muted">{{ __('Points earned per transaction or per unit amount (e.g., 1 point per 100 TK)') }}</small>
                            @error('earning_rate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="min_transaction_amount">{{ __('Minimum Transaction Amount') }}</label>
                            <input type="number" step="0.01" name="min_transaction_amount" id="min_transaction_amount" class="form-control" value="{{ old('min_transaction_amount', $program->min_transaction_amount) }}">
                            <small class="text-muted">{{ __('Minimum purchase amount required to earn points (0 = no minimum)') }}</small>
                            @error('min_transaction_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="redemption_type">{{ __('Redemption Type') }} <span class="text-danger">*</span></label>
                            <select name="redemption_type" id="redemption_type" class="form-control" required>
                                <option value="discount" {{ old('redemption_type', $program->redemption_type) == 'discount' ? 'selected' : '' }}>
                                    {{ __('Discount') }}
                                </option>
                                <option value="free_item" {{ old('redemption_type', $program->redemption_type) == 'free_item' ? 'selected' : '' }}>
                                    {{ __('Free Item') }}
                                </option>
                                <option value="cashback" {{ old('redemption_type', $program->redemption_type) == 'cashback' ? 'selected' : '' }}>
                                    {{ __('Cashback') }}
                                </option>
                            </select>
                            <small class="text-muted">{{ __('How customers can use their points') }}</small>
                            @error('redemption_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="points_per_unit">{{ __('Points Per Unit') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="points_per_unit" id="points_per_unit" class="form-control" value="{{ old('points_per_unit', $program->points_per_unit) }}" required>
                            <small class="text-muted">{{ __('Points needed for 1 TK redemption value (e.g., 10 = 10 points per 1 TK)') }}</small>
                            @error('points_per_unit')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="switch switch-square">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" class="switch-input" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"><i class="bx bx-check"></i></span>
                                    <span class="switch-off"><i class="bx bx-x"></i></span>
                                </span>
                                <span class="switch-label">{{ __('Active') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('membership.programs.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Update Program') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        function toggleMinTransactionAmount() {
            var earningType = $('#earning_type').val();
            var minTransactionField = $('#min_transaction_amount').closest('.col-md-6');

            if (earningType === 'per_transaction') {
                minTransactionField.hide();
            } else {
                minTransactionField.show();
            }
        }

        // Initial check
        toggleMinTransactionAmount();

        // On change
        $('#earning_type').on('change', function() {
            toggleMinTransactionAmount();
        });
    });
</script>
@endpush
