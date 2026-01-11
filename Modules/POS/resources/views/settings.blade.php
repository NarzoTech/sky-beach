@extends('admin.layouts.master')

@section('title')
    <title>{{ __('Pos Settings') }}</title>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card card-info">
                            <h2 class="section-title ms-3">{{ __('Pos Settings') }}</h2>
                            <div class="card-body">
                                <form action="{{ route('admin.pos.settings.store') }}" method="POST" id="pos-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                <span class="fw-medium">{{ __('Show Phone') }}</span>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="show_phone" class="form-check-input" role="switch"
                                                        value="1" id="show_phone" @if ($pos_settings->show_phone) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                <span class="fw-medium">{{ __('Show Address') }}</span>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="show_address" class="form-check-input" role="switch"
                                                        value="1" id="show_address" @if ($pos_settings->show_address) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                <span class="fw-medium">{{ __('Show Email') }}</span>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="show_email" class="form-check-input" role="switch"
                                                        value="1" id="show_email" @if ($pos_settings->show_email) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                <span class="fw-medium">{{ __('Show Customer') }}</span>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="show_customer" class="form-check-input" role="switch"
                                                        value="1" id="show_customer" @if ($pos_settings->show_customer) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                <span class="fw-medium">{{ __('Show Warehouse') }}</span>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="show_warehouse" class="form-check-input" role="switch"
                                                        value="1" id="show_warehouse" @if ($pos_settings->show_warehouse) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                <span class="fw-medium">{{ __('Show Tax & Discount') }}</span>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="show_discount" class="form-check-input" role="switch"
                                                        value="1" id="show_discount" @if ($pos_settings->show_discount) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                <span class="fw-medium">{{ __('Show Barcode') }}</span>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="show_barcode" class="form-check-input" role="switch"
                                                        value="1" id="show_barcode" @if ($pos_settings->show_barcode) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                <span class="fw-medium">{{ __('Show Note to Customer') }}</span>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="show_note" class="form-check-input" role="switch"
                                                        value="1" id="show_note" @if ($pos_settings->show_note) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6 mb-3">
                                            <div class="d-flex align-items-center justify-content-between p-3 border rounded">
                                                <span class="fw-medium">{{ __('Auto Print Invoice') }}</span>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" name="is_printable" class="form-check-input" role="switch"
                                                        value="1" id="is_printable" @if ($pos_settings->is_printable) checked @endif>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4">
                                    <h5 class="mb-3">{{ __('Cart Behavior') }}</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="p-3 border rounded">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="fw-medium">{{ __('Merge Same Items in Cart') }}</span>
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" name="merge_cart_items" class="form-check-input" role="switch"
                                                            value="1" id="merge_cart_items" @if ($pos_settings->merge_cart_items) checked @endif>
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    {{ __('When enabled, adding the same item will update quantity. When disabled, each addition creates a new row.') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="card-footer text-right">
                                <button class="btn btn-primary" type="submit" form="pos-form">{{ __('Submit') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('components.admin.preloader')
@endsection

@push('js')
    <script>
        function deleteData(id) {
            $("#deleteForm").attr("action", '{{ route('admin.warehouse.destroy', '') }}' + "/" + id)
            $("#deleteModal").modal('show')
        }
    </script>
@endpush
