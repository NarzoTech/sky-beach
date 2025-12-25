@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Product Gallery Images') }}</title>
@endsection


@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Product Gallery Images') }}</h4>
                                <div>
                                    <a href="{{ route('admin.product.index') }}" class="btn btn-primary"><i
                                            class="fa fa-arrow-left"></i>{{ __('Back') }}</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.product-gallery.store', $product->id) }}" method="post">
                                    @csrf
                                    <div class="row">
                                        @if (Module::isEnabled('Media'))
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    @php
                                                        $images = $product->images;

                                                        $images = $images ? explode(',', $images[0]) : [];
                                                    @endphp
                                                    <x-media::media-input name="images[]" multiple="yes" :dataImages="$images"
                                                        label_text="Gallery Images" />
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="text-center offset-md-2 col-md-8">
                                            <x-admin.save-button :text="__('Save')">
                                            </x-admin.save-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Media Modal Show --}}
    @if (Module::isEnabled('Media'))
        @stack('media_list_html')
    @endif
@endsection

@push('js')

    @if (Module::isEnabled('Media'))
        @stack('media_libary_js')
    @endif
@endpush

{{-- Media Css --}}
@push('css')
    @if (Module::isEnabled('Media'))
        @stack('media_libary_css')
    @endif
@endpush
