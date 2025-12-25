@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Expense Type List') }}</title>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-4 col-md-6">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <select name="order_type" id="order_type" class="form-control">
                                        <option value="id" {{ request('order_type') == 'id' ? 'selected' : '' }}>
                                            {{ __('Serial') }}</option>
                                        <option value="name" {{ request('order_type') == 'name' ? 'selected' : '' }}>
                                            {{ __('Name') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
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
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <select name="par_page" id="par_page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par_page') ? 'selected' : '' }}>
                                            {{ __('10') }}
                                        </option>
                                        <option value="50" {{ '50' == request('par_page') ? 'selected' : '' }}>
                                            {{ __('50') }}
                                        </option>
                                        <option value="100" {{ '100' == request('par_page') ? 'selected' : '' }}>
                                            {{ __('100') }}
                                        </option>
                                        <option value="all" {{ 'all' == request('par_page') ? 'selected' : '' }}>
                                            {{ __('All') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
                                    <button type="submit" class="btn bg-label-primary">Search</button>
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
                <h4 class="section_title"> {{ __('Expense Type List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @adminCan('expense.type.create')
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addExpense" class="btn btn-primary"><i
                            class="fa fa-plus"></i>
                        {{ __('Add Expense Type') }}</a>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Parent Type') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($types as $index => $type)
                            <tr>
                                <td>{{ $types->firstItem() + $index }}</td>
                                <td>{{ $type->name }}</td>
                                <td>
                                    @if ($type->parent_id)
                                        <span class="badge bg-info">{{ $type->parent->name ?? 'N/A' }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Parent') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if (checkAdminHasPermission('expense.type.edit') || checkAdminHasPermission('expense.type.delete'))
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop{{ $type->id }}" type="button"
                                                class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $type->id }}">
                                                @adminCan('expense.type.edit')
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#editType{{ $type->id }}">Edit</a>
                                                @endadminCan
                                                @adminCan('expense.type.delete')
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $type->id }})">
                                                        Delete</a>
                                                @endadminCan
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-empty-table :name="__('Expense Type')" route="" create="no" :message="__('No data found!')"
                                colspan="7"></x-empty-table>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (request()->get('par_page') !== 'all')
                <div class="float-right">
                    {{ $types->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>


    {{-- add Expense type --}}
    <div class="modal fade" id="addExpense">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Add Expense Type') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body py-0">
                    <form action="{{ route('admin.expense.type.store') }}" method="POST" id="add-bank-form">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="parent_id">{{ __('Parent Type') }}</label>
                                    <select name="parent_id" id="parent_id" class="form-control select2">
                                        <option value="">{{ __('Select Parent (Optional)') }}</option>
                                        @foreach ($parentTypes as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                        @endforeach
                                    </select>
                                    <small
                                        class="form-text text-muted">{{ __('Leave empty to create a parent type') }}</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="add-bank-form">Save</button>
                </div>

            </div>
        </div>
    </div>

    {{-- edit expense type --}}
    @foreach ($types as $index => $type)
        <div class="modal fade" id="editType{{ $type->id }}">
            <div class="modal-dialog">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Edit Expense Type') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <form action="{{ route('admin.expense.type.update', $type->id) }}" method="POST"
                            id="edit-type-form{{ $type->id }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">{{ __('Name') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ $type->name }}" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="parent_id_edit_{{ $type->id }}">{{ __('Parent Type') }}</label>
                                        <select name="parent_id" id="parent_id_edit_{{ $type->id }}"
                                            class="form-control select2">
                                            <option value="">{{ __('Select Parent (Optional)') }}</option>
                                            @foreach ($parentTypes as $parent)
                                                @if ($parent->id != $type->id)
                                                    <option value="{{ $parent->id }}"
                                                        {{ $type->parent_id == $parent->id ? 'selected' : '' }}>
                                                        {{ $parent->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <small
                                            class="form-text text-muted">{{ __('Leave empty to make this a parent type') }}</small>
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
                            form="edit-type-form{{ $type->id }}">{{ __('Update') }}</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach
    @push('js')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    dropdownParent: $('#addExpense, .modal.show'),
                    width: '100%'
                });

                // Reinitialize select2 when modal is shown (important for edit modals)
                $('.modal').on('shown.bs.modal', function() {
                    $(this).find('.select2').select2({
                        dropdownParent: $(this),
                        width: '100%'
                    });
                });
            });

            function deleteData(id) {
                let url = "{{ route('admin.expense.type.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
