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
                                    <a href="{{ route('admin.ingredient.create') }}" class="btn btn-primary"><i
                                            class="fa fa-plus"></i>
                                        {{ __('Add Product') }}</a>
                                </h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Photo') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Sku') }}</th>
                                                <th>{{ __('Brand') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th>{{ __('Cost') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th>{{ __('Unit') }}</th>
                                                <th>{{ __('Stock Quantity') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            @foreach ($products as $index => $product)
                                                <tr>
                                                    <td>{{ ++$index }}</td>
                                                    <td> <img class="rounded-circle" src="{{ $product->ImagesUrl[0] }}"
                                                            alt="" width="100px" height="100px"></td>
                                                    <td>{{ $product->name }} </td>
                                                    <td>{{ $product->sku }}</td>
                                                    <td>{{ $product->brand->name }}</td>
                                                    <td>{{ $product->category->name }}</td>
                                                    <td>{{ currency($product->cost) }}</td>
                                                    <td>{{ currency($product->price) }}</td>
                                                    <td>{{ $product->unit->name }}</td>
                                                    <td>{{ $product->stock }}{{ $product->unit->ShortName }}</td>
                                                    <td>
                                                        @if ($product->status == 1)
                                                            <a href="javascript:;"
                                                                onclick="changeProductStatus({{ $product->id }})">
                                                                <input id="status_toggle" type="checkbox" checked
                                                                    data-bs-toggle="toggle" data-on="{{ __('Active') }}"
                                                                    data-off="{{ __('InActive') }}" data-onstyle="success"
                                                                    data-offstyle="danger">
                                                            </a>
                                                        @else
                                                            <a href="javascript:;"
                                                                onclick="changeProductStatus({{ $product->id }})">
                                                                <input id="status_toggle" type="checkbox"
                                                                    data-bs-toggle="toggle" data-on="{{ __('Active') }}"
                                                                    data-off="{{ __('InActive') }}" data-onstyle="success"
                                                                    data-offstyle="danger">
                                                            </a>
                                                        @endif
                                                    </td>
                                                    <td class="d-flex justify-content-center align-items-center">
                                                        <a href="{{ route('admin.ingredient.edit', ['product' => $product->id]) }}"
                                                            class="btn btn-primary btn-sm me-2"><i class="fa fa-edit"
                                                                aria-hidden="true"></i></a>

                                                        <button type="button" data-bs-toggle="modal"
                                                            @if ($product->orders->count() > 0) data-bs-target="#canNotDeleteModal"
                                                                @else
                                                                onclick="deleteData({{ $product->id }})" @endif
                                                            class="btn btn-danger btn-sm me-2">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </button>
                                                        <div class="dropdown d-inline">
                                                            <button class="btn btn-primary btn-sm dropdown-toggle"
                                                                type="button" id="dropdownMenuButton2"
                                                                data-bs-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                                <i class="fas fa-cog"></i>
                                                            </button>

                                                            <div class="dropdown-menu" x-placement="top-start"
                                                                style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -131px, 0px);">
                                                                <a class="dropdown-item has-icon"
                                                                    href="{{ route('admin.product-variant', $product->id) }}"><i
                                                                        class="fas fa-cog"></i>{{ __('Product Variant') }}</a>

                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $products->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="canNotDeleteModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    {{ __('You can not delete this product. Because there are one or more order has been created in this product.') }}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            'use strict';
        });

        function deleteData(id) {
            var id = id;
            var url = '{{ route('admin.ingredient.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
