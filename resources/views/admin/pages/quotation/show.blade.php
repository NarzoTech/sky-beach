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
                <div class="row justify-content-between">
                    <div class="col-5">
                        <div>
                            <div>
                                <p class="title">{{ $setting->app_name }}</p>
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
                            <div class="property">
                                <span class="key">Quotation By:</span>
                                <span class="value">{{ $quotation->createdBy->name }}</span>
                            </div>

                        </div>
                    </div>
                    <div class="col-5">
                        <div>
                            <p class="title" style="font-weight: 600;">Invoice</p>
                            <div class="property">
                                <span class="value">
                                    Quotation No:
                                </span>
                                <span class="value" style="font-weight: bold">
                                    {{ $quotation->quotation_no }}
                                </span>
                            </div>
                            <div class="property">
                                <span class="value">
                                    Date:
                                </span>
                                <span class="value">
                                    {{ formatDate($quotation->date) }}
                                </span>
                            </div>

                            <p class="billing-badge">Billing To</p>
                            <div class="property">
                                <span class="key">
                                    Name:
                                </span>
                                <span class="value">
                                    {{ $quotation->customer->name ?? 'Guest' }}
                                </span>
                            </div>


                            <div class="property">
                                <span class="key">
                                    Mobile:
                                </span>
                                <span class="value">
                                    {{ $quotation->customer->phone ?? '' }}
                                </span>
                            </div>

                            <div class="property">
                                <span class="key">
                                    Email:
                                </span>
                                <span class="value">
                                    {{ $quotation->customer->email ?? '' }}
                                </span>
                            </div>

                            <div class="property">
                                <span class="key">
                                    Address:
                                </span>
                                <span class="value">
                                    {{ $quotation->customer->address ?? '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%; border-left: none !important; border-right: none !important;"
                                    class="text-center">
                                    SL.
                                </th>
                                <th style="width: 40%; border-left: none !important; border-right: none !important; padding-left: 3px;"
                                    class="text-left">
                                    Item
                                </th>
                                <th style="width: 15%; border-left: none !important; border-right: none !important;"
                                    class="text-left">
                                    Quantity
                                </th>
                                <th style="width: 20%; border-left: none !important; border-right: none !important; text-align:center"
                                    class="text-center">
                                    Rate
                                </th>
                                <th style="width: 20%; border-left: none !important; border-right: none !important;"
                                    class="text-right pr-2">
                                    Total
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quotation->details as $index => $details)
                                <tr>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-left">
                                        {{ $details->description ?? ($details->ingredient->name ?? '') }}
                                    </td>

                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-center qty">
                                        {{ $details->quantity }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-right pr-2">
                                        {{ currency_icon() }} {{ $details->price }}
                                    </td>
                                    <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                                        class="text-right pr-2">
                                        {{ currency_icon() }} {{ $details->sub_total }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    <div class="row">
                        <div class="col-6">
                            <div class="invoice-watermark">
                            </div>
                        </div>
                        <div class="col-6">
                            <table class="summary-table" style="margin-bottom: 10px">
                                <tbody>
                                    <tr>
                                        <td colspan="5" style="border: none !important">
                                        </td>
                                        <td class="text-right pe-5"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            Subtotal :
                                        </td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            {{ currency_icon() }}
                                            {{ $quotation->subtotal }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" style="border: none !important"></td>
                                        <td class="text-right pe-5"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            Discount:</td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            {{ $quotation->discount }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="border: none !important"></td>
                                        <td class="text-right pe-5"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            VAT:</td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            {{ $quotation->vat }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="border: none !important">
                                        </td>
                                        <td class="text-right pe-5"
                                            style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important">
                                        </td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="border: none !important"></td>
                                        <td class="text-right pe-5"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            Total:
                                        </td>
                                        <td class="text-right pr-2"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            {{ currency_icon() }}
                                            {{ $quotation->total }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="mt-3 payment-details">
                        <span class="block bold" style="font-size: 12px">
                            <b>
                                <span style="font-weight: bold; letter-spacing: 0.1px; font-size: 13px;">
                                    In Words:
                                </span>
                                {{ numberToWord($quotation->total) }} {{ currency_icon() }}
                                Only
                            </b>
                        </span>
                    </div>
                    @if($quotation->note)
                    <div class="mt-3">
                        <span class="block bold" style="font-size: 12px">
                            <b>Notes:</b>
                        </span>
                        <p style="font-size: 11px; margin-top: 5px;">{{ $quotation->note }}</p>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between" style="margin-top: 80px">
                        <div>
                            <p class="signature">
                                Received By
                            </p>
                        </div>
                        <div>
                        </div>
                        <div>
                            <p class="signature">
                                Authorised By
                            </p>
                        </div>
                    </div>
                </div>
                <div class="print-btn pos-share-btns d-print-none">
                    <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light">
                        <i class="fa fa-print"></i> Print
                    </a>
                </div>
            </section>
        </div>
    </div>
@endsection
