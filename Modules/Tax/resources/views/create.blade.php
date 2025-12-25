@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Tax') }}</title>
@endsection
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <x-admin.breadcrumb title="{{ __('Tax') }}" :list="[
                __('Dashboard') => route('admin.dashboard'),
                __('Tax') => route('admin.tax.index'),
                __('Create Tax') => '#',
            ]" />

            <div class="section-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Create Tax') }}</h4>
                                <div>
                                    <a href="{{ route('admin.tax.index') }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.tax.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                                <input type="text" id="name" class="form-control" name="name"
                                                    value="{{ old('name') }}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Rate') }} <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text">%</span>
                                                    <input type="number" class="form-control" name="rate"
                                                        value="{{ old('rate') }}" step="0.01">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Status') }} <span class="text-danger">*</span></label>
                                                <select name="status" class="form-control">
                                                    <option value="1">{{ __('Active') }}</option>
                                                    <option value="0">{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button class="btn btn-primary">{{ __('Save') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>

    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $("#name").on("focusout", function(e) {
                    $("#slug").val(convertToSlug($(this).val()));
                })
            });
        })(jQuery);

        function convertToSlug(Text) {
            return Text
                .toLowerCase()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '-');
        }
    </script>
@endsection
