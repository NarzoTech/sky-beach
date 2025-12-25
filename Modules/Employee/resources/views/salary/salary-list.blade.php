@extends('admin.layouts.master')
@section('title')
    <title>{{ __('All Paid Salary List') }}</title>
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

            <div class="card mt-5">
                <div class="card-header">
                    <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                        <h4 class="section_title"> {{ __('All Paid Salary List') }}</h4>
                    </div>
                    <div class="btn-actions-pane-right actions-icon-btn">
                        <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                            Excel</button>
                        <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                            PDF</button>

                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive list_table">
                        <table style="width: 100%;" class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Sl') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    {{-- <th style="display: none;">Business Branch</th> --}}
                                    <th>{{ __('Note') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $index => $payment)
                                    <tr>
                                        <td>{{ $payments->firstItem() + $index }}</td>
                                        <td>{{ $payment->employee?->name }}</td>
                                        <td>{{ currency($payment->amount) }}</td>
                                        <td>{{ formatDate($payment->date) }}</td>
                                        <td>{{ $payment->note }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button id="btnGroupDrop{{ $payment->id }}" type="button"
                                                    class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu"
                                                    aria-labelledby="btnGroupDrop{{ $payment->id }}">

                                                    <a href="{{ route('admin.employee.salary.edit', $payment->id) }}"
                                                        class="dropdown-item">{{ __('Edit') }}</a>
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $payment->id }})">
                                                        {{ __('Delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <x-empty-table :name="__('Paid Salary List')" route="" create="no" :message="__('No data found!')"
                                        colspan="6"></x-empty-table>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if (request()->get('par-page') !== 'all')
                        <div class="float-right">
                            {{ $payments->onEachSide(0)->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.employee.salary.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
