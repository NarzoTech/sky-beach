@extends('admin.layouts.master')
@section('title', __('Create Notice'))

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">


                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active">
                        <a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>

                </div>
            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">

                        <form method="POST" action="{{ route('admin.notice.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __('Create Notice') }}</div>
                                </div>

                                <div class="card-body">

                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="">Notice Title</label>
                                            <input type="text" name="notice_title" class="form-control"
                                                placeholder="Notice Title" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="">Business</label>
                                            <select class="form-control select2" multiple id="bussiness_id"
                                                name="bussiness_id[]" required>
                                            </select>
                                        </div>

                                        <div class="col-md-12 mt-3">
                                            <label for="">Message Details</label>
                                            <textarea name="message" rows="5" class="form-control" placeholder="Message Details"></textarea>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button type="submit" class="btn btn-success">Save</button>
                                    </div>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
