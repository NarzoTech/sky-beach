@extends('admin.layouts.master')
@section('title', __('Invoice'))
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
