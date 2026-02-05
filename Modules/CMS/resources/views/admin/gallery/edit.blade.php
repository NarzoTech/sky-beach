@extends('admin.layouts.master')

@section('title')
    {{ __('Edit Gallery Image') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-3 page-title-card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="section_title">{{ __('Edit Gallery Image') }}</h4>
                <a href="{{ route('admin.cms.gallery.index') }}" class="btn btn-primary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cms.gallery.update', $image) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Title') }}</label>
                                <input type="text" name="title" value="{{ old('title', $image->title) }}" class="form-control">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Category') }}</label>
                                        <input type="text" name="category" value="{{ old('category', $image->category) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Page') }}</label>
                                        <select name="page" class="form-select">
                                            <option value="">-- No specific page --</option>
                                            <option value="home" {{ old('page', $image->page) == 'home' ? 'selected' : '' }}>Home</option>
                                            <option value="about" {{ old('page', $image->page) == 'about' ? 'selected' : '' }}>About</option>
                                            <option value="menu" {{ old('page', $image->page) == 'menu' ? 'selected' : '' }}>Menu</option>
                                            <option value="gallery" {{ old('page', $image->page) == 'gallery' ? 'selected' : '' }}>Gallery</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Alt Text') }}</label>
                                <input type="text" name="alt_text" value="{{ old('alt_text', $image->alt_text) }}" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Current Image') }}</label>
                                @if($image->image)
                                    <div class="mb-2">
                                        <img src="{{ $image->image_url }}" alt="{{ $image->alt }}" class="img-thumbnail" style="max-height: 150px;">
                                    </div>
                                @endif
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Sort Order') }}</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $image->sort_order) }}" class="form-control" min="0">
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $image->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ __('Update Image') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
