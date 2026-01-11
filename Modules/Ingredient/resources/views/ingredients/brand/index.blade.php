@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Brand List') }}</title>
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
                <h4 class="section_title"> {{ __('Brand List') }}</h4>
            </div>
            @adminCan('ingredient.brand.create')
                <div class="btn-actions-pane-right actions-icon-btn">
                    <a href="{{ route('admin.brand.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i>
                        {{ __('Add Brand') }}</a>
                </div>
            @endadminCan
        </div>
        <div class="card-body">
            <div class="alert alert-danger d-none justify-content-between delete-section danger-bg">
                <span><span class="number">0 </span> rows selected</span>
                @adminCan('ingredient.brand.delete')
                    <button class="btn btn-danger delete-button">Delete</button>
                @endadminCan
            </div>
            <div class="table-responsive">
                <table style="width: 100%;" class="table mb-5">
                    <thead>
                        <tr>
                            <th>
                                <div class="custom-checkbox custom-control">
                                    <input type="checkbox" data-checkboxes="checkgroup" data-checkbox-role="dad"
                                        class="custom-control-input" id="checkbox-all">
                                    <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                </div>
                            </th>
                            <th>{{ __('SL.') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th class="text-left">{{ __('Name') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($brands as $index => $brand)
                            <tr>
                                <td>
                                    <div class="custom-checkbox custom-control">
                                        <input type="checkbox" data-checkboxes="checkgroup" class="custom-control-input"
                                            id="checkbox-{{ $brand->id }}" name="select">
                                        <label for="checkbox-{{ $brand->id }}"
                                            class="custom-control-label">&nbsp;</label>
                                    </div>
                                </td>
                                <td>{{ $index + $brands->firstItem() }}</td>
                                <td><img src="{{ $brand->image_url }}" alt="" class="img-fluid"
                                        style="width: 80px"></td>
                                <td class="text-left">{{ $brand->name }}</td>
                                <td>
                                    @if (checkAdminHasPermission('ingredient.brand.edit') || checkAdminHasPermission('ingredient.brand.delete'))
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop{{ $brand->id }}" type="button"
                                                class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $brand->id }}">
                                                @adminCan('ingredient.brand.edit')
                                                    <a href="{{ route('admin.brand.edit', ['brand' => $brand->id, 'lang_code' => getSessionLanguage()]) }}"
                                                        class="dropdown-item" data-bs-toggle="tooltip"
                                                        title="Edit">{{ __('Edit') }}</a>
                                                @endadminCan
                                                @adminCan('ingredient.brand.delete')
                                                    <a href="javascript:;" data-bs-target="#deleteModal" data-bs-toggle="modal"
                                                        class="dropdown-item"
                                                        onclick="deleteData({{ $brand->id }})">{{ __('Delete') }}</a>
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
                    {{ $brands->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection


@push('js')
    <script>
        'use strict';
        $(document).ready(function() {
            //check all checkboxes
            $('#checkbox-all').on('click', function() {
                var $this = $(this);
                var check = $this.prop('checked');
                $('input[name="select"]').each(function() {
                    $(this).prop('checked', check);

                    // change the count number
                    if (check) {
                        $('.number').text($('input[name="select"]').length);
                        $('.delete-section').removeClass('d-none');
                        $('.delete-section').addClass('d-flex');

                    } else {
                        $('.number').text(0);
                        $('.delete-section').addClass('d-none');
                        $('.delete-section').removeClass('d-flex');
                    }
                });
            });

            $('input[name="select"]').on('click', function() {
                var total = $('input[name="select"]').length;
                var number = $('input[name="select"]:checked').length;
                if (total == number) {
                    $('#checkbox-all').prop('checked', true);
                } else {
                    $('#checkbox-all').prop('checked', false);
                }
                $('.number').text(number);

                if (number > 0) {
                    $('.delete-section').removeClass('d-none');
                    $('.delete-section').addClass('d-flex');
                } else {
                    $('.delete-section').addClass('d-none');
                    $('.delete-section').removeClass('d-flex');
                }
            });

            // delete all selected
            $('.delete-button').on('click', function() {
                var ids = [];
                $('input[name="select"]:checked').each(function() {
                    ids.push($(this).attr('id').split('-')[1]);
                });

                // fire swal
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this data!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('admin.brand.deleteSelected') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: ids
                            },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                } else {
                                    toastr.error(response.message);
                                }
                            }
                        });
                    }
                });
            });
        });

        function deleteData(id) {
            $("#deleteForm").attr("action", '{{ route('admin.brand.destroy', '') }}' + "/" + id)
        }
    </script>
@endpush
