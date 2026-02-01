@extends('admin.layouts.master')
@section('title', __('Purchase List'))


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group search-wrapper">
                                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                        class="form-control" placeholder="Search..." autocomplete="off">
                                    <button type="submit">
                                        <i class='bx bx-search'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
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
                            <div class="col-xxl-2 col-md-6 col-lg-4">
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
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <select class="form-control select2" name="product_id">
                                        <option value="" selected disabled>{{ __('Product') }}
                                        </option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                                ({{ $product->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn bg-primary">{{ __('Search') }}</button>
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
                <h4 class="section_title"> {{ __('Purchase List') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                @adminCan('purchase.create')
                    <a href="{{ route('admin.purchase.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i>
                        {{ __('Add Purchase') }}</a>
                @endadminCan
                @adminCan('purchase.excel.download')
                    <button type="button" class="btn bg-label-success export"><i class="fa fa-file-excel"></i>
                        {{ __('Excel') }}</button>
                @endadminCan
                @adminCan('purchase.pdf.download')
                    <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                        {{ __('PDF') }}</button>
                @endadminCan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Invoice Number') }}</th>
                            <th>{{ __('Supplier Company') }}</th>
                            <th>{{ __('Supplier Name') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Total Pay') }}</th>
                            <th>{{ __('Total Due') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchases as $index => $purchase)
                            <tr>
                                <td>{{ request()->get('par-page') === 'all' ? $index + 1 : $purchases->firstItem() + $index }}</td>
                                <td>{{ formatDate($purchase->purchase_date) }}</td>
                                <td>{{ $purchase->invoice_number }}</td>
                                <td>{{ $purchase->supplier?->company }}</td>
                                <td>{{ $purchase->supplier?->name }}</td>
                                <td>{{ currency($purchase->total_amount) }}</td>
                                <td>{{ currency($purchase->paid_amount) }}</td>
                                <td>{{ currency($purchase->due_amount) }}</td>
                                <td>
                                    @if (checkAdminHasPermission('purchase.view') ||
                                            checkAdminHasPermission('purchase.edit') ||
                                            checkAdminHasPermission('purchase.delete') ||
                                            checkAdminHasPermission('purchase.invoice') ||
                                            checkAdminHasPermission('purchase.return'))
                                        <div class="btn-group" role="group">
                                            <button id="btnGroupDrop{{ $purchase->id }}" type="button"
                                                class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                {{ __('Action') }}
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop{{ $purchase->id }}">
                                                @adminCan('purchase.view')
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"
                                                        data-bs-target="#showCustomer{{ $purchase->id }}">{{ __('Show') }}</a>
                                                @endadminCan
                                                @adminCan('purchase.invoice')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.purchase.invoice', $purchase->id) }}">{{ __('Invoice') }}</a>
                                                @endadminCan
                                                @adminCan('purchase.edit')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.purchase.edit', $purchase->id) }}">{{ __('Edit') }}</a>
                                                @endadminCan
                                                @adminCan('purchase.return.view')
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.purchase.return', $purchase->id) }}">{{ __('Purchase Return') }}</a>
                                                @endadminCan
                                                @adminCan('purchase.delete')
                                                    <a href="javascript:;" class="dropdown-item"
                                                        onclick="deleteData({{ $purchase->id }})">
                                                        {{ __('Delete') }}</a>
                                                @endadminCan
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="5" class="text-center">
                                <b> {{ __('Total') }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['total_amount']) }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['paid_amount']) }}</b>
                            </td>
                            <td colspan="1">
                                <b>{{ currency($data['due_amount']) }}</b>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $purchases->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Show customer --}}
    @foreach ($purchases as $index => $purchase)
        <div class="modal fade" id="showCustomer{{ $purchase->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{ __('Customer') }}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body py-0">
                        <div class="row">
                            {{-- table --}}
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <td>{{ $purchase->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Phone') }}</th>
                                            <td>{{ $purchase->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Email') }}</th>
                                            <td>{{ $purchase->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('City') }}</th>
                                            <td>{{ $purchase->city }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Tax Number') }}</th>
                                            <td>{{ $purchase->tax_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Status') }}</th>
                                            <td>{{ $purchase->status == 1 ? 'Active' : 'Inactive' }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Address') }}</th>
                                            <td>{{ $purchase->address }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach


    @push('js')
        <script>
            function deleteData(id) {
                let url = "{{ route('admin.purchase.destroy', ':id') }}"
                url = url.replace(':id', id);
                $("#deleteForm").attr("action", url);
                $('#deleteModal').modal('show');
            }
        </script>
    @endpush
@endsection
