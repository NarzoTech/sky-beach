@extends('admin.layouts.master')

@section('title')
    {{ __('Edit Menu Item') }}
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">{{ __('Edit Menu Item') }}</h4>
            <a href="{{ route('admin.restaurant.menu-items.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> {{ __('Back') }}
            </a>
        </div>

        <form action="{{ route('admin.restaurant.menu-items.update', $menuItem) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Basic Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $menuItem->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="slug">{{ __('Slug') }}</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                       id="slug" name="slug" value="{{ old('slug', $menuItem->slug) }}">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">{{ __('Description') }}</label>
                                <textarea class="form-control summernote @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4">{{ old('description', $menuItem->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="category">{{ __('Category') }}</label>
                                    <input type="text" class="form-control" id="category" name="category" 
                                           value="{{ old('category', $menuItem->category) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="cuisine_type">{{ __('Cuisine Type') }}</label>
                                    <input type="text" class="form-control" id="cuisine_type" name="cuisine_type" 
                                           value="{{ old('cuisine_type', $menuItem->cuisine_type) }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="ingredients">{{ __('Ingredients') }}</label>
                                <textarea class="form-control" id="ingredients" name="ingredients" 
                                          rows="3">{{ old('ingredients', $menuItem->ingredients) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="allergens">{{ __('Allergens') }}</label>
                                <input type="text" class="form-control" id="allergens" name="allergens" 
                                       value="{{ old('allergens', $menuItem->allergens) }}">
                                <small class="text-muted">{{ __('Separate with commas') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Pricing & Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="price">{{ __('Price') }} <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', $menuItem->price) }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="discount_price">{{ __('Discount Price') }}</label>
                                    <input type="number" step="0.01" class="form-control" 
                                           id="discount_price" name="discount_price" value="{{ old('discount_price', $menuItem->discount_price) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="preparation_time">{{ __('Preparation Time (minutes)') }}</label>
                                    <input type="number" class="form-control" id="preparation_time" 
                                           name="preparation_time" value="{{ old('preparation_time', $menuItem->preparation_time) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="calories">{{ __('Calories') }}</label>
                                    <input type="number" class="form-control" id="calories" 
                                           name="calories" value="{{ old('calories', $menuItem->calories) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Image Upload -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Image') }}</h5>
                        </div>
                        <div class="card-body">
                            @if($menuItem->image)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $menuItem->image) }}" alt="{{ $menuItem->name }}" 
                                         class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            @endif
                            <div class="mb-3">
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Availability') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="available_in_pos" 
                                           name="available_in_pos" value="1" {{ old('available_in_pos', $menuItem->available_in_pos) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="available_in_pos">
                                        {{ __('Available in POS') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="available_in_website" 
                                           name="available_in_website" value="1" {{ old('available_in_website', $menuItem->available_in_website) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="available_in_website">
                                        {{ __('Available in Website') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status" 
                                           name="status" value="1" {{ old('status', $menuItem->status) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status">
                                        {{ __('Active') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Special Tags -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('Special Tags') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_vegetarian" 
                                           name="is_vegetarian" value="1" {{ old('is_vegetarian', $menuItem->is_vegetarian) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_vegetarian">
                                        {{ __('Vegetarian') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_spicy" 
                                           name="is_spicy" value="1" {{ old('is_spicy', $menuItem->is_spicy) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_spicy">
                                        {{ __('Spicy') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="spice_level">{{ __('Spice Level') }}</label>
                                <select class="form-select" id="spice_level" name="spice_level">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="mild" {{ old('spice_level', $menuItem->spice_level) == 'mild' ? 'selected' : '' }}>{{ __('Mild') }}</option>
                                    <option value="medium" {{ old('spice_level', $menuItem->spice_level) == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                    <option value="hot" {{ old('spice_level', $menuItem->spice_level) == 'hot' ? 'selected' : '' }}>{{ __('Hot') }}</option>
                                    <option value="extra hot" {{ old('spice_level', $menuItem->spice_level) == 'extra hot' ? 'selected' : '' }}>{{ __('Extra Hot') }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_featured" 
                                           name="is_featured" value="1" {{ old('is_featured', $menuItem->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        {{ __('Featured') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_new" 
                                           name="is_new" value="1" {{ old('is_new', $menuItem->is_new) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_new">
                                        {{ __('New Item') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_popular" 
                                           name="is_popular" value="1" {{ old('is_popular', $menuItem->is_popular) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_popular">
                                        {{ __('Popular') }}
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="order">{{ __('Display Order') }}</label>
                                <input type="number" class="form-control" id="order" 
                                       name="order" value="{{ old('order', $menuItem->order) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save"></i> {{ __('Update Menu Item') }}
                    </button>
                    <a href="{{ route('admin.restaurant.menu-items.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').innerHTML = 
                    '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 200px;">';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
