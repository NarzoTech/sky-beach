@extends('admin.layouts.master')
@section('title', __('Menu Items'))
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form action="" method="GET">
                        <div class="row">
                            <div class="col-lg-2 col-md-6">
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
                                    <select name="category_id" class="form-control select2">
                                        <option value="">{{ __('All Categories') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <select name="availability" class="form-control">
                                        <option value="">{{ __('All Availability') }}</option>
                                        <option value="1" {{ request('availability') === '1' ? 'selected' : '' }}>{{ __('Available') }}</option>
                                        <option value="0" {{ request('availability') === '0' ? 'selected' : '' }}>{{ __('Unavailable') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group">
                                    <select name="par-page" class="form-control">
                                        <option value="">{{ __('Per Page') }}</option>
                                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>10</option>
                                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>100</option>
                                        <option value="all" {{ 'all' == request('par-page') ? 'selected' : '' }}>{{ __('All') }}</option>
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
                <h4 class="section_title">{{ __('Menu Items') }}</h4>
            </div>
            @adminCan('menu.item.create')
                <div class="btn-actions-pane-right actions-icon-btn">
                    <a href="{{ route('admin.menu-item.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> {{ __('Add Menu Item') }}
                    </a>
                </div>
            @endadminCan
        </div>
        <div class="card-body">
            @adminCan('menu.item.delete')
            <div class="alert alert-danger d-none justify-content-between delete-section danger-bg">
                <span><span class="number">0 </span> rows selected</span>
                <button class="btn btn-danger delete-button">Delete</button>
            </div>
            @endadminCan
            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            @adminCan('menu.item.delete')
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
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Variants') }}</th>
                            <th>{{ __('Available') }}</th>
                            <th>{{ __('Featured') }}</th>
                            <th>{{ __('Status') }}</th>
                            @if (checkAdminHasPermission('menu.item.view') || checkAdminHasPermission('menu.item.edit') || checkAdminHasPermission('menu.item.delete'))
                                <th>{{ __('Action') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                @adminCan('menu.item.delete')
                                <td>
                                    <div class="custom-checkbox custom-control">
                                        <input type="checkbox" data-checkboxes="checkgroup" class="custom-control-input"
                                            id="checkbox-{{ $item->id }}" name="select">
                                        <label for="checkbox-{{ $item->id }}" class="custom-control-label">&nbsp;</label>
                                    </div>
                                </td>
                                @endadminCan
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td>
                                    {{ $item->name }}
                                </td>
                                <td>{{ $item->category->name ?? 'N/A' }}</td>
                                <td>{{ currency($item->base_price) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $item->variants->count() }}</span>
                                </td>
                                <td>
                                    @if ($item->is_available)
                                        <span class="badge bg-success">{{ __('Yes') }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_featured)
                                        <span class="badge bg-primary">{{ __('Yes') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                @if (checkAdminHasPermission('menu.item.edit') || checkAdminHasPermission('menu.item.delete'))
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $item->id }}" type="button"
                                            class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $item->id }}">
                                            @adminCan('menu.item.view')
                                                <a href="{{ route('admin.menu-item.show', $item->id) }}"
                                                    class="dropdown-item">{{ __('View') }}</a>
                                            @endadminCan
                                            @adminCan('menu.item.edit')
                                                <a href="{{ route('admin.menu-item.edit', $item->id) }}"
                                                    class="dropdown-item">{{ __('Edit') }}</a>
                                                <a href="{{ route('admin.menu-item.variants', $item->id) }}"
                                                    class="dropdown-item">{{ __('Manage Variants') }}</a>
                                                <a href="{{ route('admin.menu-item.addons', $item->id) }}"
                                                    class="dropdown-item">{{ __('Manage Add-ons') }}</a>
                                                <a href="{{ route('admin.menu-item.recipe', $item->id) }}"
                                                    class="dropdown-item">{{ __('Manage Recipe') }}</a>
                                            @endadminCan
                                            @adminCan('menu.item.delete')
                                                <a href="javascript:void(0)"
                                                    class="trigger--fire-modal-1 deleteForm dropdown-item"
                                                    data-url="{{ route('admin.menu-item.destroy', $item->id) }}"
                                                    data-form="deleteForm">{{ __('Delete') }}</a>
                                            @endadminCan
                                        </div>
                                    </div>
                                </td>
                                @elseif (checkAdminHasPermission('menu.item.view'))
                                <td>
                                    <a href="{{ route('admin.menu-item.show', $item->id) }}"
                                        class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                        title="{{ __('View') }}">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $items->onEachSide(0)->links() }}
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

            $('#checkbox-all').on('click', function() {
                var check = $(this).prop('checked');
                $('input[name="select"]').prop('checked', check);
                updateDeleteSection();
            });

            $('input[name="select"]').on('click', function() {
                var total = $('input[name="select"]').length;
                var checked = $('input[name="select"]:checked').length;
                $('#checkbox-all').prop('checked', total == checked);
                updateDeleteSection();
            });

            function updateDeleteSection() {
                var count = $('input[name="select"]:checked').length;
                $('.number').text(count);
                if (count > 0) {
                    $('.delete-section').removeClass('d-none').addClass('d-flex');
                } else {
                    $('.delete-section').addClass('d-none').removeClass('d-flex');
                }
            }

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
                            url: "{{ route('admin.menu-item.deleteSelected') }}",
                            type: 'POST',
                            data: { _token: "{{ csrf_token() }}", ids: ids },
                            success: function(response) {
                                if (response.success) {
                                    toastr.success(response.message);
                                    setTimeout(() => location.reload(), 1000);
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
