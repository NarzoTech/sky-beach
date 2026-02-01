@extends('admin.layouts.master')
@section('title', __('Clear cache'))
@section('content')
    <!-- Main Content -->

    <div class="card">
        <div class="card-body">
            <div class="alert alert-warning alert-has-icon">
                <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                <div class="alert-body">
                    <div class="alert-title">{{ __('Warning') }}</div>
                    {{ __('If you want to clearing all caches on your website may briefly affect its performance as cached data is regenerated.') }}
                </div>
            </div>

            <button class="btn btn-danger" data-bs-toggle="modal"
                data-bs-target="#cacheClearModal">{{ __('Clear cache') }}</button>

        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="cacheClearModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Cache Clear Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body py-0">
                    <p>{{ __('Are You sure want to clear cache ?') }}</p>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <form action="{{ route('admin.cache-clear-confirm') }}" method="POST">
                        @csrf
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Yes, Clear') }}</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
