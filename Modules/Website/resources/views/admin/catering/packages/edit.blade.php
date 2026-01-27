@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Edit Catering Package') }}</title>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">{{ __('Edit Catering Package') }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.restaurant.catering.packages.index') }}">{{ __('Packages') }}</a></li>
                    <li class="breadcrumb-item active">{{ $package->name }}</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.restaurant.catering.packages.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>{{ __('Back') }}
        </a>
    </div>

    <form action="{{ route('admin.restaurant.catering.packages.update', $package) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Basic Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Package Name') }} *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $package->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Slug') }}</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $package->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Short Description') }}</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" maxlength="500">{{ old('description', $package->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Long Description') }}</label>
                            <textarea name="long_description" class="form-control @error('long_description') is-invalid @enderror" rows="5">{{ old('long_description', $package->long_description) }}</textarea>
                            @error('long_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing & Capacity -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Pricing & Capacity') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Price Per Person') }} *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price_per_person" class="form-control @error('price_per_person') is-invalid @enderror" value="{{ old('price_per_person', $package->price_per_person) }}" step="0.01" min="0" required>
                                </div>
                                @error('price_per_person')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Minimum Guests') }} *</label>
                                <input type="number" name="min_guests" class="form-control @error('min_guests') is-invalid @enderror" value="{{ old('min_guests', $package->min_guests) }}" min="1" required>
                                @error('min_guests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">{{ __('Maximum Guests') }} *</label>
                                <input type="number" name="max_guests" class="form-control @error('max_guests') is-invalid @enderror" value="{{ old('max_guests', $package->max_guests) }}" min="1" required>
                                @error('max_guests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- What's Included -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __("What's Included") }}</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addIncludeBtn">
                            <i class="bx bx-plus me-1"></i>{{ __('Add Item') }}
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="includesContainer">
                            @php
                                $includes = old('includes', $package->includes ?? []);
                                if (empty($includes)) $includes = [''];
                            @endphp
                            @foreach($includes as $index => $include)
                                <div class="input-group mb-2 include-item">
                                    <input type="text" name="includes[]" class="form-control" value="{{ $include }}" placeholder="{{ __('e.g., Appetizers, Main Course, Dessert') }}">
                                    <button type="button" class="btn btn-outline-danger remove-include"><i class="bx bx-trash"></i></button>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted">{{ __('List what is included in this package') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Package Image') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($package->image)
                            <div class="mb-3 text-center">
                                <img src="{{ $package->image_url }}" alt="{{ $package->name }}" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        @endif
                        <div class="mb-3">
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="imageInput">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">{{ __('Leave empty to keep current image') }}</small>
                        </div>
                        <div id="imagePreview" class="text-center" style="display: none;">
                            <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Display Order') }}</label>
                            <input type="number" name="display_order" class="form-control" value="{{ old('display_order', $package->display_order) }}">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', $package->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">{{ __('Active') }}</label>
                        </div>

                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_featured" class="form-check-input" id="isFeatured" value="1" {{ old('is_featured', $package->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isFeatured">{{ __('Featured Package') }}</label>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Statistics') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('Total Inquiries') }}</span>
                            <span class="fw-semibold">{{ $package->inquiries()->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('Price Range') }}</span>
                            <span class="fw-semibold">{{ $package->price_range['formatted'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('Created') }}</span>
                            <span class="fw-semibold">{{ $package->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-save me-1"></i>{{ __('Update Package') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.querySelector('img').src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });

    // Add include item
    document.getElementById('addIncludeBtn').addEventListener('click', function() {
        const container = document.getElementById('includesContainer');
        const div = document.createElement('div');
        div.className = 'input-group mb-2 include-item';
        div.innerHTML = `
            <input type="text" name="includes[]" class="form-control" placeholder="{{ __('e.g., Appetizers, Main Course, Dessert') }}">
            <button type="button" class="btn btn-outline-danger remove-include"><i class="bx bx-trash"></i></button>
        `;
        container.appendChild(div);
    });

    // Remove include item
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-include')) {
            const items = document.querySelectorAll('.include-item');
            if (items.length > 1) {
                e.target.closest('.include-item').remove();
            }
        }
    });
});
</script>
@endpush
