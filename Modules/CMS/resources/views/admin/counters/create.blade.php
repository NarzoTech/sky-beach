@extends('admin.layouts.master')

@section('title')
    {{ __('Add Counter') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Add Counter') }}</h4>
            <a href="{{ route('admin.cms.counters.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cms.counters.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Label') }} <span class="text-danger">*</span></label>
                                <input type="text" name="label" value="{{ old('label') }}" class="form-control @error('label') is-invalid @enderror" required placeholder="e.g., Happy Customers">
                                @error('label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Value') }} <span class="text-danger">*</span></label>
                                <input type="number" name="value" value="{{ old('value') }}" class="form-control @error('value') is-invalid @enderror" required placeholder="e.g., 5000">
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Icon Class') }}</label>
                                <input type="text" name="icon" value="{{ old('icon', 'bx bx-star') }}" class="form-control" placeholder="e.g., bx bx-star, fas fa-users">
                                <small class="text-muted">Use BoxIcons (bx) or FontAwesome (fas/far) class names</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Suffix') }}</label>
                                <input type="text" name="suffix" value="{{ old('suffix') }}" class="form-control" placeholder="e.g., +, K, %">
                                <small class="text-muted">Text to appear after the number (e.g., + for 5000+)</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Sort Order') }}</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 pt-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ __('Create Counter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
