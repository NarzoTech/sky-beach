@extends('admin.layouts.master')
@section('title', __('Edit Employee'))


@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="section_title">{{ __('Edit Employee') }}</h4>

                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.employee.update', $employee->id) }}" method="post"
                                    id="add-employee-form" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">{{ __('Employee Name') }}<span
                                                        class="text-danger">*</span>
                                                </label>
                                                <input type="text" class="form-control" name="name"
                                                    placeholder="Employee Name" value="{{ $employee->name }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="">{{ __('Designation') }}</label>
                                                <input type="text" class="form-control" name="designation"
                                                    placeholder="Designation" value="{{ $employee->designation }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Email') }}</label>
                                                <input type="email" class="form-control" name="email"
                                                    placeholder="Email" id="email" value="{{ $employee->email }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Mobile') }}</label>
                                                <input type="text" class="form-control" name="mobile"
                                                    placeholder="Mobile" id="mobile" value="{{ $employee->mobile }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('NID Number') }}</label>
                                                <input type="text" class="form-control" name="nid"
                                                    placeholder="NID Number" id="nid" value="{{ $employee->nid }}">
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
                                                    placeholder="Address" id="address" value="{{ $employee->address }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Joining Date') }}
                                                </label>
                                                <input type="text" class="form-control datepicker" name="join_date"
                                                    value="{{ formatDate($employee->join_date) }}" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Salary') }}
                                                </label>
                                                <input type="text" class="form-control" name="salary"
                                                    placeholder="Salary" id="salary" value="{{ $employee->salary }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="yearly_leaves">{{ __('Yearly Leaves') }}
                                                </label>
                                                <input type="text" class="form-control" name="yearly_leaves"
                                                    placeholder="Yearly Leaves" id="yearly_leaves"
                                                    value="{{ $employee->yearly_leaves }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">{{ __('Status') }}
                                                </label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1"
                                                        @if ($employee->status == 1) selected @endif>
                                                        {{ __('Active') }}</option>
                                                    <option value="0"
                                                        @if ($employee->status == 0) selected @endif>
                                                        {{ __('Inactive') }}</option>
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


@push('js')
    <script>
        $(document).ready(function() {
            $('#account_type').on('change', function() {
                var account_type = $(this).val();
                if (account_type == 'mobile_banking') {
                    $('.mobile_section').removeClass('d-none');
                    $('.bank-card').addClass('d-none');
                    $('.bank').addClass('d-none');

                    // disabled others field
                    removeDisabled('.mobile_section');

                } else if (account_type == 'card') {
                    $('.bank-card').removeClass('d-none');
                    $('.mobile_section').addClass('d-none');
                    $('.bank').addClass('d-none');

                    removeDisabled('.bank-card');
                } else if (account_type == 'bank') {
                    $('.bank').removeClass('d-none');
                    $('.mobile_section').addClass('d-none');
                    $('.bank-card').addClass('d-none');

                    removeDisabled('.bank');
                } else {
                    $('.mobile_section').addClass('d-none');
                    $('.bank-card').addClass('d-none');
                    $('.bank').addClass('d-none');
                }
            });
        });

        function removeDisabled(selector) {
            // remove all disabled attribute

            $('.mobile_section').find('input, select').each(function() {
                $(this).attr('disabled', true);
            });

            $('.bank-card').find('input, select').each(function() {
                $(this).attr('disabled', true);
            });

            $('.bank').find('input, select').each(function() {
                $(this).attr('disabled', true);
            });

            // remove disabled attribute in side the selector
            $(selector).find('input, select').each(function() {
                $(this).removeAttr('disabled');
            });
        }
    </script>
@endpush
