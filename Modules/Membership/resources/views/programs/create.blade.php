@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Create Loyalty Program') }}</title>
@endsection
@section('content')
    <div class="card mb-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Create Loyalty Program') }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('membership.programs.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">{{ __('Program Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="earning_type">{{ __('Earning Type') }} <span class="text-danger">*</span></label>
                            <select name="earning_type" id="earning_type" class="form-control" required>
                                <option value="per_transaction" {{ old('earning_type') == 'per_transaction' ? 'selected' : '' }}>
                                    {{ __('Per Transaction') }}
                                </option>
                                <option value="per_amount" {{ old('earning_type') == 'per_amount' ? 'selected' : '' }}>
                                    {{ __('Per Amount') }}
                                </option>
                            </select>
                            @error('earning_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="earning_rate">{{ __('Earning Rate') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="earning_rate" id="earning_rate" class="form-control" value="{{ old('earning_rate') }}" required>
                            @error('earning_rate')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="min_transaction_amount">{{ __('Minimum Transaction Amount') }}</label>
                            <input type="number" step="0.01" name="min_transaction_amount" id="min_transaction_amount" class="form-control" value="{{ old('min_transaction_amount', 0) }}">
                            @error('min_transaction_amount')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="redemption_type">{{ __('Redemption Type') }} <span class="text-danger">*</span></label>
                            <select name="redemption_type" id="redemption_type" class="form-control" required>
                                <option value="discount" {{ old('redemption_type') == 'discount' ? 'selected' : '' }}>
                                    {{ __('Discount') }}
                                </option>
                                <option value="free_item" {{ old('redemption_type') == 'free_item' ? 'selected' : '' }}>
                                    {{ __('Free Item') }}
                                </option>
                                <option value="cashback" {{ old('redemption_type') == 'cashback' ? 'selected' : '' }}>
                                    {{ __('Cashback') }}
                                </option>
                            </select>
                            @error('redemption_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="points_per_unit">{{ __('Points Per Unit') }} <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="points_per_unit" id="points_per_unit" class="form-control" value="{{ old('points_per_unit') }}" required>
                            @error('points_per_unit')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="switch switch-square">
                                <input type="checkbox" name="is_active" class="switch-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
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
                    <button type="submit" class="btn btn-primary">{{ __('Create Program') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
