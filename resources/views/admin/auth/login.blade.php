@extends('admin.auth.app')
@section('title')
    <title>{{ __('Login') }}</title>
@endsection
@section('content')
    <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
        <div class="w-px-400 mx-auto mt-12 pt-5">
            <h4 class="mb-1">Welcome to {{ $setting->app_name }}! 👋</h4>
            <p class="mb-6">Please sign-in to your account and start the adventure</p>

            <form id="formAuthentication" class="mb-6" action="{{ route('admin.store-login') }}" method="post">
                @csrf
                <div class="mb-6">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    @if (app()->isLocal() && app()->hasDebugModeEnabled())
                        <input id="email exampleInputEmail" type="email" class="form-control" name="email"
                            tabindex="1" autofocus value="admin@gmail.com">
                    @else
                        <input id="email exampleInputEmail" type="email" class="form-control" name="email"
                            tabindex="1" autofocus value="{{ old('email') }}">
                    @endif
                </div>
                <div class="mb-6 form-password-toggle">
                    <label class="form-label" for="password">{{ __('Password') }}</label>
                    <div class="input-group input-group-merge">
                        @if (app()->isLocal() && app()->hasDebugModeEnabled())
                            <input id="password exampleInputPassword" type="password" class="form-control" name="password"
                                tabindex="2" value="1234">
                        @else
                            <input id="password exampleInputPassword" type="password" class="form-control" name="password"
                                tabindex="2">
                        @endif
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                </div>
                <div class="my-8">
                    <div class="d-flex justify-content-between">
                        <div class="form-check mb-0 ms-2">
                            <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                            <label class="form-check-label" for="remember-me">
                                Remember Me
                            </label>
                        </div>
                        <a href="{{ route('admin.password.request') }}">
                            <p class="mb-0">Forgot Password?</p>
                        </a>
                    </div>
                </div>
                <button class="btn btn-primary d-grid w-100" type="submit">
                    Sign in
                </button>
            </form>
        </div>
    </div>
@endsection
