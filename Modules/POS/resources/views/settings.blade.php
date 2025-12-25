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
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="show_phone" class="custom-switch-input"
                                                        value="1" @if ($pos_settings->show_phone) checked @endif>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description">{{ __('Show Phone') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="show_address" class="custom-switch-input"
                                                        value="1" @if ($pos_settings->show_address) checked @endif>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description">{{ __('Show Address') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="show_email" class="custom-switch-input"
                                                        value="1" @if ($pos_settings->show_email) checked @endif>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description">{{ __('Show Email') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="show_customer" class="custom-switch-input"
                                                        value="1" @if ($pos_settings->show_customer) checked @endif>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description">{{ __('Show Customer') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="show_warehouse" class="custom-switch-input"
                                                        value="1" @if ($pos_settings->show_warehouse) checked @endif>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span
                                                        class="custom-switch-description">{{ __('Show Warehouse') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="show_discount" class="custom-switch-input"
                                                        value="1" @if ($pos_settings->show_discount) checked @endif>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span
                                                        class="custom-switch-description">{{ __('Show Tax & Discount & Shipping') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="show_barcode" class="custom-switch-input"
                                                        value="1" @if ($pos_settings->show_barcode) checked @endif>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description">{{ __('Show Barcode') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="show_note" class="custom-switch-input"
                                                        value="1" @if ($pos_settings->show_note) checked @endif>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span
                                                        class="custom-switch-description">{{ __('Show Note to Customer') }}</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="custom-switch mt-2">
                                                    <input type="checkbox" name="is_printable" class="custom-switch-input"
                                                        value="1" @if ($pos_settings->is_printable) checked @endif>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span
                                                        class="custom-switch-description">{{ __('Print Invoice Automatically') }}</span>
                                                </label>
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
