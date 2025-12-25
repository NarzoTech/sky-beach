@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Settings') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="{{ route('admin.update-general-setting') }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card">
                                <div class="card-header">
                                    <div class="section_title">{{ __('Business Settings') }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div id="logo-preview" class="image_preview"
                                                @if (!empty($setting->logo)) style="background-image: url({{ asset($setting->logo) }}); background-size: cover; background-position: center center;" @endif>
                                                <label for="logo-upload" id="logo-label">{{ __('Logo') }}</label>
                                                <input type="file" name="logo" id="logo-upload" hidden>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div id="favicon-preview" class="image_preview"
                                                @if (!empty($setting->favicon)) style="background-image: url({{ asset($setting->favicon) }}); background-size: cover; background-position: center center;" @endif>
                                                <label for="favicon-upload" id="favicon-label">{{ __('Favicon') }}</label>
                                                <input type="file" name="favicon" id="favicon-upload" hidden>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div id="login-preview" class="image_preview"
                                                @if (!empty($setting->login)) style="background-image: url({{ asset($setting->login) }}); background-size: cover; background-position: center center;" @endif>
                                                <label for="login-upload" id="login-label">{{ __('Login') }}</label>
                                                <input type="file" name="login" id="login-upload" hidden>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div id="default_avatar-preview" class="image_preview"
                                                @if (!empty($setting->default_avatar)) style="background-image: url({{ asset($setting->default_avatar) }}); background-size: cover; background-position: center center;" @endif>
                                                <label for="default_avatar-upload"
                                                    id="default_avatar-label">{{ __('Default Avatar') }}</label>
                                                <input type="file" name="default_avatar" id="default_avatar-upload"
                                                    hidden>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="">Business Name</label>
                                                <input type="text" name="app_name" value="{{ $setting->app_name }}"
                                                    class="form-control" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Mobile Phone</label>
                                                <input type="text" name="mobile" value="{{ $setting->mobile }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Email</label>
                                                <input type="email" name="email" value="{{ $setting->email }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Address</label>
                                                <input type="text" name="address" value="{{ $setting->address }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Business Type</label>
                                                <input type="text" name="type" value="{{ $setting->type }}"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">City</label>
                                                <input type="text" name="city" value="{{ $setting->city }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Zip</label>
                                                <input type="text" name="zip" value="{{ $setting->zip }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Country</label>
                                                <select name="country" id="" class="select2">
                                                    @foreach ($allCountries as $country)
                                                        <option value="{{ $country }}"
                                                            {{ $country == $setting->country ? 'selected' : '' }}>
                                                            {{ $country }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Website</label>
                                                <input type="text" name="website" value="{{ $setting->website }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Business Start Date</label>
                                                <input type="text" name="start_date"
                                                    value="{{ $setting->start_date }}" class="form-control datepicker"
                                                    autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Date Format</label>
                                                <select class="form-control" name="date_format">
                                                    <option value="d/m/Y"
                                                        {{ $setting->date_format == 'd/m/Y' ? 'selected' : '' }}>
                                                        d/m/Y ({{ date('d/m/Y') }})
                                                    </option>
                                                    <option value="d-m-Y"
                                                        {{ $setting->date_format == 'd-m-Y' ? 'selected' : '' }}>
                                                        d-m-Y ({{ date('d-m-Y') }})
                                                    </option>
                                                    <option value="Facebook"
                                                        {{ $setting->date_format == 'Facebook' ? 'selected' : '' }}>
                                                        Facebook ({{ now()->format('jS F') }})
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Time Format</label>
                                                <select class="form-control" name="time_format">
                                                    <option value="h:ia"
                                                        {{ $setting->time_format == 'h:ia' ? 'selected' : '' }}>
                                                        12-hour (08:46pm)
                                                    </option>
                                                    <option value="H:i"
                                                        {{ $setting->time_format == 'H:i' ? 'selected' : '' }}>
                                                        24-hour (20:46)
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Timezone</label>
                                                <select name="timezone" id="" class="form-control select2">
                                                    @foreach ($all_timezones as $timezone)
                                                        <option value="{{ $timezone->name }}"
                                                            @selected($setting->timezone == $timezone->name)>
                                                            {{ $timezone->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Report Date Sorting</label>
                                                <select class="form-control" name="report_default_days">
                                                    <option value="1"
                                                        {{ $setting->report_default_days == 1 ? 'selected' : '' }}>
                                                        Current Date
                                                    </option>
                                                    <option value="7"
                                                        {{ $setting->report_default_days == 7 ? 'selected' : '' }}>
                                                        Last 7 Days
                                                    </option>
                                                    <option value="30"
                                                        {{ $setting->report_default_days == 30 ? 'selected' : '' }}>
                                                        Last 30 Days
                                                    </option>
                                                    <option value="365"
                                                        {{ $setting->report_default_days == 365 ? 'selected' : '' }}>
                                                        Last 365 Days
                                                    </option>
                                                    <option value=""
                                                        {{ $setting->report_default_days == '' ? 'selected' : '' }}>
                                                        All
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Color</label>
                                                <input type="color" name="color" value="{{ $setting->color }}"
                                                    class="form-control">
                                                <input type="hidden" name="status" value="1">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Currency</label>
                                                <input type="text" name="currency" value="{{ $setting->currency }}"
                                                    class="form-control" placeholder="Currency">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Select Business Vat(%)</label>
                                                <input type="number" name="vat" value="{{ $setting->vat }}"
                                                    class="form-control" step=".01" placeholder="Ex: 10"
                                                    step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Number of Digits in Phone Number</label>
                                                <input type="number" name="min_phone_number"
                                                    value="{{ $setting->min_phone_number }}" class="form-control"
                                                    onchange="checkNumber()">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Invoice Prefix</label>
                                                <input type="text" name="invoice_prefix"
                                                    value="{{ $setting->invoice_prefix }}" class="form-control"
                                                    placeholder="EX: AS">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label for="">Invoice Suffix</label>
                                                <input type="number" name="invoice_suffix"
                                                    value="{{ $setting->invoice_suffix }}" class="form-control"
                                                    placeholder="EX: 10000001">
                                                <span class="text-info mb-0">
                                                    If invoice prefix or suffix is null system will auto generate invoice
                                                    number
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-1 col-md-2 col-sm-4">
                                            <button type="submit" class="btn btn-success">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection


@push('js')
    <script>
        prevImage('logo-upload', 'logo-preview', 'logo-label');
        prevImage('favicon-upload', 'favicon-preview', 'favicon-label');
        prevImage('login-upload', 'login-preview', 'login-label');
        prevImage('default_avatar-upload', 'default_avatar-preview', 'default_avatar-label');
    </script>
@endpush
