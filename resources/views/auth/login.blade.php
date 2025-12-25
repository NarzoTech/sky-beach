@extends('layout')

@section('main-body')
    <form method="POST" action="{{ route('user-login') }}">
        @csrf
        <input type="hidden" name="user_type" value="client">
        <div class="row">
            <div class="col-12">
                <div class="form-group inflanar-form-input mg-top-20">
                    <label>{{ __('Email') }}*</label>
                    @if (env('APP_MODE') == 'DEMO')
                        <input class="ecom-wc__form-input" type="email" name="email" value="client@gmail.com">
                    @else
                        <input class="ecom-wc__form-input" type="email" name="email">
                    @endif

                </div>
                <div class="form-group inflanar-form-input mg-top-20">
                    <label>{{ __('Password') }}*</label>
                    @if (env('APP_MODE') == 'DEMO')
                        <input class="inflanar-signin__form-input" id="password-field" type="password" name="password"
                            value="1234">
                    @else
                        <input class="inflanar-signin__form-input" id="password-field" type="password" name="password">
                    @endif


                </div>
                <div class="form-group mg-top-20">
                    <div class="inflanar-signin__check-inline">
                        <div class="inflanar-signin__checkbox">
                            <div class="inflanar-signin__checkbox--group">

                                <input class="inflanar-signin__form-check" id="checkbox" name="remember" type="checkbox">
                                <label for="checkbox">{{ __('Remember Me') }}</label>
                            </div>
                        </div>
                        <div class="inflanar-signin__forgot">
                            <a href="{{ route('password.request') }}" class="forgot-pass">{{ __('Forgot Password?') }}</a>
                        </div>
                    </div>
                </div>


                <!-- Login Button Group -->
                <div class="form-group mg-top-40">
                    <button type="submit" class="inflanar-btn"><span>{{ __('Log In') }}</span></button>
                </div>
                <div class="inflanar-signin__bottom">
                    <div class="inflanar-signin__form-login--label"><span>{{ __('OR') }}</span></div>
                    <!-- Login Button Group -->
                    <div class="inflanar-signin__button--group">
                    </div>
                    <p class="inflanar-signin__text mg-top-20">{{ __('Don’t have an account ?') }} <a
                            href="{{ route('register') }}">{{ __('Create Account') }}</a></p>
                </div>
            </div>
        </div>
    </form>
@endsection
