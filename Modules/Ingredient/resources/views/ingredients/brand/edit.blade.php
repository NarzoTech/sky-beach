@extends('admin.layouts.master')
@section('title', __('Brand Edit'))
@section('content')
    <div class="main-content">
        <section class="section">

    </div>
    <div class="section-body">
        <div class="mt-4 row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="section_title">{{ __('Edit Brand') }}</h4>
                        <a href="{{ route('admin.brand.index') }}" class="btn btn-primary"><i
                                class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.brand.update', $brand) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="lang_code" value="{{ request()->lang_code }}">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="name">{{ __('Name') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" id="name"
                                                    required value="{{ old('name', $brand->name) }}" required>
                                                @error('name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="slug">{{ __('Status') }}<span
                                                        class="text-danger">*</span></label>
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="1"
                                                        @if ($brand->status == 1) selected @endif>
                                                        {{ __('Active') }}</option>
                                                    <option value="0"
                                                        @if ($brand->status == 0) selected @endif>
                                                        {{ __('Inactive') }}</option>
                                                </select>
                                                @error('status')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="description">{{ __('Short Description') }}</label>
                                                <textarea name="description" id="description" cols="30" rows="5"
                                                    placeholder="{{ __('Enter Short Description') }}" class="w-100">{!! $brand->description !!}</textarea>
                                                @error('description')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 text-center">
                                            <x-admin.update-button :text="__('Update')">
                                            </x-admin.update-button>
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

    @if (Module::isEnabled('Media'))
        @stack('media_list_html')
    @endif
@endsection

@push('js')
    @if (Module::isEnabled('Media'))
        @stack('media_libary_js')
    @endif
@endpush


{{-- Media Css --}}
@push('css')
    @if (Module::isEnabled('Media'))
        @stack('media_libary_css')
    @endif
@endpush
