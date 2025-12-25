@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Purchases Return Type') }}</title>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="{{ __('Search') }}..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <select name="order_by" id="order_by" class="form-control">
                                        <option value="">{{ __('Order By') }}</option>
                                        <option value="asc" {{ request('order_by') == 'asc' ? 'selected' : '' }}>
                                            {{ __('ASC') }}
                                        </option>
                                        <option value="desc" {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                            {{ __('DESC') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <select name="par-page" id="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('10') }}
                                        </option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('50') }}
                                        </option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('100') }}
                                        </option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                            {{ __('All') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn bg-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-5">
        <div class="card-header">
            <h4 class="section_title">{{ __('Purchases Return Type') }}</h4>
            @adminCan('purchase.return.type.create')
                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addType" class="btn btn-primary"><i
                        class="fa fa-plus"></i>
                    {{ __('Add Purchases Return Type') }}</a>
            @endadminCan
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Created By') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lists as $list)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $list->name }}</td>
                                        <td>{{ $list->createdBy?->name }}</td>
                                        <td>
                                            @if (checkAdminHasPermission('purchase.return.type.edit') || checkAdminHasPermission('purchase.return.type.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $list->id }}" type="button"
                                                        class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                        {{ __('Action') }}
                                                    </button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $list->id }}">
                                                        @adminCan('purchase.return.type.edit')
                                                            <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                                data-bs-target="#editType{{ $list->id }}">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('purchase.return.type.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $list->id }})">
                                                                {{ __('Delete') }}</a>
                                                        @endadminCan
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if (request()->get('par-page') !== 'all')
                        <div class="float-right">
                            {{ $lists->onEachSide(0)->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addType">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="section_title">{{ __('Add Type') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body py-0">
                    <form action="{{ route('admin.purchase.return.type.store') }}" method="POST" id="add-type-form">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-type-form">Save</button>
                </div>

            </div>
        </div>
    </div>

    @foreach ($lists as $list)
        <div class="modal fade" id="editType{{ $list->id }}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="section_title">{{ __('Edit Return Type') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.purchase.return.type.update', $list->id) }}" method="POST"
                            id="edit-bank-form{{ $list->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $list->name }}">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary"
                            form="edit-bank-form{{ $list->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection
@push('js')
    <script>
        function deleteData(id) {
            $("#deleteForm").attr("action", '{{ route('admin.purchase.return.type.destroy', '') }}' + "/" + id)
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
