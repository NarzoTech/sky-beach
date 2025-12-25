@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Manage Language') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <div class="section-header-back">
                    <a href="{{ route('admin.languages.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
                </div>




            </div>
    </div>
    <div class="section-body">
        <div class="mt-4 row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>{{ __('Edit Language') }}</h4>
                        <div>
                            <a href="{{ route('admin.languages.index') }}" class="btn btn-primary"><i
                                    class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.languages.update', $language->id) }}" enctype="multipart/form-data"
                            method="post">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="offset-md-3 col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            placeholder="{{ __('Enter Name') }}" value="{{ old('name', $language->name) }}">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="offset-md-3 col-md-6">
                                    <div class="form-group">
                                        <label for="code">{{ __('Code') }}</label>
                                        <select class="form-control select2" name="code" id="code">
                                            <option>{{ __('Select language') }}</option>
                                            @foreach ($all_languages as $lang)
                                                <option value="{{ $lang->code }}" @selected(old('code', $language->code) == $lang->code)>
                                                    {{ $lang->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('code')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="offset-md-3 col-md-6">
                                    <div class="form-group">
                                        <label for="direction">{{ __('Direction') }}</label>
                                        <select class="form-control" name="direction" id="direction">
                                            <option value="ltr" @selected(old('direction', $language->direction) == 'ltr')>
                                                {{ __('Left To Right') }}
                                            </option>
                                            <option value="rtl" @selected(old('direction', $language->direction) == 'rtl')>
                                                {{ __('Right to Left') }}
                                            </option>
                                        </select>
                                        @error('direction')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="offset-md-3 col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Icon') }}</label>
                                        <div id="image-preview" class="image-preview"
                                            @if ($language->icon) style="background-image: url({{ asset($language->icon) }}); background-size: cover; background-position: center center;" @endif>
                                            <label for="image-upload" id="image-label">{{ __('Icon') }}</label>
                                            <input type="file" name="icon" id="image-upload">
                                        </div>
                                        @error('icon')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-center offset-md-3 col-md-6">
                                    <x-admin.update-button :text="__('Update')" />
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
    <script src="{{ asset('backend/js/jquery.uploadPreview.min.js') }}"></script>
    <script>
        $.uploadPreview({
            input_field: "#image-upload",
            preview_box: "#image-preview",
            label_field: "#image-label",
            label_default: "{{ __('Choose Icon') }}",
            label_selected: "{{ __('Change Icon') }}",
            no_label: false,
            success_callback: null
        });
    </script>
@endpush
