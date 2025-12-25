@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Manage Roles') }}</title>
@endsection
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="section_title">{{ __('Manage Roles') }}</h4>
            <div>
                @adminCan('role.create')
                    <a href="{{ route('admin.role.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i>
                        {{ __('Add New') }}</a>
                @endadminCan
                @adminCan('role.assign')
                    <a href="{{ route('admin.role.assign') }}" class="btn btn-success"><i class="fa fa-sync"></i>
                        {{ __('Assign Role') }}</a>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Permission') }}</th>
                            @adminCan(['role.edit', 'role.delete'])
                                <th>{{ __('Action') }}</th>
                            @endadminCan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ ucwords($role->name) }}</td>
                                <td>
                                    {{ $role?->permissions?->count() ?? 0 }}
                                </td>
                                @adminCan(['role.edit', 'role.delete'])
                                    <td class="common_table">
                                        @adminCan('role.edit')
                                            <a href="{{ route('admin.role.edit', $role->id) }}" class="btn btn-primary"><i
                                                    class="fa fa-edit me-0" aria-hidden="true"></i></a>
                                        @endadminCan
                                        @adminCan('role.delete')
                                            <a href="javascript:;"class="btn btn-danger"
                                                onclick="deleteData({{ $role->id }})"><i class="fa fa-trash"
                                                    aria-hidden="true"></i></a>
                                        @endadminCan
                                    </td>
                                @endadminCan
                            </tr>
                        @empty
                            <x-empty-table name="Role" route="admin.role.create" create="no" colspan="4"
                                message="No data found!"></x-empty-table>
                        @endforelse
                    </tbody>
                </table>
                <div class="float-right">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        "use strict"

        function deleteData(id) {
            let url = "{{ route('admin.role.destroy', ':id') }}"
            url = url.replace(':id', id);
            $("#deleteForm").attr("action", url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush

@push('css')
    <style>
        .dd-custom-css {
            position: absolute;
            will-change: transform;
            top: 0px;
            left: 0px;
            transform: translate3d(0px, -131px, 0px);
        }

        .max-h-400 {
            min-height: 400px;
        }
    </style>
@endpush
