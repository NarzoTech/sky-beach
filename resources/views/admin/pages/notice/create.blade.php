@extends('admin.layouts.master')
@section('title', __('Create Notice'))

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="card mb-3 page-title-card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="section_title">{{ __('Create Notice') }}</h4>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                            <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                        </a>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.notice.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
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
        </section>
    </div>
@endsection
