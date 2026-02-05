@extends('admin.layouts.master')
@section('title', __('Edit Menu Category'))
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="card mb-3 page-title-card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="section_title">{{ __('Edit Menu Category') }}: {{ $category->name }}</h4>
                        <a href="{{ route('admin.menu-category.index') }}" class="btn btn-primary">
                            <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <form action="{{ route('admin.menu-category.update', $category->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Basic Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Basic Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" id="name" required value="{{ old('name', $category->name) }}">
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="slug">{{ __('Slug') }}</label>
                                                <input type="text" name="slug" class="form-control" id="slug" value="{{ old('slug', $category->slug) }}">
                                                @error('slug')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="parent_id">{{ __('Parent Category') }}</label>
                                                <select name="parent_id" id="parent_id" class="form-control select2">
                                                    <option value="">{{ __('None (Top Level)') }}</option>
                                                    @foreach ($parentCategories as $parent)
                                                        @if ($parent->id != $category->id)
                                                            <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                                                {{ $parent->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('parent_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="display_order">{{ __('Display Order') }}</label>
                                                <input type="number" name="display_order" class="form-control" id="display_order" value="{{ old('display_order', $category->display_order) }}" min="0">
                                                @error('display_order')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="description">{{ __('Description') }}</label>
                                                <textarea name="description" class="form-control" id="description" rows="3">{{ old('description', $category->description) }}</textarea>
                                                @error('description')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Image -->
                            <div class="card">
                                <div class="card-header">
                                    <h5>{{ __('Image') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div id="image-preview" class="mb-3">
                                        @if ($category->image)
                                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <input type="file" name="image" class="form-control" id="image" accept="image/*">
                                        @error('image')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>{{ __('Status') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}<span class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" {{ old('status', $category->status) == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                            <option value="0" {{ old('status', $category->status) == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="is_featured">{{ __('Featured') }}</label>
                                        <select name="is_featured" id="is_featured" class="form-control">
                                            <option value="0" {{ old('is_featured', $category->is_featured) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                            <option value="1" {{ old('is_featured', $category->is_featured) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        </select>
                                        @error('is_featured')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="is_popular">{{ __('Popular') }}</label>
                                        <select name="is_popular" id="is_popular" class="form-control">
                                            <option value="0" {{ old('is_popular', $category->is_popular) == 0 ? 'selected' : '' }}>{{ __('No') }}</option>
                                            <option value="1" {{ old('is_popular', $category->is_popular) == 1 ? 'selected' : '' }}>{{ __('Yes') }}</option>
                                        </select>
                                        @error('is_popular')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="card mt-3">
                                <div class="card-body text-center">
                                    <x-admin.save-button :text="__('Update Category')"></x-admin.save-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('js')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $('[name="name"]').on('input', function() {
                    var name = $(this).val();
                    var slug = convertToSlug(name);
                    $("[name='slug']").val(slug);
                });

                // Image preview
                $('#image').on('change', function() {
                    var file = this.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#image-preview').html('<img src="' + e.target.result + '" style="max-width: 100%; max-height: 200px; border-radius: 5px;">');
                        }
                        reader.readAsDataURL(file);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
