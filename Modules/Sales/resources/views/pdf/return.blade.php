@extends('admin.layouts.pdf-layout')

@section('title', __('Sales Return List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Date'),
                    __('Invoice No'),
                    __('Customer'),
                    __('Total Amount'),
                    __('Paying Amount'),
                    __('Payment Status'),
                    __('Due'),
                ];
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
                $data['totalAmount'] = 0;
                $data['paidAmount'] = 0;
                $data['totalDue'] = 0;
            @endphp
            @foreach ($lists as $index => $sale)
                @php
                    $data['totalAmount'] += $sale->return_amount;
                    $data['paidAmount'] += $sale->return_amount - $sale->return_due;
                    $data['totalDue'] += $sale->return_due;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ formatDate($sale->return_date) }}</td>
                    <td>{{ $sale->invoice }}</td>
                    <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                    <td>{{ $sale->return_amount }}</td>
                    <td>{{ $sale->return_amount - $sale->return_due }}</td>
                    <td>
                        @if (!$sale->return_due)
                            <span class="badge bg-success">{{ __('Paid') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('Due') }}</span>
                        @endif
                    </td>
                    <td>{{ $sale->return_due }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: center; font-weight: bold">
                    {{ __('Total') }}
                </td>
                <td class="fw-bold">
                    {{ $data['totalAmount'] }}
                </td>
                <td class="fw-bold">
                    {{ $data['paidAmount'] }}
                </td>
                <td class="fw-bold"></td>
                <td colspan="1" class="fw-bold">
                    {{ $data['totalDue'] }}
                </td>
            </tr>
        </tbody>
    </table>
@endsection
