@extends('admin.layouts.pdf-layout')

@section('title', __('Due Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Date'), __('Invoice No'), __('Customer'), __('Total Amount')];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $totalDues = 0;
            @endphp
            @foreach ($sales as $index => $sale)
                @php
                    if ($sale->due_amount == 0) {
                        continue;
                    }
                    $totalDues += $sale->due_amount;
                @endphp

                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ formatDate($sale->order_date) }}</td>
                    <td>{{ $sale->invoice }}</td>
                    <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                    <td>{{ currency($sale->due_amount) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-end"><b>{{ __('Total') }}</b></td>
                <td colspan="2"><b>{{ currency($totalDues) }}</b></td>
            </tr>
        </tbody>
    </table>
@endsection
