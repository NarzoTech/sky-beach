@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Area List') }}</title>
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
                                        class="form-control" placeholder="Search..." autocomplete="off">
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
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> {{ __('Area List') }}</h4>
            </div>
            @adminCan('customer.area.create')
                <div class="btn-actions-pane-right actions-icon-btn">
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addarea" class="btn btn-primary"><i
                            class="fa fa-plus"></i>
                        {{ __('Add Area') }}</a>
                </div>
            @endadminCan
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($areas as $index => $area)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $area->name }}</td>
                                <td>
                                    @if (checkAdminHasPermission('customer.area.edit') || checkAdminHasPermission('customer.area.delete'))
                                        <div class="btn-group" role="group">

                                            <button id="btnGroupDrop{{ $area->id }}" type="button"
                                                class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $area->id }}">
                                                @adminCan('customer.area.edit')
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#editarea{{ $area->id }}">Edit</a>
                                                @endadminCan
                                                @adminCan('customer.area.delete')
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $area->id }})">
                                                        Delete</a>
                                                @endadminCan
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-empty-table :name="__('area')" route="" create="no" :message="__('No data found!')"
                                colspan="3"></x-empty-table>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $areas->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>


    {{-- add area --}}
    <div class="modal fade" id="addarea">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add area') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body pt-0 pb-0">
                    <form action="{{ route('admin.area.store') }}" method="POST" id="add-area-form">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">{{ __('Area Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-area-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit area --}}
    @foreach ($areas as $index => $area)
        <div class="modal fade" id="editarea{{ $area->id }}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Add area') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        <form action="{{ route('admin.area.update', $area->id) }}" method="POST"
                            id="edit-area-form{{ $area->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{ __('area Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $area->name }}">
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
                            form="edit-area-form{{ $area->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
@endsection



@push('js')
    <script>
        function deleteData(id) {
            let url = '{{ route('admin.area.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
