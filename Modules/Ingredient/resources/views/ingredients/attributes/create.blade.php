@extends('admin.layouts.master')
@section('title', __('Attribute List'))
@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Add Attribute') }}</h4>
                                <a href="{{ route('admin.attribute.index') }}" class="btn btn-primary"><i
                                        class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.attribute.store') }}" method="post">
                                    @csrf
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="name">{{ __('Name') }}<span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control"
                                                            id="name" required value="{{ old('name') }}">
                                                        @error('name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="slug">{{ __('Slug') }}<span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" id="slug" name="slug"
                                                            value="{{ old('slug') }}" class="form-control">
                                                        @error('slug')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div
                                                        class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                                        <h5 class="mb-0">{{ __('Attribute Values') }}</h5>
                                                        <button type="button"
                                                            class="btn btn-primary btn-sm add-values">{{ __('+ Add Values') }}</button>
                                                    </div>
                                                </div>
                                                <div class="col-12 values-container">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <input type="text" name="values[]" class="form-control"
                                                                id="values" required placeholder="Value 1">
                                                            @error('values')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-center">
                                                    <x-admin.save-button :text="__('Save')"> </x-admin.save-button>
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
                $('.add-values').on('click', function() {
                    let html = '';
                    // count of input fields
                    let count = $('.values-container').find('.form-group').length;
                    html += `<div class="col-12">
                                <div class="form-group">
                                <div class="d-flex justify-content-between">
                                    <input type="text" name="values[]" class="form-control" id="values"
                                    required placeholder="Value ${count + 1}">
                                    <button type="button" class="btn btn-danger btn-sm remove-values ml-2"><i class="fas fa-trash"></i></button>
                                </div>
                                @error('values')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            </div>
                            `;
                    $('.values-container').append(html)
                })
                $('.values-container').on('click', '.remove-values', function() {
                    $(this).closest('.form-group').remove();
                });
            });
        })(jQuery);
    </script>
@endpush
