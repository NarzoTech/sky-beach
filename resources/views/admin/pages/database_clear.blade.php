@extends('admin.layouts.master')
@section('title', __('Clear Database'))
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
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
                    </div>
                </div>
            </div>
            <div class="modal fade" tabindex="-1" role="dialog" id="clearDatabaseModal">
                <div class="modal-dialog" role="document">
                    <form class="modal-content" action="{{ route('admin.database-clear-success') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Clear Database Confirmation') }}</h5>
                            <button type="button" class="btn-close btn" data-bs-dismiss="modal">
                                <i class="fas fa-times fa-fw"></i>
                            </button>
                        </div>
                        <div class="modal-body pt-0">
                            <p>{{ __('Are you really want to clear this database?') }}</p>
                            <input type="password" id="password" name="password" placeholder="{{ __('Password') }}"
                                required="true" class="form-control">

                        </div>
                        <div class="modal-footer bg-whitesmoke br">
                            <button type="button" class="btn btn-primary"
                                data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-danger m-0">{{ __('Yes, Clear') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
