@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Invoice') }}</title>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
@endpush

@section('content')
    <div class="main-content">
        <div class="container-fluid">
            <section class="page">
                <div class="row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-between">
                            <div class="flex-1 d-flex flex-column">
                                <span><strong>{{ __('Name') }}:</strong>&nbsp;{{ $ledger->expenseSupplier->name }}</span>
                                <span><strong>{{ __('Mobile') }}:</strong>&nbsp;{{ $ledger->expenseSupplier->phone }}</span>
                                <span><strong>{{ __('Email') }}:</strong>&nbsp;{{ $ledger->expenseSupplier->email }}</span>
                                <span><strong>{{ __('Address') }}:</strong>&nbsp;{{ $ledger->expenseSupplier->address }}</span>
                                <span><strong>{{ __('Paid By') }}:</strong>&nbsp;{{ $ledger->createdBy->name }}</span>
                            </div>
                            <div class="flex-1 d-flex flex-column">
                                <span><strong>{{ __('Date') }}:</strong>&nbsp;{{ formatDate($ledger->date) }}</span>
                                <span><strong>{{ __('Invoice No') }}:</strong>&nbsp;{{ $ledger->invoice_no }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered mt-4" cellspacing="0" width="100%"
                            style="margin-top: 0 !important">
                            <thead class="theme-primary text-white">
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Expense Invoice No') }}</th>
                                    <th class="text-right">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ledger->details as $details)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $details->invoice }}</td>
                                        <td class="text-right">{{ currency($details->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="print-btn d-print-none float-right">
                    <a onclick="window.print()" class="btn btn-primary waves-effect waves-light">
                        <i class="fa fa-print"></i> {{ __('Print') }}
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection
