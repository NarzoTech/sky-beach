@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Service List') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
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
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Service List') }}</h4>
            </div>
            @adminCan('service.create')
                <div class="btn-actions-pane-right actions-icon-btn">
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addService" class="btn btn-primary"><i
                            class="fa fa-plus"></i>
                        {{ __('Add Service') }}</a>
                </div>
            @endadminCan
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $index => $service)
                            <tr>
                                <td>{{ $loop->first + $index }}</td>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->category->name }}</td>
                                <td>{{ currency($service->price) }}</td>
                                <td>
                                    @if (checkAdminHasPermission('service.edit') || checkAdminHasPermission('service.delete'))
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop{{ $service->id }}" type="button"
                                                class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                {{ __('Action') }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $service->id }}">
                                                @adminCan('service.edit')
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#editService{{ $service->id }}">{{ __('Edit') }}</a>
                                                @endadminCan
                                                @adminCan('service.delete')
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $service->id }})">
                                                        {{ __('Delete') }}</a>
                                                @endadminCan
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-empty-table :name="__('Service')" route="" create="no" :message="__('No data found!')"
                                colspan="5"></x-empty-table>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $services->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>


    {{-- add service --}}
    <div class="modal fade" id="addService">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="section_title">{{ __('Add Service') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body py-0">
                    <form action="{{ route('admin.service.store') }}" method="POST" id="add-service-form"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="category_id">{{ __('Category') }}<span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="status" class="form-control select2">
                                        <option value="">{{ __('Select Category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="price">{{ __('Price') }} <span class="text-danger">*</span> </label>
                                    <input type="number" class="form-control" id="price" name="price"
                                        value="0">
                                    <small class="form-text text-danger">Leave 0 for blank</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image">{{ __('Image') }}</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">{{ __('Description') }}</label>
                                    <textarea name="description" class="form-control height-80px" id="description" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">{{ __('Status') }}<span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">{{ __('Active') }}</option>
                                        <option value="0">{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-service-form">Save</button>
                </div>

            </div>
        </div>
    </div>


    {{-- edit service --}}
    @foreach ($services as $index => $service)
        <div class="modal fade" id="editService{{ $service->id }}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Service') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.service.update', $service->id) }}" method="POST"
                            id="edit-service-form{{ $service->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $service->name }}">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="category_id">{{ __('Category') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="category_id" id="status" class="form-control select2">
                                            <option value="">{{ __('Select Category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $service->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="price">{{ __('Price') }}</label>
                                        <input type="number" class="form-control" id="price" name="price"
                                            value="{{ $service->price }}" step="0.01">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="image">{{ __('Image') }}</label>
                                        <input type="file" class="form-control" id="image" name="image">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="description">{{ __('Description') }}</label>
                                        <textarea name="description" class="form-control height-80px" id="description" cols="30" rows="10">{{ $service->description }}</textarea>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }}<span
                                                class="text-danger">*</span></label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" {{ $service->status == 1 ? 'selected' : '' }}>
                                                {{ __('Active') }}
                                            </option>
                                            <option value="0" {{ $service->status == 0 ? 'selected' : '' }}>
                                                {{ __('Inactive') }}
                                            </option>
                                        </select>
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
                            form="edit-service-form{{ $service->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.service.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }

            // Initialize Select2 inside modals with dropdownParent to fix z-index issue
            $('#addService').on('shown.bs.modal', function() {
                $(this).find('.select2').select2({
                    dropdownParent: $('#addService')
                });
            });

            @foreach ($services as $service)
                $('#editService{{ $service->id }}').on('shown.bs.modal', function() {
                    $(this).find('.select2').select2({
                        dropdownParent: $('#editService{{ $service->id }}')
                    });
                });
            @endforeach
        </script>
    @endpush
@endsection
