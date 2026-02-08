@extends('admin.layouts.master')
@section('title', __('Invoice'))

@push('css')
    <link rel="stylesheet" href="{{ asset('/backend/css/invoice.css') }}">
@endpush

@section('content')
    <div class="main-content">
        <section class="page">
            <div class="row justify-content-between">
                <div class="col-5">
                    <div>
                        <div>
                            <p class="title">{{ ucfirst($setting->app_name) }}</p>
                            <div class="property">

                                <span class="value">
                                    <p>{{ $setting->address }}</p>
                                </span>
                            </div>

                            <div class="property">
                                <span class="key">Mobile:</span>
                                <span class="value">
                                    {{ $setting->mobile }}
                                </span>
                            </div>
                            <div class="property">
                                <span class="key">Email:</span>
                                <span class="value">{{ $setting->email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-5">
                    <div>
                        <p class="title">Purchase</p>
                        <div class="property">
                            <span class="key">Invoice No:</span>
                            <span class="value">{{ $purchase->invoice_number }}</span>
                        </div>
                        <div class="property">
                            <span class="key">Date:</span>
                            <span class="value">{{ formatDate($purchase->purchase_date) }}</span>
                        </div>
                        <p class="subtitle">Billing To</p>

                        <div class="property">
                            <span class="key">Name:</span>
                            <span class="value">{{ $purchase->supplier->name }}</span>
                        </div>
                        <div class="property">
                            <span class="key">Address:</span>
                            <span class="value">{{ $purchase->supplier->address }}</span>
                        </div>
                        <div class="property">
                            <span class="key">Mobile:</span>
                            <span class="value">{{ $purchase->supplier->phone }}</span>
                        </div>
                        <div class="property">
                            <span class="key">Email:</span>
                            <span class="value">{{ $purchase->supplier->email }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 6%;"><b>SL</b></th>
                            <th style="width: 35%;"><b>Item</b></th>
                            <th style="width: 23%;"><b>Quantity</b></th>
                            <th style="width: 18%;"><b>Rate</b></th>
                            <th style="width: 23%;"><b>Total</b></th>
                        </tr>
                    </thead>

                    @php
                        $unit = [];
                        $subTotal = 0;
                    @endphp
                    <tbody>
                        @foreach ($purchase->purchaseDetails as $index => $details)
                            <tr>
                                <td>
                                    {{ $index + 1 }}
                                </td>
                                <td>
                                    {{ $details->ingredient->name ?? '-' }}
                                    @if($details->ingredient?->sku)({{ $details->ingredient->sku }})@endif
                                </td>
                                <td class="qty">
                                    @php
                                        $unitName = $details->unit->name ?? $details->ingredient?->unit?->name ?? '';
                                        $unitQty = isset($unit[$unitName]) ? $unit[$unitName] : 0;
                                        $newQty = $details->quantity + $unitQty;
                                        $unit[$unitName] = $newQty;

                                        $subTotal += $details->sub_total;
                                    @endphp
                                    {{ $details->quantity }} {{ $unitName }}
                                </td>
                                <td>
                                    {{ number_format($details->purchase_price, 2) }}
                                </td>
                                <td>
                                    {{ number_format($details->sub_total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="qty">
                                @foreach ($unit as $key => $value)
                                    {{ $value }} {{ $key }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <table class="summary-table invoice-summary-table">
                    <tbody>
                        <tr>
                            <td>
                                <b>Subtotal:</b>
                            </td>
                            <td>
                                <b>{{ currency($subTotal) }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Total:</b>
                            </td>
                            <td>
                                <b>{{ currency($purchase->total_amount) }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Paid:</td>
                            <td>
                                {{ currency($purchase->paid_amount) }}</td>
                        </tr>
                        <tr>
                            <td>
                                Due:
                            </td>
                            <td>
                                {{ currency($purchase->due_amount) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-3 payment-details">
                    <div style=" width: 100%">
                        <h6 class="mb-2"><b>Payment Details</b></h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th><b>Sl</b></th>
                                    <th><b>Payment Method</b></th>
                                    <th><b>Payment By</b></th>
                                    <th><b>Amount</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase->payments as $index => $payment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ ucfirst($payment->account->account_type ?? '-') }}</td>
                                        <td>
                                            {{ $payment->account->bank_account_name ?? '-' }}
                                        </td>
                                        <td>{{ currency($payment->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between" style="margin-top: 150px">
                    <div>
                        <p class="signature">Received By</p>
                    </div>
                    <div>
                    </div>
                    <div>
                        <p class="signature">Authorised By</p>
                    </div>
                </div>
            </div>
            <div class="print-btn pos-share-btns d-print-none">
                <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light">
                    <i class="fa fa-print me-2"></i> Print
                </a>
            </div>
        </section>
    </div>
@endsection
