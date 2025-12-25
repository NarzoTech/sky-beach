@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Edit Product') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="section_title">{{ __('Edit Product') }}</h4>
                    <div>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-primary"><i
                                class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    <form class="create_product_table" action="{{ route('admin.product.update', $product) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">{{ __('Name') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" id="name"
                                                value="{{ old('name', $product->name) }}" required>
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
                                                <input type="text" name="sku" class="form-control currency"
                                                    id="sku" value="{{ old('sku', $product->sku) }}" required>
                                                <div class="input-group-text mb-0 edit_sku generate_sku cursor-pointer">
                                                    <i class="fas fa-barcode"></i>
                                                </div>
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
                                            <select name="category_id" id="categories" class="form-control select2"
                                                required>
                                                <option value="">{{ __('Select Categories') }}
                                                </option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}"
                                                        @if (old('category_id', $product->category_id) == $cat->id) selected @endif>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="brand_id">{{ __('Brand') }}</label>
                                            <select name="brand_id" id="brand_id" class="form-control select2">
                                                <option value="">{{ __('Select Brand') }}</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}"
                                                        @if (old('brand_id', $product->brand_id) == $brand->id) selected @endif>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                                value="{{ old('cost', $product->cost) }}" step="0.01">
                                            @error('cost')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('Stock alert') }}</label>
                                            <input type="number" class="form-control" name="stock_alert"
                                                value="{{ old('stock_alert', $product->stock_alert) }}">
                                            @error('stock_alert')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="short_description">{{ __('Short Description') }}</label>
                                            <textarea name="short_description" id="" cols="30" rows="9" class="form-control">{!! old('short_description', $product->short_description) !!}</textarea>
                                            @error('short_description')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="row">
                                    <div class="col-12">

                                        <div id="image-preview" class="image-preview"
                                            style="background-image: url('{{ asset($product->image) }}')">
                                            <label for="upload" id="image-label">{{ __('Image') }}</label>
                                            <input type="file" name="image" id="image-upload">
                                        </div>

                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="status">{{ __('Status') }}<span
                                                    class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" @if (old('status', $product->status) == 1) selected @endif>
                                                    {{ __('Active') }}</option>
                                                <option value="0" @if (old('status', $product->status) == 0) selected @endif>
                                                    {{ __('Inactive') }}</option>
                                            </select>
                                            @error('status')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="unit_id">{{ __('Unit') }}<span
                                                    class="text-danger">*</span></label>
                                            <select name="unit_id" id="unit_id" class="form-control select2" required>
                                                <option value="">{{ __('Select Unit') }}
                                                </option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}"
                                                        @if (old('unit_id', $product->unit_id) == $unit->id) selected @endif>
                                                        {{ $unit->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('unit_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="">
                                            <x-admin.update-button :text="__('Update')">
                                            </x-admin.update-button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
                $('[name="is_warranty"]').on('change', function() {
                    var is_warranty = $(this).val();
                    changeAttr(is_warranty, 'warranty_duration')
                })
                $('[name="is_partial"]').on('change', function() {
                    var is_partial = $(this).val();
                    changeAttr(is_partial, 'partial_amount')
                })
                $('[name="is_pre_order"]').on('change', function() {
                    var is_pre_order = $(this).val();
                    changeAttr(is_pre_order, 'release_date')
                    changeAttr(is_pre_order, 'max_product')
                })

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
