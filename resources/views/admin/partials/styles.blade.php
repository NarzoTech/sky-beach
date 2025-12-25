<link rel="stylesheet" href="{{ asset('backend/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/bootstrap-social.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/components.css') }}">
@if (session()->has('text_direction') && session()->get('text_direction') !== 'ltr')
    <link rel="stylesheet" href="{{ asset('backend/css/rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/dev_rtl.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('global/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/bootstrap4-toggle.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/dev.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/tagify.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/sweetalert2.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/fontawesome-iconpicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/clockpicker/dist/bootstrap-clockpicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/datetimepicker/jquery.datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/iziToast.min.css') }}">

<script src="{{ asset('global/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('backend/js/fontawesome.js') }}"></script>
