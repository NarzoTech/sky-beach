@extends('admin.layouts.master')
@section('title', __('Expense Supplier Due Pay History'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body pb-0">
                    <form class="search_form" action="" method="GET">
                        <div class="row">
                            <div class="col-xxl-3 col-md-6 col-lg-4">
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
                                            {{ __('ASC') }}</option>
                                        <option value="desc" {{ request('order_by') == 'desc' ? 'selected' : '' }}>
                                            {{ __('DESC') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
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
                            <div class="col-xxl-3 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <div class="input-group input-daterange" id="bs-datepicker-daterange">
                                        <input type="text" id="dateRangePicker" placeholder="From Date"
                                            class="form-control datepicker" name="from_date"
                                            value="{{ request()->get('from_date') }}" autocomplete="off">
                                        <span class="input-group-text">to</span>
                                        <input type="text" placeholder="To Date" class="form-control datepicker"
                                            name="to_date" value="{{ request()->get('to_date') }}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <button type="button" class="btn bg-danger form-reset">{{ __('Reset') }}</button>
                                    <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-5 mb-5">
        <div class="card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ __('Expense Supplier Due Pay History') }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn bg-label-warning export-pdf"><i class="fa fa-file-pdf"></i>
                    {{ __('PDF') }}</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive list_table">
                <table style="width: 100%;" class="table mb-3">
                    <thead>
                        <tr>
                            <th>{{ __('SN') }}</th>
                            <th>{{ __('Invoice') }}</th>
                            <th>{{ __('Supplier Name') }}</th>
                            <th>{{ __('Expense ID') }}</th>
                            <th>{{ __('Payment Date') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Note') }}</th>
                            <th>{{ __('Memo') }}</th>
                            <th>{{ __('Created By') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $index => $payment)
                            <tr>
                                <td>{{ $payments->firstItem() + $index }}</td>
                                <td>{{ $payment->invoice }}</td>
                                <td>{{ $payment->expenseSupplier->name }}</td>
                                <td>EXP-{{ $payment->expense_id }}</td>
                                <td>{{ formatDate($payment->payment_date) }}</td>
                                <td>{{ currency($payment->amount) }}</td>
                                <td>{{ $payment->note }}</td>
                                <td>{{ $payment->memo }}</td>
                                <td>{{ $payment->createdBy->name }}</td>
                                <td>
                                    @adminCan('expense_supplier.due_pay')
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="deletePayment({{ $payment->id }})">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endadminCan
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" class="text-center"><b>{{ __('Total') }}</b></td>
                            <td colspan="5"><b>{{ currency($data['total']) }}</b></td>
                        </tr>
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
@endsection

@push('js')
    <script>
        $('.export-pdf').on('click', function() {
            var fullUrl = window.location.href;
            if (fullUrl.includes('?')) {
                fullUrl += '&export_pdf=true';
            } else {
                fullUrl += '?export_pdf=true';
            }
            window.location.href = fullUrl;
        })

        function deletePayment(id) {
            if (confirm('Are you sure you want to delete this payment?')) {
                let url = "{{ route('admin.expense-suppliers.due-pay-delete', ':id') }}";
                url = url.replace(':id', id);

                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                }).then(response => {
                    window.location.reload();
                });
            }
        }
    </script>
@endpush
