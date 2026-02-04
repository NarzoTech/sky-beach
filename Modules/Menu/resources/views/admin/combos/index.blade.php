@extends('admin.layouts.master')
@section('title', __('Combo Deals'))
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
                <h4 class="section_title">{{ __('Combo Deals') }}</h4>
            </div>
            @adminCan('menu.combo.create')
                <div class="btn-actions-pane-right actions-icon-btn">
                    <a href="{{ route('admin.combo.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus"></i> {{ __('Add Combo') }}
                    </a>
                </div>
            @endadminCan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SL.') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Items') }}</th>
                            <th>{{ __('Original') }}</th>
                            <th>{{ __('Combo Price') }}</th>
                            <th>{{ __('Savings') }}</th>
                            <th>{{ __('Duration') }}</th>
                            <th>{{ __('Status') }}</th>
                            @if (checkAdminHasPermission('menu.combo.view') || checkAdminHasPermission('menu.combo.edit') || checkAdminHasPermission('menu.combo.delete'))
                                <th>{{ __('Action') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($combos as $combo)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ $combo->image_url }}" alt="{{ $combo->name }}"
                                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td>
                                    <strong>{{ $combo->name }}</strong>
                                    <br><small class="text-muted">{{ Str::limit($combo->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $combo->items->count() }} {{ __('items') }}</span>
                                </td>
                                <td>
                                    <span class="text-muted text-decoration-line-through">{{ number_format($combo->original_price, 2) }}</span>
                                </td>
                                <td>
                                    <strong class="text-success">{{ number_format($combo->combo_price, 2) }}</strong>
                                </td>
                                <td>
                                    @if ($combo->savings > 0)
                                        <span class="badge bg-success">{{ number_format($combo->savings_percentage, 0) }}% off</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($combo->start_date || $combo->end_date)
                                        <small>
                                            @if ($combo->start_date)
                                                {{ $combo->start_date->format('M d') }}
                                            @endif
                                            -
                                            @if ($combo->end_date)
                                                {{ $combo->end_date->format('M d') }}
                                            @endif
                                        </small>
                                    @else
                                        <small class="text-muted">{{ __('Always') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if ($combo->status)
                                        <span class="badge bg-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>
                                @if (checkAdminHasPermission('menu.combo.edit') || checkAdminHasPermission('menu.combo.delete'))
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $combo->id }}" type="button"
                                            class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $combo->id }}">
                                            @adminCan('menu.combo.view')
                                                <a href="{{ route('admin.combo.show', $combo->id) }}"
                                                    class="dropdown-item" data-bs-toggle="tooltip"
                                                    title="{{ __('View') }}">{{ __('View') }}</a>
                                            @endadminCan
                                            @adminCan('menu.combo.edit')
                                                <a href="{{ route('admin.combo.edit', $combo->id) }}"
                                                    class="dropdown-item" data-bs-toggle="tooltip"
                                                    title="{{ __('Edit') }}">{{ __('Edit') }}</a>
                                            @endadminCan
                                            @adminCan('menu.combo.delete')
                                                <a href="javascript:void(0)"
                                                    class="trigger--fire-modal-1 deleteForm dropdown-item"
                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                    data-url="{{ route('admin.combo.destroy', $combo->id) }}"
                                                    data-form="deleteForm">{{ __('Delete') }}</a>
                                            @endadminCan
                                        </div>
                                    </div>
                                </td>
                                @elseif (checkAdminHasPermission('menu.combo.view'))
                                <td>
                                    <a href="{{ route('admin.combo.show', $combo->id) }}"
                                        class="btn btn-sm btn-info" data-bs-toggle="tooltip"
                                        title="{{ __('View') }}">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <p class="text-muted">{{ __('No combo deals found') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $combos->onEachSide(0)->links() }}
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
        });
    </script>
@endpush
