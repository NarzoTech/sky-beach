@extends('admin.layouts.master')
@section('title', __('Print Barcode'))

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Print Barcode') }}</h4>
                                <a href="{{ route('admin.ingredient.index') }}" class="btn btn-primary"><i
                                        class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <i class='bx bx-search'></i>
                                                </div>
                                                <input type="text" class="form-control"
                                                    placeholder="Enter Product Name/Sku/scan barcode" id="searchProduct">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card-body pt-0">
                                <form action="{{ route('admin.ingredient.barcode.print') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table class="table table-condensed common_table mb-3"
                                                    id="purchase_entry_table">
                                                    <thead>
                                                        <tr>
                                                            <th>Product Name</th>
                                                            <th>Barcode</th>
                                                            <th>Quantity</th>
                                                            <th><i class="fa fa-trash" aria-hidden="true"></i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="mytab1">

                                                    </tbody>
                                                </table>
                                            </div>
                                            <hr />
                                        </div>


                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="mt-3 d-flex flex-wrap align-items-center ">
                                                    <label class="custom-control pl-0 mb-0">
                                                        <span><b>Print : </b></span>
                                                    </label>

                                                    <div
                                                        class="custom-control custom-checkbox d-flex flex-wrap align-items-center gap-2 ms-3">
                                                        <input type="checkbox" id="shopname" name="action[]"
                                                            class="custom-control-input" value="shopname" checked>
                                                        <span class="custom-control-label mb-0" for="shopname">Shop
                                                            Name</span>
                                                    </div>

                                                    <div
                                                        class="custom-control custom-checkbox d-flex flex-wrap align-items-center gap-2 ms-3">
                                                        <input type="checkbox" id="productname" name="action[]"
                                                            class="custom-control-input" value="productname" checked>
                                                        <span class="custom-control-label mb-0" for="productname">Product
                                                            Name</span>
                                                    </div>

                                                    <div
                                                        class="custom-control custom-checkbox d-flex flex-wrap align-items-center gap-2 ms-3">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="size_color" checked>
                                                        <span class="custom-control-label mb-0">Size Color</span>
                                                    </div>
                                                    <div
                                                        class="custom-control custom-checkbox d-flex flex-wrap align-items-center gap-2 ms-3">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="category">
                                                        <span class="custom-control-label mb-0">Category Name</span>
                                                    </div>
                                                    <div
                                                        class="custom-control custom-checkbox d-flex flex-wrap align-items-center gap-2 ms-3">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="selling_price">
                                                        <span class="custom-control-label mb-0">Selling Price</span>
                                                    </div>

                                                    <div
                                                        class="custom-control custom-checkbox d-flex flex-wrap align-items-center gap-2 ms-3">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="sku">
                                                        <span class="custom-control-label mb-0">Sku</span>
                                                    </div>
                                                    <div
                                                        class="custom-control custom-checkbox d-flex flex-wrap align-items-center gap-2 ms-3">
                                                        <input type="checkbox" class="custom-control-input" name="action[]"
                                                            value="include_vat">
                                                        <span class="custom-control-label mb-0">Include Vat</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"> <i class="fas fa-print me-2"></i>
                                            Save </button>
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
        $(document).ready(function() {
            $("#searchProduct").on("input", function() {
                var value = $(this).val().toLowerCase();
                $.ajax({
                    url: "{{ route('admin.ingredient.search') }}",
                    type: "GET",
                    data: {
                        search: value
                    },
                    success: function(res) {
                        if (res.status = true) {
                            let html = `
                            <tr>
                                <td>
                                    <input type="hidden" name="product_id[]" value="${res.data.id}">
                                    ${res.data.name}
                                    Barcode : ${res.data.barcode}
                                </td>

                                <td>
                                    <input type="hidden" name="barcode_id[]" value="${res.data.barcode}">
                                    ${res.data.barcode}
                                </td>

                                <td>
                                    <input type="text" name="qty[]" value="1" class="form-control">
                                </td>

                                <td>
                                    <a href="javascript:0" class="btn btn-sm btn-danger remove-product">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            `;

                            // check if barcode already exist

                            if ($("#mytab1 tr").length > 0) {
                                $("#mytab1 tr").each(function() {
                                    let code = $(this).find("td").eq(1).text()

                                    // write a regex for remove white space
                                    code = code.replaceAll(/\s/g, '');

                                    console.log(code, res.data.barcode);
                                    if (code != res.data
                                        .barcode) {
                                        $("#mytab1").append(html);
                                    }
                                })
                            } else {
                                $("#mytab1").append(html);
                            }
                        }
                    }
                })
            });

            $(document).on('click', '.remove-product', function(e) {
                $(this).closest('tr').remove();
            })
        });
    </script>
@endpush
