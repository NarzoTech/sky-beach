@extends('admin.layouts.master')
@section('title', __('Customer Details'))
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card shadow">
                            @if ($user->image)
                                <img src="{{ asset($user->image) }}" class="w-100">
                            @else
                                <img src="{{ asset($setting->default_avatar) }}" class="w-100">
                            @endif

                            <div class="container my-3">
                                <h4>{{ html_decode($user->name) }}</h4>

                                @if ($user->phone)
                                    <p class="title">{{ html_decode($user->phone) }}</p>
                                @endif

                                <p class="title">{{ html_decode($user->email) }}</p>

                                <p class="title">{{ __('Joined') }} : {{ formatDate($user->created_at, 'd-m-Y h:i A') }}</p>

                                @if ($user->is_banned == 'yes')
                                    <p class="title">{{ __('Banned') }} : <b>{{ __('Yes') }}</b></p>
                                @else
                                    <p class="title">{{ __('Banned') }} : <b>{{ __('No') }}</b></p>
                                @endif

                                @if ($user->email_verified_at)
                                    <p class="title">{{ __('Email verified') }} : <b>{{ __('Yes') }}</b> </p>
                                @else
                                    <p class="title">{{ __('Email verified') }} : <b>{{ __('None') }}</b> </p>

                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#verifyModal"
                                        class="btn btn-success mb-3">{{ __('Send Verify Link to Mail') }}</a>
                                @endif

                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sendMailModal"
                                    class="btn btn-primary sendMail mb-3">{{ __('Send Mail To Customer') }}</a>

                                @if ($user->is_banned == 'yes')
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#bannedModal"
                                        class="btn btn-warning mb-3">{{ __('Remove to Banned') }}</a>
                                @else
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#bannedModal"
                                        class="btn btn-warning mb-3">{{ __('Make a Banned') }}</a>
                                @endif

                                <a onclick="deleteData({{ $user->id }})" href="javascript:;"
                                    class="btn btn-danger">{{ __('Delete Account') }}</a>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-9">
                        {{-- profile information card area --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="service_card">{{ __('Profile Information') }}</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.customer-info-update', $user->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="">{{ __('Name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ html_decode($user->name) }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="">{{ __('Phone') }}</label>
                                                <input type="text" name="phone" class="form-control"
                                                    value="{{ html_decode($user->phone) }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <div class="form-group">
                                                <label for="">{{ __('Address') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="address" class="form-control"
                                                    value="{{ html_decode($user->address) }}">
                                            </div>
                                        </div>

                                        <div class="col-md-12 mt-4">
                                            <button type="submit"
                                                class="btn btn-primary w-100">{{ __('Update Profile') }}</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- change password card area --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="service_card">{{ __('Change Password') }}</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.customer-password-change', $user->id) }}" method="post">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="">{{ __('Password') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="password" class="form-control">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="">{{ __('Confirm Password') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                        </div>

                                        <div class="col-md-12 mt-4">
                                            <button type="submit"
                                                class="btn btn-primary w-100">{{ __('Change Password') }}</button>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- banned history card area --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="service_card">{{ __('Banned History') }}</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="30%">{{ __('Subject') }}</th>
                                            <th width="30%">{{ __('Description') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($banned_histories as $banned_history)
                                            <tr>
                                                <td>{{ $banned_history->subject }}</td>
                                                <td>{!! nl2br($banned_history->description) !!}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Start Banned modal -->
    <div class="modal fade" id="bannedModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Banned request confirmation') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="{{ route('admin.send-banned-request', $user->id) }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="">{{ __('Subject') }}</label>
                                <input type="text" class="form-control" name="subject">
                            </div>

                            <div class="form-group">
                                <label for="">{{ __('Description') }}</label>
                                <textarea name="description" class="form-control text-area-5" id="" cols="30" rows="10"></textarea>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Send Request') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Banned modal -->

    <!-- Start Verify modal -->
    <div class="modal fade" id="verifyModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Send verify link to customer mail') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <p>{{ __('Are you sure want to send verify link to customer mail?') }}</p>

                        <form action="{{ route('admin.send-verify-request', $user->id) }}" method="POST">
                            @csrf

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Send Request') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Verify modal -->

    <!-- Start Send Mail modal -->
    <div class="modal fade" id="sendMailModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Send mail to customer') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="{{ route('admin.send-mail-to-customer', $user->id) }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="">{{ __('Subject') }}</label>
                                <input type="text" class="form-control" name="subject">
                            </div>

                            <div class="form-group">
                                <label for="">{{ __('Description') }}</label>
                                <textarea name="description" class="form-control text-area-5" id="" cols="30" rows="10"></textarea>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Send Mail') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Send Mail modal -->

    @push('js')
        <script>
            function deleteData(id) {
                $("#deleteForm").attr("action", '{{ url('/admin/customer-delete/') }}' + "/" + id)
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
