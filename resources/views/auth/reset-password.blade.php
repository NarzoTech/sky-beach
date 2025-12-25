@extends('layout')

@section('main-body')
    <form method="POST" action="{{ route('reset-password-store', $token) }}">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="form-group inflanar-form-input">
                    <label>{{ __('Email') }}*</label>
                    <input class="ecom-wc__form-input" type="email" name="email" placeholder="{{ __('Email') }}"
                        value="{{ $user->email }}">
                </div>

                <div class="form-group inflanar-form-input mg-top-20">
                    <label>{{ __('Password') }}*</label>
                    <input class="ecom-wc__form-input" type="password" name="password" placeholder="{{ __('Password') }}">
                </div>

                <div class="form-group inflanar-form-input mg-top-20">
                    <label>{{ __('Confirm Password') }}*</label>
                    <input class="ecom-wc__form-input" type="password" name="password_confirmation"
                        placeholder="{{ __('Confirm Password') }}">
                </div>

                <!-- Login Button Group -->
                <div class="form-group mg-top-30">
                    <button type="submit" class="inflanar-btn"><span>{{ __('Send Reset Link') }}</span></button>
                </div>
                <div class="inflanar-signin__bottom">
                    <p class="inflanar-signin__text mg-top-20">{{ __('Go to login page') }} <a
                            href="{{ route('login') }}">{{ __('Login') }}</a></p>
                </div>
            </div>
        </div>
    </form>
@endsection
