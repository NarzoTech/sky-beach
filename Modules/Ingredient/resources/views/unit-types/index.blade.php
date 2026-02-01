@extends('admin.layouts.master')

@section('title', __('Unit List'))

@section('content')
    <div class="row">
        <div class="col-xxl-3 col-lg-4">
            <div class="card mb-5">
                @adminCan('ingredient.unit.create')
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="section_title">{{ __('Create Unit Type') }}</h4>
                        <div>
                        </div>
                    </div>
                    <div class="card-body pb-0">
                        <form class="search_form" action="{{ route('admin.unit.store') }}" method="POST"
                            enctype="multipart/form-data" id="form">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="name" class="form-control" name="name" required>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <label>{{ __('Short Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="ShortName" class="form-control" name="ShortName" required>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="form-group">
                                        <label>{{ __('Base Unit') }}</label>
                                        <select name="base_unit" id="base_unit" class="form-control">
                                            <option value="">{{ __('Select Base Unit') }}</option>
                                            @foreach ($parentUnits as $parentUnit)
                                                <option value="{{ $parentUnit->id }}">{{ $parentUnit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-12 operator d-none">
                                    <div class="form-group">
                                        <label>{{ __('Operator') }}</label>
                                        <select name="operator" id="operator" class="form-control" required>
                                            <option value="*">{{ __('Multiply') }} (*)</option>
                                            <option value="/">{{ __('Divide') }} (/)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-12 operator_value d-none">
                                    <div class="form-group">
                                        <label>{{ __('Operator Value') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="operator_value" class="form-control" name="operator_value"
                                            value="1">
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <div class="form-group mb-1">
                                        <label>{{ __('Status') }} </label>
                                        <div class="d-flex flex-wrap gap-3 border rounded py-2 px-4">
                                            <div class="d-flex gap-2 align-items-center py-1">
                                                <input id="active" type="radio" name='status' value="1" checked />
                                                <label for="active" class="mb-0">{{ __('Active') }} </label>
                                            </div>
                                            <div class="d-flex gap-2 align-items-center py-1">
                                                <input id="inactive" type="radio" name='status' value="0" />
                                                <label for="inactive" class="mb-0">{{ __('Inactive') }} </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 mt-5">
                                    <div class="form-group mt-1">
                                        <x-admin.save-button :text="__('Save')" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endadminCan
            </div>
        </div>

        <div class="col-xxl-9 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-invoice" id="dataTable">
                            <thead>
                                <tr>
                                    <th>{{ __('SN') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Short Name') }}</th>
                                    <th>{{ __('Base Unit') }}</th>
                                    <th>{{ __('Operator') }}</th>
                                    <th>{{ __('Operator Value') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($units as $index => $unit)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $unit->name }}</td>
                                        <td>{{ $unit->ShortName }}</td>
                                        <td>{{ $unit->parent?->name }}</td>
                                        <td>{{ $unit->operator }}</td>
                                        <td>{{ $unit->operator_value }}</td>
                                        <td>
                                            @if ($unit->status == 1)
                                                <span class="badge badge-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (checkAdminHasPermission('ingredient.unit.edit') || checkAdminHasPermission('ingredient.unit.delete'))
                                                <div class="btn-group" role="group">
                                                    <button id="btnGroupDrop{{ $unit->id }}" type="button"
                                                        class="btn bg-label-primary dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false">
                                                        Action
                                                    </button>

                                                    <div class="dropdown-menu"
                                                        aria-labelledby="btnGroupDrop{{ $unit->id }}">
                                                        @adminCan('ingredient.unit.edit')
                                                            <a href="javascript:;"
                                                                data-href="{{ route('admin.unit.edit', $unit->id) }}"
                                                                class="dropdown-item edit-btn">{{ __('Edit') }}</a>
                                                        @endadminCan
                                                        @adminCan('ingredient.unit.delete')
                                                            <a href="javascript:;" class="dropdown-item"
                                                                onclick="deleteData({{ $unit->id }})">
                                                                {{ __('Delete') }}
                                                            </a>
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
                </div>
            </div>
        </div>
    </div>

    @include('components.admin.preloader')
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function(e) {
                $('.preloader_area').removeClass('d-none');
                const url = $(this).data('href');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        $('#name').val(response.name);
                        $('#ShortName').val(response.ShortName);
                        $('#base_unit').val(response.base_unit);
                        $('#operator').val(response.operator);
                        $('#operator_value').val(response.operator_value);

                        if (response.base_unit) {
                            $('.operator').removeClass('d-none');
                            $('.operator_value').removeClass('d-none');
                        } else {
                            $('.operator').addClass('d-none');
                            $('.operator_value').addClass('d-none');
                        }
                        $('input[name="status"][value="' + response.status + '"]').prop(
                            'checked', true);
                        let url = "{{ route('admin.unit.update', ':id') }}";
                        url = url.replace(':id', response.id);
                        $('#form').attr('action', url);
                        const unitId = "<input type='hidden' name='unit_id' value='" +
                            response.id + "'>";
                        const method = "<input type='hidden' name='_method' value='PUT'>";
                        $('#form').append(unitId);
                        $('#form').append(method);
                        $('.preloader_area').addClass('d-none');
                    },
                    error: function(error) {
                        console.log(error);
                        $('.preloader_area').addClass('d-none');
                    }
                });
            })

            $('#base_unit').on("change", function() {
                const baseUnit = $(this).val();
                if (baseUnit) {
                    $('.operator').removeClass('d-none');
                    $('.operator_value').removeClass('d-none');
                } else {
                    $('.operator').addClass('d-none');
                    $('.operator_value').addClass('d-none');
                }
            });
        });

        function deleteData(id) {
            let url = '{{ route('admin.unit.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteModal').modal('show');
        }
    </script>
@endpush
