@extends('admin.layouts.pdf-layout')

@section('title', __('Quotation List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Quotation Date'), __('Quotation No'), __('Customer'), __('Total Amount')];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($quotations as $index => $quotation)
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ formatDate($quotation->date) }}</td>
                    <td>{{ $quotation->quotation_no }}</td>
                    <td>{{ $quotation->customer->name }}</td>
                    <td>{{ $quotation->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
