@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Product') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">

                            </div>
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="5%">{{ __('Check') }}</th>
                                                <th width="15%">{{ __('Photo') }}</th>
                                                <th width="30%">{{ __('Name') }}</th>
                                                <th width="10%">{{ __('Price') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <form method="POST"
                                                action ="{{ route('admin.store-related-products', $product->id) }}">
                                                @csrf
                                                @foreach ($products as $prod)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="product_id[]"
                                                                value="{{ $prod->id }}"
                                                                @if (in_array($prod->id, $relatedProducts)) checked @endif>
                                                        </td>
                                                        <td>
                                                            <img src="{{ asset($prod->photo) }}" alt="{{ $prod->name }}"
                                                                class="img-thumbnail" style="width: 100px;">
                                                        </td>
                                                        <td>{{ $prod->name }}</td>
                                                        <td>{{ $prod->price }}</td>
                                                    </tr>
                                                @endforeach

                                                {{-- save button --}}

                                                <tr>
                                                    <td colspan="4">
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ __('Save') }}</button>
                                                    </td>
                                                </tr>
                                            </form>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $products->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
