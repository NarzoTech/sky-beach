@extends('admin.layouts.pdf-layout')

@section('title', __('Due Date Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Sale Date'),
                    __('Due Date'),
                    __('Invoice'),
                    __('Customer'),
                    __('Phone'),
                    __('Total'),
                    __('Paid'),
                    __('Paid By'),
                    __('Due'),
                    __('Return Amount'),
                    __('Payment Status'),
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
                $data['due_amount'] = 0;
            @endphp
            @foreach ($sales as $index => $sale)
                @php
                    $data['due_amount'] += $sale->due_amount;

                @endphp

                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ formatDate($sale->order_date) }}</td>
                    <td>{{ formatDate($sale->due_date) }}</td>
                    <td>{{ $sale->invoice }}</td>
                    <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                    <td>{{ $sale->customer->phone }}</td>
                    <td>{{ $sale->grand_total }}</td>
                    <td>{{ $sale->paid_amount }}</td>
                    <td>
                        @foreach ($sale->payment as $payment)
                            {{ $payment->account->account_type }} :
                            {{ $payment->amount }}
                            <br>
                        @endforeach
                    </td>
                    <td>{{ $sale->due_amount }}</td>
                    <td>{{ $sale->saleReturns->sum('return_amount') }}</td>
                    <td>{{ $sale->due_amount == 0 ? 'Paid' : 'Due' }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="9" class="text-center">
                    <b> {{ __('Total') }}</b>
                </td>
                <td colspan="1">
                    <b>{{ currency($data['due_amount']) }}</b>
                </td>

            </tr>
        </tbody>
    </table>
@endsection
