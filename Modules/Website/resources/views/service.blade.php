@extends('website::layouts.master')

@section('title', __('Our Services') . ' - ' . config('app.name'))

@php
    $sections = site_sections('service');
    $breadcrumb = $sections['service_breadcrumb'] ?? null;
    $listSection = $sections['service_list'] ?? null;
@endphp

@section('content')
    <!--==========BREADCRUMB AREA START===========-->
    @if (!$breadcrumb || $breadcrumb->section_status)
        <section class="breadcrumb_area"
            style="background: url({{ $breadcrumb?->background_image ? asset($breadcrumb->background_image) : asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>{{ $breadcrumb?->title ?? __('Our Services') }}</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">{{ __('Home') }}</a></li>
                                <li><a
                                        href="{{ route('website.service') }}">{{ $breadcrumb?->title ?? __('Our Services') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!--==========BREADCRUMB AREA END===========-->


    <!--==========SERVICE START===========-->
    @if (!$listSection || $listSection->section_status)
        <section class="service_area pt_95 xs_pt_75">
            <div class="container">
                <div class="row">
                    @forelse($services as $service)
                        <div class="col-lg-4 col-sm-6 wow fadeInUp">
                            <div class="service_item">
                                <img src="{{ $service->image_url }}" alt="{{ $service->title }}" class="img-fluid w-100">
                                <div class="service_item_overly">
                                    <div class="service_item_icon">
                                        @if ($service->icon)
                                            <img src="{{ $service->icon_url }}" alt="{{ $service->title }}"
                                                class="img-fluid w-100">
                                        @else
                                            <img src="{{ asset('website/images/fresh.png') }}" alt="{{ $service->title }}"
                                                class="img-fluid w-100">
                                        @endif
                                    </div>
                                    <h2>{{ $service->title }}</h2>
                                    <p>{{ Str::limit(strip_tags($service->short_description), 120) }}</p>
                                    <a class="common_btn" href="{{ route('website.service-details', $service->slug) }}">
                                        <span class="icon">
                                            <img src="{{ asset('website/images/eye.png') }}" alt="{{ __('view') }}"
                                                class="img-fluid w-100">
                                        </span>
                                        {{ __('View All Details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <div class="alert alert-info">
                                <h4>{{ __('No services available') }}</h4>
                                <p>{{ __('Please check back later for our services.') }}</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if ($services instanceof \Illuminate\Pagination\LengthAwarePaginator && $services->hasPages())
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
