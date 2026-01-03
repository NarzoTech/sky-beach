@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Add Product') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="section_title">{{ __('Add Product') }}</h4>
                    <div>
                        <a href="{{ route('admin.ingredient.index') }}" class="btn btn-primary"><i
                                class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="create_product_table" action="{{ route('admin.ingredient.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="name">{{ __('Name') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" id="name"
                                                value="{{ old('name') }}" required>
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sku">{{ __('SKU') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" id="sku" name="sku"
                                                    class="form-control currency" required />
                                                <span id="sku2"
                                                    class="input-group-text mb-0 generate_sku cursor-pointer"><i
                                                        class="fas fa-barcode"></i></span>
                                            </div>
                                            @error('sku')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id">{{ __('Category') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="category_id" id="categories" class="form-control select2"
                                                    required>
                                                    <option value="">{{ __('Select Categories') }}
                                                    </option>
                                                    @foreach ($categories as $cat)
                                                        <option value="{{ $cat->id }}">
                                                            {{ $cat->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="nput-group-text">
                                                    <a href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#categoryModal" class="btn btn-primary"><i
                                                            class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            @error('category_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="brand_id">{{ __('Brand') }}</label>
                                            <div class="input-group">
                                                <select name="brand_id" id="brand_id" class="form-control select2">
                                                    <option value="">{{ __('Select Brand') }}</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}">
                                                            {{ $brand->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="nput-group-text">
                                                    <a href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#brandModal" class="btn btn-primary"><i
                                                            class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            @error('brand_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cost">{{ __('Purchase Price') }}
                                                ({{ currency_icon() }})</label>
                                            <input type="number" name="cost" class="form-control" id="cost"
                                                value="{{ old('cost') }}" step="0.01">
                                            @error('cost')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Opening Stock') }}</label>
                                            <input type="number" class="form-control" name="stock"
                                                value="{{ old('stock', 0) }}">
                                            @error('stock')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Stock alert') }}</label>
                                            <input type="number" class="form-control" name="stock_alert"
                                                value="{{ old('stock_alert') }}">
                                            @error('stock_alert')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="short_description">{{ __('Short Description') }}</label>
                                            <textarea name="short_description" id="" rows="9" class="form-control">{!! old('short_description') !!}</textarea>
                                            @error('short_description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-md-12 mb-5">
                                        <div id="image-preview" class="image-preview">
                                            <label for="upload" id="image-label">{{ __('Image') }}</label>
                                            <input type="file" name="image" id="image-upload">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12">
                                        <div class="form-group">
                                            <label for="status">{{ __('Status') }}<span
                                                    class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="1">
                                                    {{ __('Active') }}</option>
                                                <option value="0">
                                                    {{ __('Inactive') }}</option>
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-12">
                                        <div class="form-group">
                                            <label for="unit_id">{{ __('Unit') }}<span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select name="unit_id" id="unit_id" class="form-control select2"
                                                    required>
                                                    <option value="">{{ __('Select Unit') }}
                                                    </option>
                                                    @foreach ($units as $unit)
                                                        <option value="{{ $unit->id }}">
                                                            {{ $unit->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="nput-group-text">
                                                    <a href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#unitModal" class="btn btn-primary"><i
                                                            class="fa fa-plus"></i></a>
                                                </div>
                                            </div>
                                            @error('unit_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <x-admin.save-button :text="__('Save')">
                                        </x-admin.save-button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- category create modal --}}
    @include('ingredient::ingredients.category.create-modal')
    @include('ingredient::ingredients.brand.create-modal')
    @include('ingredient::unit-types.unit-modal')
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

                $('.generate_sku').on('click', function() {
                    var sku = Math.floor(10000000 + Math.random() * 90000000);
                    $("[name='sku']").val(sku);
                });

                
                $('#categoryForm').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('admin.category.store') }}",
                        type: 'POST',
                        data: $('#categoryForm').serialize(),
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#categoryModal').modal('hide');
                                $('#categoryForm').trigger('reset');

                                let html =
                                    `<option value="${response.categories.id}">${response.categories.name}</option>`
                                $('#categories').append(html)
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(error) {
                            handleError(error)
                        }
                    })
                })
                $('#brandForm').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('admin.brand.store') }}",
                        type: 'POST',
                        data: $('#brandForm').serialize(),
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#brandModal').modal('hide');
                                $('#brandForm').trigger('reset');

                                let html =
                                    `<option value="${response.brand.id}">${response.brand.name}</option>`
                                $('#brand_id').append(html)
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(error) {
                            handleError(error)
                        }
                    })
                })
                $('#unitForm').on('submit', function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: "{{ route('admin.unit.store') }}",
                        type: 'POST',
                        data: $('#unitForm').serialize(),
                        success: function(response) {
                            if (response.status == 200) {
                                toastr.success(response.message);
                                $('#unitModal').modal('hide');
                                $('#unitForm').trigger('reset');

                                let html =
                                    `<option value="${response.unit.id}">${response.unit.name}</option>`
                                $('#unit_id').append(html)
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(error) {
                            handleError(error)
                        }
                    })
                })
                $('#base_unit').on("change", function() {
                    const baseUnit = $(this).val();
                    if (baseUnit) {
                        $('.operator').removeClass('d-none');
                        $('.operator_value').removeClass('d-none');
                    } else {
                        $('.operator').addClass('d-none');
                        $('.operator_value').addClass('d-none');
                    }
                });

                $.uploadPreview({
                    input_field: "#image-upload",
                    preview_box: "#image-preview",
                    label_field: "#image-label",
                    label_default: "Choose File",
                    label_selected: "Change File",
                    no_label: false
                });

            });

            function changeAttr(val, selectorName) {
                if (val == 1) {
                    $(`[name="${selectorName}"]`).attr('required', true);
                    $(`.${selectorName}`).removeClass('d-none')
                    $(`[name="${selectorName}"]`).removeAttr('disabled');
                } else {
                    $(`[name="${selectorName}"]`).removeAttr('required');
                    $(`[name="${selectorName}"]`).attr('disabled');
                    $(`.${selectorName}`).addClass('d-none')
                }
            }

        })(jQuery);
    </script>
@endpush
