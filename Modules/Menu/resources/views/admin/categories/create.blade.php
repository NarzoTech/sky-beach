@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Add Menu Category') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Add Menu Category') }}</h4>
                                <div>
                                    <a href="{{ route('admin.menu-category.index') }}" class="btn btn-primary">
                                        <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.menu-category.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control" id="name"
                                                            required value="{{ old('name') }}">
                                                        @error('name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="slug">{{ __('Slug') }}</label>
                                                        <input type="text" name="slug" class="form-control" id="slug"
                                                            value="{{ old('slug') }}" placeholder="auto-generated-from-name">
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
                                                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                                                    {{ $parent->name }}
                                                                </option>
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
                                                        <input type="number" name="display_order" class="form-control"
                                                            id="display_order" value="{{ old('display_order', 0) }}" min="0">
                                                        @error('display_order')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="description">{{ __('Description') }}</label>
                                                        <textarea name="description" class="form-control" id="description"
                                                            rows="3">{{ old('description') }}</textarea>
                                                        @error('description')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="image">{{ __('Image') }}</label>
                                                        <input type="file" name="image" class="form-control" id="image"
                                                            accept="image/*">
                                                        @error('image')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="status">{{ __('Status') }}<span class="text-danger">*</span></label>
                                                        <select name="status" id="status" class="form-control">
                                                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>
                                                                {{ __('Active') }}
                                                            </option>
                                                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>
                                                                {{ __('Inactive') }}
                                                            </option>
                                                        </select>
                                                        @error('status')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="is_featured">{{ __('Featured') }}</label>
                                                        <select name="is_featured" id="is_featured" class="form-control">
                                                            <option value="0" {{ old('is_featured', 0) == 0 ? 'selected' : '' }}>
                                                                {{ __('No') }}
                                                            </option>
                                                            <option value="1" {{ old('is_featured') == 1 ? 'selected' : '' }}>
                                                                {{ __('Yes') }}
                                                            </option>
                                                        </select>
                                                        @error('is_featured')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 text-center mt-3">
                                                    <x-admin.save-button :text="__('Save')"></x-admin.save-button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
            });
        })(jQuery);
    </script>
@endpush
