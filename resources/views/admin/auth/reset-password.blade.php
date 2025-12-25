@extends('admin.auth.app')
@section('title')
    <title>{{ __('Reset Password') }}</title>
@endsection
@section('content')
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-12 pt-5">
            <h4 class="mb-1">{{ __('Reset Password') }}</h4>
            <form action="{{ route('admin.password.reset-store', $token) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email exampleInputEmail" type="email" class="form-control" name="email" tabindex="1"
                        autofocus placeholder="{{ old('email') }}" value="{{ $admin->email }}">
                </div>
                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control" name="password"
                        placeholder="{{ __('Password') }}">
                </div>
                <div class="form-group">
                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                        placeholder="{{ __('Confirm Password') }}">
                </div>

                <div class="form-group">
                    <button id="adminLoginBtn" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                        {{ __('Reset Password') }}
                    </button>
                </div>
                <div class="form-group">
                    <div class="d-block">
                        <a href="{{ route('admin.login') }}">
                            {{ __('Go to login page') }} -> </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
