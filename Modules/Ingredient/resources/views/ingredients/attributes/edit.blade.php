@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Attribute List') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Edit Attribute') }}</h4>
                                <a href="{{ route('admin.attribute.index') }}" class="btn btn-primary"><i
                                        class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.attribute.update', $attribute->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="name">{{ __('Name') }}<span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control"
                                                            id="name" required
                                                            value="{{ old('name', $attribute->name) }}">
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
                                                            value="{{ old('slug', $attribute->slug) }}"
                                                            class="form-control">
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
                                                    @foreach ($attribute->values as $val)
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <div class="d-flex justify-content-between">
                                                                    <input type="text"
                                                                        name="values[{{ $val->id }}]"
                                                                        class="form-control" id="values" required
                                                                        value="{{ old('values', $val->name) }}">
                                                                    @if (!$loop->first)
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm remove-values ml-2"
                                                                            data-id="{{ $val->id }}"><i
                                                                                class="fas fa-trash"></i></button>
                                                                    @endif
                                                                </div>
                                                                @error('values')
                                                                    <span class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    @endforeach
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
                    const id = $(this).data('id');

                    if (id) {
                        $.ajax({
                            url: "{{ route('admin.attribute.value.delete') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                id: id,
                                attribute_id: "{{ $attribute->id }}"
                            },
                            success: function(response) {
                                toastr.success('Value deleted successfully');
                            }
                        });
                    } else {
                        $(this).closest('.form-group').remove();
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
