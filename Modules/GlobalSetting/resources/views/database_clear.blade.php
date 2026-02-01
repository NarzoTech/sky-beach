@extends('admin.layouts.master')
@section('title', __('Database clear'))
@section('content')
    <!-- Main Content -->
    <div class="card">
        <div class="card-body">
            <div class="alert alert-warning alert-has-icon">
                <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                <div class="alert-body">
                    <div class="alert-title">{{ __('Warning') }}</div>
                    {{ __('If you want to use the software from scratch, you have to clear database. You do not need to remove the existing data one by one') }}
                </div>
            </div>

            <button class="btn btn-danger" data-bs-toggle="modal"
                data-bs-target="#clearDatabaseModal">{{ __('Clear Database') }}</button>

        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="clearDatabaseModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Clear Database Confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <p>{{ __('Are you really want to clear this database?') }}</p>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <form id="deleteForm" action="{{ route('admin.database-clear-success') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Yes, Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
