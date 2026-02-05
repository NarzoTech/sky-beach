@extends('admin.layouts.master')

@section('title')
    {{ __('Edit Counter') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-3 page-title-card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="section_title">{{ __('Edit Counter') }}</h4>
                <a href="{{ route('admin.cms.counters.index') }}" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cms.counters.update', $counter) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Label') }} <span class="text-danger">*</span></label>
                                <input type="text" name="label" value="{{ old('label', $counter->label) }}" class="form-control @error('label') is-invalid @enderror" required>
                                @error('label')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Value') }} <span class="text-danger">*</span></label>
                                <input type="number" name="value" value="{{ old('value', $counter->value) }}" class="form-control @error('value') is-invalid @enderror" required>
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
                                <input type="text" name="icon" value="{{ old('icon', $counter->icon) }}" class="form-control" placeholder="e.g., bx bx-star">
                                <small class="text-muted">Use BoxIcons (bx) or FontAwesome (fas/far) class names</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Suffix') }}</label>
                                <input type="text" name="suffix" value="{{ old('suffix', $counter->suffix) }}" class="form-control" placeholder="e.g., +, K, %">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Sort Order') }}</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $counter->sort_order) }}" class="form-control" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 pt-4">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $counter->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ __('Update Counter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
