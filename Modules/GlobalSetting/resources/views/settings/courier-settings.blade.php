@extends('admin.layouts.master')
@section('title', __('Settings'))
@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="header-title text-center" style="color: ;font-size: 27px">
                                    <b>Pathao Settings</b>
                                </h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.courier.settings.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="courier_type" value="1">
                                    <div class="form-group row">
                                        <input type="hidden" name="PATHAO_SANDBOX" value="2">

                                        <div class="col-md-12 mb-3">
                                            <label for="">PATHAO CLIENT ID</label>
                                            <input type="text" name="PATHAO_CLIENT_ID" value=""
                                                class="form-control" autocomplete="off" placeholder="PATHAO CLIENT ID">
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label for="">PATHAO CLIENT SECRET</label>
                                            <input type="text" name="PATHAO_CLIENT_SECRET" value=""
                                                class="form-control" autocomplete="off" placeholder="PATHAO CLIENT SECRET">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="">PATHAO USERNAME</label>
                                            <input type="text" name="PATHAO_USERNAME" value="" class="form-control"
                                                autocomplete="off" placeholder="PATHAO USERNAME">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="">PATHAO PASSWORD</label>
                                            <input type="text" name="PATHAO_PASSWORD" value="" class="form-control"
                                                autocomplete="off" placeholder="PATHAO PASSWORD">
                                        </div>
                                        <div class="float-right mt-3">
                                            <button type="submit" class="btn btn-success">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card table-responsive mt-4 theme-color-set">
                            <div class="card-header">
                                <h4 class="header-title text-center" style="color: ;font-size: 27px">
                                    <b>Steadfast Setting</b>
                                </h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.courier.settings.store') }}" method="POST"
                                    enctype="multipart/form-data">

                                    @csrf
                                    <input type="hidden" name="courier_type" value="3">
                                    <div class="form-group row">

                                        <div class="col-md-12 mb-3">
                                            <label for="">API KEY</label>
                                            <input type="text" name="API_KEY" value="" class="form-control"
                                                autocomplete="off" placeholder="API KEY">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="">SECRET KEY</label>
                                            <textarea name="SECRET_KEY" class="form-control" placeholder="SECRET KEY" id="SECRET_KEY" cols="30" rows="10"></textarea>
                                        </div>
                                        <div class="float-right mt-3">
                                            <button type="submit" class="btn btn-success">Save</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card table-responsive mt-4 theme-color-set">
                            <div class="card-header">
                                <h4 class="header-title text-center" style="color: ;font-size: 27px">
                                    <b>Redx Setting</b>
                                </h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.courier.settings.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="courier_type" value="2">
                                    <input type="hidden" name="REDX_SANDBOX" value="2">
                                    <div class="form-group row">

                                        <div class="col-md-12 mb-3">
                                            <label for="">REDX_ACCESS_TOKEN</label>
                                            <textarea name="REDX_ACCESS_TOKEN" class="form-control" placeholder="Enter REDX_ACCESS_TOKEN" id="REDX_ACCESS_TOKEN"
                                                cols="30" rows="10"></textarea>
                                        </div>
                                        <div class="float-right mt-3">
                                            <button type="submit" class="btn btn-success">Save</button>
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
