@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Attribute List') }}</title>
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

    <div class="card mt-5 mb-5">
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title"> {{ __('Attribute List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <a href="{{ route('admin.attribute.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i>
                    {{ __('Add Attribute') }}</a>
            </div>
        </div>
        <div class="card-body common_table">
            <div class="alert alert-danger d-none justify-content-between delete-section danger-bg">
                <span><span class="number">0 </span> rows selected</span>
                <button class="btn btn-danger delete-button">Delete</button>
            </div>
            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SL.') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Values') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attributes as $attribute)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $attribute->name }}</td>
                                <td>
                                    @foreach ($attribute->values as $val)
                                        {{ $val->name }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button id="btnGroupDrop{{ $attribute->id }}" type="button"
                                            class="btn bg-label-primary dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $attribute->id }}">
                                            <a href="{{ route('admin.attribute.edit', $attribute->id) }}"
                                                class="dropdown-item" data-bs-toggle="tooltip"
                                                title="{{ __('Edit') }}">{{ __('Edit') }}</a>
                                            <a href="javascript:void(0)"
                                                class="dropdown-item trigger--fire-modal-1 deleteForm"
                                                data-bs-toggle="modal" title="{{ __('Delete') }}"
                                                data-url="{{ route('admin.attribute.destroy', $attribute->id) }}"
                                                data-form="deleteForm"
                                                data-id="{{ $attribute->id }}">{{ __('Delete') }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <x-empty-table :name="__('Attribute')" route="admin.attribute.create" create="no" :message="__('No data found!')"
                                    colspan="4"></x-empty-table>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if (request()->get('par-page') !== 'all')
                    <div class="float-right">
                        {{ $attributes->onEachSide(0)->links() }}
                    </div>
                @endif
            </div>
        </div>



        <div class="modal fade" tabindex="-1" role="dialog" id="confirm-availibility">
            <div class="modal-dialog" role="document">
                <form action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="attribute_id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-danger">
                                {{ __('Attribute has values. Sure to Delete?') }}
                            </p>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-success">{{ __('Yes, Delete') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @include('components.admin.preloader')
    @endsection


    @push('js')
        <script>
            $(document).ready(function() {
                'use strict';
                $('.deleteForm').on('click', function() {
                    $('.preloader_area').removeClass('d-none')
                    const id = $(this).data('id');

                    const route = "{{ route('admin.attribute.destroy', '') }}/" + id;
                    $.ajax({
                        url: "{{ route('admin.attribute.has-value') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            attribute_id: id
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                $('#confirm-availibility').modal('show');
                                $('#confirm-availibility').find('form').attr('action', route);

                                $('[name="attribute_id"]').val(id);
                            } else {
                                $('#deleteForm').attr('action', route);
                            }

                            $('.preloader_area').addClass('d-none')
                        }
                    });
                });
            });
        </script>
    @endpush
