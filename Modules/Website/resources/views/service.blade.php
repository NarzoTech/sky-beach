@extends('website::layouts.master')

@section('title', 'Our Services - CTAKE')

@php
    $sections = site_sections('service');
    $breadcrumb = $sections['service_breadcrumb'] ?? null;
    $listSection = $sections['service_list'] ?? null;
@endphp

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        @if(!$breadcrumb || $breadcrumb->section_status)
        <section class="breadcrumb_area" style="background: url({{ $breadcrumb?->background_image ? asset($breadcrumb->background_image) : asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $breadcrumb?->title ?? 'Our Services' }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">Home</a></li>
                                <li><a href="{{ route('website.service') }}">{{ $breadcrumb?->title ?? 'Our Services' }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========SERVICE START===========-->
        @if(!$listSection || $listSection->section_status)
        <section class="service_area pt_95 xs_pt_75">
            <div class="container">
                <div class="row">
                    @forelse($services as $service)
                    <div class="col-lg-4 col-sm-6 wow fadeInUp">
                        <div class="service_item">
                            @if($service->image)
                                <img src="{{ asset($service->image) }}" alt="{{ $service->title }}" class="img-fluid w-100">
                            @else
                                <img src="{{ asset('website/images/service_1.jpg') }}" alt="{{ $service->title }}" class="img-fluid w-100">
                            @endif
                            <div class="service_item_overly">
                                <div class="service_item_icon">
                                    @if($service->icon)
                                        <img src="{{ asset($service->icon) }}" alt="{{ $service->title }}" class="img-fluid w-100">
                                    @else
                                        <img src="{{ asset('website/images/fress.png') }}" alt="{{ $service->title }}" class="img-fluid w-100">
                                    @endif
                                </div>
                                <h2>{{ $service->title }}</h2>
                                <p>{{ Str::limit($service->short_description, 120) }}</p>
                                <a class="common_btn" href="{{ route('website.service-details', $service->slug) }}">
                                    <span class="icon">
                                        <img src="{{ asset('website/images/eye.png') }}" alt="view" class="img-fluid w-100">
                                    </span>
                                    View All Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <h4>No services available</h4>
                            <p>Please check back later for our services.</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                @if($services instanceof \Illuminate\Pagination\LengthAwarePaginator && $services->hasPages())
                <div class="pagination_area mt_60 wow fadeInUp">
                    <nav aria-label="Page navigation">
                        {{ $services->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
                @endif
            </div>
        </section>
        @endif
        <!--==========SERVICE END===========-->
@endsection
