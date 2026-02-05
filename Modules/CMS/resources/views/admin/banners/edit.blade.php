@extends('admin.layouts.master')

@section('title')
    {{ __('Edit Banner') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-3 page-title-card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="section_title">{{ __('Edit Banner') }}</h4>
                <a href="{{ route('admin.cms.banners.index') }}" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cms.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Position') }} <span class="text-danger">*</span></label>
                                <select name="position" class="form-select @error('position') is-invalid @enderror" required>
                                    <option value="">Select Position</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos }}" {{ old('position', $banner->position) == $pos ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $pos)) }}</option>
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
                                        <input type="text" name="title" value="{{ old('title', $banner->title) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Subtitle') }}</label>
                                        <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Button Text') }}</label>
                                        <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Button Link') }}</label>
                                        <input type="text" name="button_link" value="{{ old('button_link', $banner->button_link) }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Badge Text') }}</label>
                                <input type="text" name="badge_text" value="{{ old('badge_text', $banner->badge_text) }}" class="form-control">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Start Date') }}</label>
                                        <input type="date" name="start_date" value="{{ old('start_date', $banner->start_date?->format('Y-m-d')) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('End Date') }}</label>
                                        <input type="date" name="end_date" value="{{ old('end_date', $banner->end_date?->format('Y-m-d')) }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Banner Image') }}</label>
                                @if($banner->image)
                                    <div class="mb-2">
                                        <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Background Image') }}</label>
                                @if($banner->background_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($banner->background_image) }}" alt="Background" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                <input type="file" name="background_image" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Sort Order') }}</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}" class="form-control" min="0">
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ __('Update Banner') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
