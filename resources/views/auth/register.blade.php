@extends('layout')

@section('main-body')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="row">
            <div class="col-lg-6 col-md-6 col-12">
                <div class="form-group inflanar-form-input mg-top-20">
                    <label>{{ __('Name') }}*</label>
                    <input class="ecom-wc__form-input" type="text" name="name" placeholder="{{ __('Name') }}">
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-12">
                <div class="form-group inflanar-form-input mg-top-20">
                    <label>{{ __('Email') }}*</label>
                    <input class="ecom-wc__form-input" type="text" name="email" placeholder="{{ __('Email') }}">
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-12">
                <div class="form-group inflanar-form-input mg-top-20">
                    <label>{{ __('Password') }}*</label>
                    <input class="ecom-wc__form-input" type="password" name="password" placeholder="{{ __('Password') }}">
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-12">
                <div class="form-group inflanar-form-input mg-top-20">
                    <label>{{ __('Confirm Password') }}*</label>
                    <input class="ecom-wc__form-input" type="password" name="password_confirmation"
                        placeholder="{{ __('Confirm Password') }}">
                </div>
            </div>

            <div class="col-12">


                <!-- Login Button Group -->
                <div class="form-group mg-top-40">
                    <button type="submit" class="inflanar-btn"><span>{{ __('Create Account') }}</span></button>
                </div>
                <div class="inflanar-signin__bottom mg-top-20">
                    <p class="inflanar-signin__text">{{ __('Alread have an account?') }} <a
                            href="{{ route('login') }}">{{ __('Login') }}</a></p>
                </div>
            </div>
        </div>
    </form>
@endsection
