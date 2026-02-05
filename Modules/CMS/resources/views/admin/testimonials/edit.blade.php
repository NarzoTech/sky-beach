@extends('admin.layouts.master')

@section('title')
    {{ __('Edit Testimonial') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Edit Testimonial') }}</h4>
            <a href="{{ route('admin.cms.testimonials.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.cms.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $testimonial->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Position') }}</label>
                                        <input type="text" name="position" value="{{ old('position', $testimonial->position) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Company') }}</label>
                                        <input type="text" name="company" value="{{ old('company', $testimonial->company) }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Content') }} <span class="text-danger">*</span></label>
                                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="4" required>{{ old('content', $testimonial->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Rating') }} <span class="text-danger">*</span></label>
                                <select name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                    @for($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Photo') }}</label>
                                <div id="image-preview" class="mb-2">
                                    @if($testimonial->image)
                                        <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->name }}" class="img-thumbnail" style="max-height: 150px;">
                                    @endif
                                </div>
                                @if($testimonial->image)
                                    <div class="mb-2">
                                        <label class="form-check-label text-danger" style="cursor: pointer;">
                                            <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="remove_image">
                                            {{ __('Remove image') }}
                                        </label>
                                    </div>
                                @endif
                                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">{{ __('Recommended: 100x100px, Max 2MB. Leave empty to keep current image.') }}</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Sort Order') }}</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" class="form-control" min="0">
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $testimonial->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_featured" value="0">
                                    <input type="checkbox" name="is_featured" value="1" class="form-check-input" id="is_featured" {{ old('is_featured', $testimonial->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">{{ __('Featured') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> {{ __('Update Testimonial') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $('#image').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 150px;">');
                $('#remove_image').prop('checked', false);
            }
            reader.readAsDataURL(file);
        }
    });

    $('#remove_image').on('change', function() {
        if ($(this).is(':checked')) {
            $('#image').val('');
            $('#image-preview').html('');
        }
    });
</script>
@endpush
