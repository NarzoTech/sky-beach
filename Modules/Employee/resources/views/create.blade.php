@extends('admin.layouts.master')
@section('title', __('Create Employee'))


@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Create Employee') }}</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.employee.store') }}" method="post" id="add-employee-form"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">{{ __('Employee Name') }}<span
                                                        class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="name"
                                                    placeholder="Employee Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">{{ __('Designation') }}</label>
                                                <input type="text" class="form-control" name="designation"
                                                    placeholder="Designation">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Email') }}</label>
                                                <input type="email" class="form-control" name="email"
                                                    placeholder="Email" id="email">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Mobile') }}</label>
                                                <input type="text" class="form-control" name="mobile"
                                                    placeholder="Mobile" id="mobile">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('NID Number') }}</label>
                                                <input type="text" class="form-control" name="nid"
                                                    placeholder="NID Number" id="nid">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Image') }}</label>
                                                <input type="file" class="form-control" name="image"
                                                    placeholder="Image" id="image">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Address') }}
                                                </label>
                                                <input type="text" class="form-control" name="address"
                                                    placeholder="Address" id="address">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Joining Date') }}
                                                </label>
                                                <input type="text" class="form-control datepicker" name="join_date"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Salary') }}
                                                </label>
                                                <input type="text" class="form-control" name="salary"
                                                    placeholder="Salary" id="salary">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="yearly_leaves">{{ __('Yearly Leaves') }}
                                                </label>
                                                <input type="number" class="form-control" name="yearly_leaves"
                                                    placeholder="Yearly Leaves" id="yearly_leaves">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Status') }}
                                                </label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1">{{ __('Active') }}</option>
                                                    <option value="0">{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text-center offset-md-2 col-md-8">
                                            <x-admin.save-button :text="__('Save')">
                                            </x-admin.save-button>
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
