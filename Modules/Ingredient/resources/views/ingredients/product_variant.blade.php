@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Product') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>
                                    <a href="{{ route('admin.product-variant.create', $product->id) }}"
                                        class="btn btn-primary"><i class="fa fa-plus"></i>
                                        {{ __('Add Product Variant') }}</a>
                                </h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">{{ __('SN') }}</th>
                                                <th width="30%">{{ __('Sku') }}</th>
                                                <th width="10%">{{ __('Price') }}</th>
                                                <th width="10%">{{ __('Cost') }}</th>
                                                <th width="30%">{{ __('Attributes') }}</th>
                                                <th width="15%">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($variants as $index => $variant)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $variant['sku'] }}</td>
                                                    <td>{{ currency($variant['price']) }}</td>
                                                    <td>{{ currency($variant['cost']) }}</td>
                                                    <td>
                                                        @foreach ($variant['attributes'] as $attr)
                                                            {{ $attr['attribute_value'] }} @if (!$loop->last)
                                                                {{ __(' , ') }}
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.product-variant.edit', $variant['id']) }}"
                                                            class="btn btn-primary btn-sm"><i class="fa fa-edit"
                                                                aria-hidden="true"></i></a>

                                                        <button type="button" onclick="deleteData({{ $variant['id'] }})"
                                                            class="btn btn-danger btn-sm">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
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
        $(document).ready(function() {
            'use strict';
        });

        function deleteData(id) {
            var id = id;
            var url = '{{ route('admin.product-variant.delete', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
