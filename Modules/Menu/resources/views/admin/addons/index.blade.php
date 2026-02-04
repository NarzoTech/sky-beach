@extends('admin.layouts.master')
@section('title', __('Menu Add-ons'))
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
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                            {{ __('Active') }}
                                        </option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                            {{ __('Inactive') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
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
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <select name="par-page" id="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>10</option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>100</option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>{{ __('All') }}</option>
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
                <h4 class="section_title">{{ __('Menu Add-ons') }}</h4>
            </div>
            @adminCan('menu.addon.create')
                <div class="btn-actions-pane-right actions-icon-btn">
                    <a href="{{ route('admin.menu-addon.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> {{ __('Add Add-on') }}
                    </a>
                </div>
            @endadminCan
        </div>
        <div class="card-body">
            @adminCan('menu.addon.delete')
            <div class="alert alert-danger d-none justify-content-between delete-section danger-bg">
                <span><span class="number">0 </span> rows selected</span>
                <button class="btn btn-danger delete-button">Delete</button>
            </div>
            @endadminCan
            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            @adminCan('menu.addon.delete')
                            <th>
                                <div class="custom-checkbox custom-control">
                                    <input type="checkbox" data-checkboxes="checkgroup" data-checkbox-role="dad"
                                        class="custom-control-input" id="checkbox-all">
                                    <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                </div>
                            </th>
                            @endadminCan
                            <th>{{ __('SL.') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th>{{ __('Status') }}</th>
                            @if (checkAdminHasPermission('menu.addon.edit') || checkAdminHasPermission('menu.addon.delete'))
                                <th>{{ __('Action') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($addons as $addon)
                            <tr>
                                @adminCan('menu.addon.delete')
                                <td>
                                    <div class="custom-checkbox custom-control">
                                        <input type="checkbox" data-checkboxes="checkgroup" class="custom-control-input"
                                            id="checkbox-{{ $addon->id }}" name="select">
                                        <label for="checkbox-{{ $addon->id }}" class="custom-control-label">&nbsp;</label>
                                    </div>
                                </td>
                                @endadminCan
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if ($addon->image)
                                        <img src="{{ asset($addon->image) }}" alt="{{ $addon->name }}"
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bx bx-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $addon->name }}</td>
                                <td>{{ Str::limit($addon->description, 50) }}</td>
                                <td><strong>{{ number_format($addon->price, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $addon->menu_items_count ?? $addon->menuItems->count() }} {{ __('items') }}</span>
                                </td>
                                <td>
                                    @if ($addon->status)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                @if (checkAdminHasPermission('menu.addon.edit') || checkAdminHasPermission('menu.addon.delete'))
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $addon->id }}" type="button"
                                            class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $addon->id }}">
                                            @adminCan('menu.addon.edit')
                                                <a href="{{ route('admin.menu-addon.edit', $addon->id) }}"
                                                    class="dropdown-item" data-bs-toggle="tooltip"
                                                    title="{{ __('Edit') }}">{{ __('Edit') }}</a>
                                            @endadminCan
                                            @adminCan('menu.addon.delete')
                                                <a href="javascript:void(0)"
                                                    class="trigger--fire-modal-1 deleteForm dropdown-item"
                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                    data-url="{{ route('admin.menu-addon.destroy', $addon->id) }}"
                                                    data-form="deleteForm">{{ __('Delete') }}</a>
                                            @endadminCan
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $addons->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            'use strict';
            $('.deleteForm').on('click', function() {
                var url = $(this).data('url');
                $('#deleteForm').attr('action', url);
                $('#deleteModal').modal('show');
            });

            // Check all checkboxes
            $('#checkbox-all').on('click', function() {
                var $this = $(this);
                var check = $this.prop('checked');
                $('input[name="select"]').each(function() {
                    $(this).prop('checked', check);
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

            // Delete all selected
            $('.delete-button').on('click', function() {
                var ids = [];
                $('input[name="select"]:checked').each(function() {
                    ids.push($(this).attr('id').split('-')[1]);
                });

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
                            url: "{{ route('admin.menu-addon.bulk-delete') }}",
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
    </script>
@endpush
