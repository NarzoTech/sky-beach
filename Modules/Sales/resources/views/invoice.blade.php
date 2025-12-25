@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Invoice') }}</title>
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
@endpush


@section('content')
    <div class="main-content">
        <div class="container-fluid">
            @include('sales::invoice-content')
        </div>
    </div>
@endsection
