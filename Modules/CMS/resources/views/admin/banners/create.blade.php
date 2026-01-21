@extends('admin.layouts.master')

@section('title')
    {{ __('Add Banner') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Add Banner') }}</h4>
            <a href="{{ route('admin.cms.banners.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cms.banners.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Position') }} <span class="text-danger">*</span></label>
                                <select name="position" class="form-select @error('position') is-invalid @enderror" required>
                                    <option value="">Select Position</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos }}" {{ old('position') == $pos ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $pos)) }}</option>
                                    @endforeach
                                </select>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Title') }}</label>
                                        <input type="text" name="title" value="{{ old('title') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Subtitle') }}</label>
                                        <input type="text" name="subtitle" value="{{ old('subtitle') }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Button Text') }}</label>
                                        <input type="text" name="button_text" value="{{ old('button_text') }}" class="form-control" placeholder="e.g., Shop Now">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Button Link') }}</label>
                                        <input type="text" name="button_link" value="{{ old('button_link') }}" class="form-control" placeholder="e.g., /menu">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Badge Text') }}</label>
                                <input type="text" name="badge_text" value="{{ old('badge_text') }}" class="form-control" placeholder="e.g., 20% OFF">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Start Date') }}</label>
                                        <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control">
                                        <small class="text-muted">Leave empty for immediate start</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('End Date') }}</label>
                                        <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control">
                                        <small class="text-muted">Leave empty for no expiration</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Banner Image') }} <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Background Image') }}</label>
                                <input type="file" name="background_image" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Sort Order') }}</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="form-control" min="0">
                            </div>

                            <div class="mb-3">
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
                            <i class="bx bx-save"></i> {{ __('Create Banner') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
