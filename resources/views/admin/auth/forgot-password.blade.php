@extends('admin.auth.app')
@section('title')
    <title>{{ __('Forgot Password') }}</title>
@endsection
@section('content')
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-12 pt-5">
            <h4 class="mb-1">{{ __('Forgot Password') }}</h4>
            <form action="{{ route('admin.forget-password') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email exampleInputEmail" type="email" class="form-control" name="email" tabindex="1"
                        autofocus placeholder="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <button id="adminLoginBtn" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                        {{ __('Send Reset Link') }}
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
