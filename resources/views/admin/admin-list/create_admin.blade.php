@extends('admin.layouts.master')
@section('title', __('Add Admin'))
@section('content')
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h3 class="section_title">{{ __('Add Admin') }}</h3>
                        <div>
                            @adminCan('admin.view')
                                <a href="{{ route('admin.admin.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>
                                    {{ __('Back') }}</a>
                            @endadminCan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <form action="{{ route('admin.admin.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                                <input type="text" id="name" class="form-control" name="name"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Email') }} <span class="text-danger">*</span></label>
                                                <input type="email" id="slug" class="form-control" name="email"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Password') }} <span class="text-danger">*</span></label>
                                                <input type="password" id="password" class="form-control" name="password"
                                                    required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Status') }} <span class="text-danger">*</span></label>
                                                <select name="status" class="form-control" required>
                                                    <option value="active">{{ __('Active') }}</option>
                                                    <option value="inactive">{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="role">{{ __('Assign Role') }} <span
                                                        class="text-danger">*</span></label>
                                                <select name="role[]" id="role"
                                                    class="form-control select2 @error('role') is-invalid @enderror"
                                                    multiple required>
                                                    <option value="" disabled>{{ __('Select Role') }}</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->name }}">{{ $role->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text-center col-md-8 offset-md-2">
                                            <x-admin.save-button :text="__('Save')"></x-admin.save-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
