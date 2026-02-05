@extends('admin.layouts.master')

@section('title')
    {{ __('Add Gallery Image') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-3 page-title-card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="section_title">{{ __('Add Gallery Image') }}</h4>
                <a href="{{ route('admin.cms.gallery.index') }}" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cms.gallery.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Title') }}</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Category') }}</label>
                                        <input type="text" name="category" value="{{ old('category') }}" class="form-control" placeholder="e.g., food, ambiance, events">
                                        <small class="text-muted">Used to group images</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Page') }}</label>
                                        <select name="page" class="form-select">
                                            <option value="">-- No specific page --</option>
                                            <option value="home" {{ old('page') == 'home' ? 'selected' : '' }}>Home</option>
                                            <option value="about" {{ old('page') == 'about' ? 'selected' : '' }}>About</option>
                                            <option value="menu" {{ old('page') == 'menu' ? 'selected' : '' }}>Menu</option>
                                            <option value="gallery" {{ old('page') == 'gallery' ? 'selected' : '' }}>Gallery</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Alt Text') }}</label>
                                <input type="text" name="alt_text" value="{{ old('alt_text') }}" class="form-control" placeholder="Image description for accessibility">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Image') }} <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" required>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                            <i class="bx bx-save"></i> {{ __('Upload Image') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
