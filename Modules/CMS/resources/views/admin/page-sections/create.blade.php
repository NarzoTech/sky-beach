@extends('admin.layouts.master')

@section('title')
    {{ __('Add Page Section') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Add Page Section') }}</h4>
            <a href="{{ route('admin.cms.page-sections.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cms.page-sections.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Page') }} <span class="text-danger">*</span></label>
                                <select name="page" class="form-select @error('page') is-invalid @enderror" required>
                                    <option value="">Select Page</option>
                                    <option value="home" {{ old('page') == 'home' ? 'selected' : '' }}>Home</option>
                                    <option value="about" {{ old('page') == 'about' ? 'selected' : '' }}>About</option>
                                    <option value="menu" {{ old('page') == 'menu' ? 'selected' : '' }}>Menu</option>
                                    <option value="contact" {{ old('page') == 'contact' ? 'selected' : '' }}>Contact</option>
                                    <option value="services" {{ old('page') == 'services' ? 'selected' : '' }}>Services</option>
                                    <option value="reservation" {{ old('page') == 'reservation' ? 'selected' : '' }}>Reservation</option>
                                </select>
                                @error('page')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Section Key') }} <span class="text-danger">*</span></label>
                                <input type="text" name="section_key" value="{{ old('section_key') }}" class="form-control @error('section_key') is-invalid @enderror" required placeholder="e.g., hero, about_intro, features">
                                <small class="text-muted">Unique identifier for this section (lowercase, underscores)</small>
                                @error('section_key')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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

                    <div class="mb-3">
                        <label class="form-label">{{ __('Content') }}</label>
                        <textarea name="content" class="form-control" rows="5">{{ old('content') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Image') }}</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Background Image') }}</label>
                                <input type="file" name="background_image" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Button Text') }}</label>
                                <input type="text" name="button_text" value="{{ old('button_text') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Button Link') }}</label>
                                <input type="text" name="button_link" value="{{ old('button_link') }}" class="form-control" placeholder="e.g., /menu or https://...">
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
                            <i class="bx bx-save"></i> {{ __('Create Section') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
