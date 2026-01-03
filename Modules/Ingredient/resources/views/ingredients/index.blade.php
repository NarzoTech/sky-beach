@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Ingredient List') }}</title>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-2 col-md-3">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-3">
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
                            <div class="col-xxl-2 col-md-3">
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
                            <div class="col-xxl-2 col-md-3">
                                <div class="form-group">
                                    <select name="brand_id" id="brand_id" class="form-control select2">
                                        <option value="" selected disabled>{{ __('Brand') }}</option>
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-3">
                                <div class="form-group">
                                    <select name="category_id" id="categories" class="form-control select2">
                                        <option value="" selected disabled>{{ __('Categories') }}
                                        </option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
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
                <h4 class="section_title"> {{ __('Ingredient List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @adminCan('ingredient.create')
                    <a href="{{ route('admin.ingredient.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i>
                        {{ __('Add Ingredient') }}</a>
                @endadminCan
                @adminCan('ingredient.bulk.import')
                    <a href="{{ route('admin.ingredient.import') }}" class="btn btn-primary"><i class="fa fa-upload"></i>
                        {{ __('Import Ingredients') }}</a>
                @endadminCan

                <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                    {{ __('Excel') }}</button>

                <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                    {{ __('PDF') }}</button>

            </div>
        </div>
        <div class="card-body">
            <div
                class="alert alert-danger d-none justify-content-between delete-section danger-bg flex-wrap align-items-center">
                <span>
                    <span class="number">0 </span> rows selected</span>
                @adminCan('ingredient.delete')
                    <button class="btn btn-danger delete-button">Delete</button>
                @endadminCan
            </div>
            <div class="table-responsive">
                <table style="width: 100%;" class="table product_list_table">
                    <thead>
                        <tr>
                            <th>
                                <div class="custom-checkbox custom-control">
                                    <input type="checkbox" data-checkboxes="checkgroup" data-checkbox-role="dad"
                                        class="custom-control-input" id="checkbox-all">
                                    <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                </div>
                            </th>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Photo') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Stock Qty') }}</th>
                            <th>{{ __('Purchase Price') }}</th>
                            <th>{{ __('Cost/Unit') }}</th>
                            <th>{{ __('Brand') }}</th>
                            <th>{{ __('Category') }}</th>
                            @adminCan('ingredient.status')
                                <th>{{ __('Status') }}</th>
                            @endadminCan
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $start = checkPaginate($ingredients) ? $ingredients->firstItem() : 1;
                        @endphp
                        @foreach ($ingredients as $index => $ingredient)
                            <tr>
                                <td>
                                    <div class="custom-checkbox custom-control">
                                        <input type="checkbox" data-checkboxes="checkgroup" class="custom-control-input"
                                            id="checkbox-{{ $ingredient->id }}" name="select">
                                    </div>
                                </td>
                                <td>{{ $start + $index }}</td>
                                <td> <img class="rounded-circle" src="{{ $ingredient->singleImage }}"></td>
                                <td>{{ $ingredient->name }} </td>
                                <td>{{ $ingredient->sku }}</td>
                                <td>{{ $ingredient->stock }}{{ $ingredient->purchaseUnit->ShortName ?? '' }}</td>
                                <td>{{ currency($ingredient->purchase_price ?? 0) }}</td>
                                <td>{{ currency($ingredient->consumption_unit_cost ?? 0) }}</td>
                                <td>{{ $ingredient->brand->name }}</td>
                                <td>{{ $ingredient->category->name }}</td>
                                @adminCan('ingredient.status')
                                    <td>
                                        @if ($ingredient->status == 1)
                                            <a href="javascript:;" onclick="status({{ $ingredient->id }})">
                                                <input id="status_toggle" type="checkbox" checked data-bs-toggle="toggle"
                                                    data-on="{{ __('Active') }}" data-off="{{ __('InActive') }}"
                                                    data-onstyle="success" data-offstyle="danger">
                                            </a>
                                        @else
                                            <a href="javascript:;" onclick="status({{ $ingredient->id }})">
                                                <input id="status_toggle" type="checkbox" data-bs-toggle="toggle"
                                                    data-on="{{ __('Active') }}" data-off="{{ __('InActive') }}"
                                                    data-onstyle="success" data-offstyle="danger">
                                            </a>
                                        @endif
                                    </td>
                                @endadminCan
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton{{ $ingredient->id }}" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>

                                        <div class="dropdown-menu" x-placement="top-start"
                                            style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -131px, 0px);">
                                            <a href="javascript:;" class="dropdown-item ingredientView"
                                                data-id="{{ $ingredient->id }}">
                                                {{ __('View') }}</a>

                                            <a href="{{ route('admin.ingredient.show', $ingredient->id) }}"
                                                class="dropdown-item"></i>
                                                {{ __('Details') }}</a>

                                            @adminCan('ingredient.edit')
                                                <a href="{{ route('admin.ingredient.edit', $ingredient->id) }}"
                                                    class="dropdown-item">

                                                    {{ __('Edit') }}</a>
                                            @endadminCan
                                            @adminCan('ingredient.status')
                                                <a class="dropdown-item" href="javascript:;"
                                                    onclick="status('{{ $ingredient->id }}')"
                                                    data-status="{{ $ingredient->id }}">
                                                    {{ $ingredient->status == 1 ? 'Disable' : 'Enable' }}
                                                </a>
                                            @endadminCan
                                            @adminCan('ingredient.delete')
                                                <a class="dropdown-item" href="javascript:;"
                                                    @if ($ingredient->orders->count() > 0) data-bs-target="#canNotDeleteModal"
                                            @else onclick="deleteData({{ $ingredient->id }})" @endif>{{ __('Delete') }}</a>
                                            @endadminCan
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right mt-5">
                    {{ $ingredients->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="canNotDeleteModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    {{ __('You can not delete this ingredient. Because there are one or more order has been created with this ingredient.') }}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ingredientView" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        $(document).ready(function() {
            'use strict';
            $('.ingredientView').on('click', function() {
                var id = $(this).data('id');
                let url = '{{ route('admin.ingredient.view', ':id') }}';
                url = url.replace(':id', id);
                $.ajax({
                    type: "GET",
                    url,
                    success: function(response) {
                        $('#ingredientView .modal-content').html(response);
                        $('#ingredientView').modal('show');
                    }
                });
            })

            $('.export').on('click', function() {
                // get full url including query string
                var fullUrl = window.location.href;
                if (fullUrl.includes('?')) {
                    fullUrl += '&export=true';
                } else {
                    fullUrl += '?export=true';
                }

                window.location.href = fullUrl;
            })


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
                            url: "{{ route('admin.ingredient.bulk.delete') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: ids
                            },
                            beforeSend: function() {
                                // disable button
                                $('.delete-button').prop('disabled', true);
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
            var id = id;
            var url = '{{ route('admin.ingredient.destroy', ':id') }}';
            url = url.replace(':id', id);
            $("#deleteForm").attr('action', url);
            $('#deleteModal').modal('show');
        }

        function status(id) {
            handleStatus("{{ route('admin.ingredient.status', '') }}/" + id)

            let status = $('[data-status=' + id + ']').text()
            // remove whitespaces using regex
            status = status.replaceAll(/\s/g, '');
            $('[data-status=' + id + ']').text(status != 'Disable' ? 'Disable' :
                'Enable')
        }
    </script>
@endpush
