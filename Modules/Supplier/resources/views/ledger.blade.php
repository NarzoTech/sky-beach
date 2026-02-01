@extends('admin.layouts.master')
@section('title', $title)

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
                                        class="form-control" placeholder="Search...">
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
                                    <button type="button" class="btn bg-danger form-reset">Reset</button>
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
        <div class="card-header-tab card-header">
            <div class="card-header-title font-size-lg text-capitalize font-weight-normal">
                <h4 class="section_title">{{ $title }}</h4>
            </div>
            <div class="btn-actions-pane-right actions-icon-btn">
                <button type="button" class="btn btn-primary export"><i class="fa fa-file-excel"></i>
                    Excel</button>
                <button type="button" class="btn btn-success export-pdf"><i class="fa fa-file-pdf"></i>
                    PDF</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table style="width: 100%;" class="table">
                    <thead>
                        <tr>

                            <th title="Sl">{{ __('Sl') }}</th>
                            <th title="Invoice No">{{ __('Invoice No') }}</th>
                            @if ($title == 'Supplier Ledger')
                                <th title="Memo No">{{ __('Memo No') }}</th>
                            @endif
                            <th title="Date">{{ __('Date') }}</th>
                            <th title="Note">{{ __('Description') }}</th>
                            <th title="Invoice No">
                                @if ($title == 'Supplier Ledger')
                                    {{ __('Paid') }}({{ __('CREDIT') }})
                                @else
                                    {{ __('Received') }}({{ __('DEBIT') }})
                                @endif
                            </th>
                            <th title="Amount">
                                @if ($title == 'Supplier Ledger')
                                    {{ __('Product') }}({{ __('DEBIT') }})
                                @else
                                    {{ __('Sales') }}({{ __('CREDIT') }})
                                @endif
                            </th>
                            <th title="Due" class="text-end">{{ __('Balance') }} @if ($title == 'Supplier Ledger')
                                    ({{ __('DUE') }})
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $opening = 0;
                            $credit = 0;
                            $debit = 0;
                        @endphp
                        <tr>
                            <td colspan="{{ $title == 'Supplier Ledger' ? 6 : 5 }}" class="text-center fw-bold">
                                {{ __('Opening Balance') }}</td>
                            <td></td>
                            <td colspan="1" class="text-end fw-bold">{{ currency($opening) }}</td>
                        </tr>

                        @foreach ($ledgers as $index => $ledger)
                            @php
                                $opening += $ledger->due_amount;
                                $credit += $ledger->amount;
                                $debit += $ledger->total_amount;
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><a href="{{ $ledger->invoice_url }}">{{ $ledger->invoice_no }}</a></td>
                                @if ($title == 'Supplier Ledger')
                                    <td>{{ $ledger->purchase?->memo_no }}</td>
                                @endif
                                <td>{{ formatDate($ledger->date) }}</td>
                                <td class="text-capitalize">{{ $ledger->invoice_type }}</td>
                                <td>{{ currency($ledger->amount) }}</td>
                                <td>{{ currency($ledger->total_amount) }}</td>
                                <td class="text-end">{{ currency($opening) }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td colspan="{{ $title == 'Supplier Ledger' ? 5 : 4 }}" class="text-center fw-bold">
                                {{ __('Total') }}
                            </td>
                            <td colspan="1" class="fw-bold">{{ currency($credit) }}</td>
                            <td colspan="1" class="fw-bold">{{ currency($debit) }}</td>
                            <td class="text-end fw-bold">{{ currency($opening) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all')
                <div class="float-right">
                    {{ $ledgers->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
