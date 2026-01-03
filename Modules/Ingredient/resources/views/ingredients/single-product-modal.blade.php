<div class="modal-header mb-0">
    <h4 class="modal-title">View Product</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xl-9 col-lg-8">
            <div class="row">
                <div class="col-lg-6 invoice-col">
                    <p class="mb-2"><b class="me-2">Product Name: </b> {{ $product->name }}</p>
                    <p class="mb-2"><b class="me-2">Barcode:</b>{{ $product->barcode }}</p>
                    <p class="mb-2"><b class="me-2">Brand: </b>{{ $product->brand->name }}</p>
                    <p class="mb-2"><b class="me-2">Unit: </b>{{ $product->unit->name }}</p>
                    <p class="mb-2 d-none"><b class="me-2">Available in locations: </b> Quick Shifter</p>
                    <p class="mb-2"><b class="me-2">Created At: </b>{{ formatDate($product->created_at) }}</p>
                </div>
                <div class="col-lg-6 invoice-col">
                    <p class="mb-2"><b class="me-2">Category: </b>{{ $product->category->name }}</p>
                    <p class="mb-2"><b class="me-2">Manage Stock: </b>{{ $product->stock_alert ? 'Yes' : 'No' }}</p>
                    <p class="mb-2"><b class="me-2">Alert quantity: </b>{{ $product->stock_alert }} </p>
                    <p class="mb-2"><b class="me-2">Has IMEI/Model No: </b>{{ $product->hasVariant ? 'Yes' : 'No' }}
                    </p>
                    <p class="mb-2"><b class="me-2">Updated At: </b>{{ formatDate($product->updated_at) }}</p>
                </div>
                <br>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4">
            <div class="thumbnail view_product">
                <img src="{{ $product->singleImage }}" class="img-fluid" alt="Product image">
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-12">
            <h6 class="mb-1"><strong>Product Stock Details</strong></h6>
        </div>
        <div class="col-md-12" id="view_product_stock_details" data-product_id="82">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table mb-0 table-condensed">
                            <thead>
                                <tr>
                                    <th><b>Barcode</b></th>
                                    <th><b>Unit price</b></th>
                                    <th><b>stock</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $product->barcode }}</td>
                                    <td>{{ $product->current_price }}</td>
                                    <td>{{ $product->stock }} {{ $product->unit->ShortName }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
</div>
