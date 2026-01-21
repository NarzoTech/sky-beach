@extends('website::layouts.master')

@section('title', 'Our Chefs - CTAKE')

@section('content')
        <!--==========BREADCRUMB AREA START===========-->
        <section class="breadcrumb_area" style="background: url({{ asset('website/images/breadcrumb_bg.jpg') }});">
            <div class="container">
                <div class="row wow fadeInUp">
                    <div class="col-12">
                        <div class="breadcrumb_text">
                            <h1>Our Chefs</h1>
                            <ul>
                                <li><a href="{{ route('website.index') }}">Home</a></li>
                                <li><a href="{{ route('website.chefs') }}">Our Chefs</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--==========BREADCRUMB AREA END===========-->


        <!--==========CHEFS START===========-->
        <section class="shefs pt_95 xs_pt_70">
            <div class="container">
                <div class="row">
                    @forelse($chefs as $chef)
                    <div class="col-xl-3 col-sm-6 col-lg-4 wow fadeInUp">
                        <div class="single_chef">
                            <a href="{{ route('website.chefs') }}" class="single_chef_img">
                                @if($chef->image)
                                    <img src="{{ asset($chef->image) }}" alt="{{ $chef->name }}" class="img-fluid w-100">
                                @else
                                    <img src="{{ asset('website/images/chef_img_1.jpg') }}" alt="{{ $chef->name }}" class="img-fluid w-100">
                                @endif
                                <span>{{ $chef->designation }}</span>
                            </a>
                            <div class="single_chef_text">
                                <a class="title" href="{{ route('website.chefs') }}">{{ $chef->name }}</a>
                                <ul>
                                    @if($chef->facebook)
                                        <li><a class="facebook" href="{{ $chef->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                    @endif
                                    @if($chef->twitter)
                                        <li><a class="twitter" href="{{ $chef->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                    @endif
                                    @if($chef->linkedin)
                                        <li><a class="linkedin" href="{{ $chef->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                    @endif
                                    @if($chef->instagram)
                                        <li><a class="instagram" href="{{ $chef->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">
                        <div class="alert alert-info">
                            <h4>No chefs available</h4>
                            <p>Please check back later to meet our team.</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                @if($chefs instanceof \Illuminate\Pagination\LengthAwarePaginator && $chefs->hasPages())
                <div class="pagination_area mt_60 wow fadeInUp">
                    <nav aria-label="Page navigation">
                        {{ $chefs->links('pagination::bootstrap-4') }}
                    </nav>
                </div>
                @endif
            </div>
        </section>
        <!--==========CHEFS END===========-->
@endsection
