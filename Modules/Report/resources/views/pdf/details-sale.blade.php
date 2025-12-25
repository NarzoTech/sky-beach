@extends('admin.layouts.pdf-layout')

@section('title', __('Details Sales Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Date'),
                    __('Invoice'),
                    __('Customer'),
                    __('Total '),
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
                $data['total_amount'] = 0;
                $data['paid_amount'] = 0;
                $data['due_amount'] = 0;
                $data['return_amount'] = 0;
            @endphp
            @foreach ($sales as $index => $sale)
                @php
                    $data['total_amount'] += $sale->grand_total;
                    $data['paid_amount'] += $sale->paid_amount;
                    $data['due_amount'] += $sale->due_amount;
                    $data['return_amount'] += $sale->saleReturns->sum('return_amount');
                @endphp

                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ formatDate($sale->order_date) }}</td>
                    <td>{{ $sale->invoice }}</td>
                    <td>{{ $sale?->customer?->name ?? 'Guest' }}</td>
                    <td>
                        {{ $sale->grand_total }}
                    </td>
                    <td>
                        {{ $sale->paid_amount }}
                    </td>
                    <td>
                        @foreach ($sale->payment as $payment)
                            {{ $payment->account->account_type }} :
                            {{ $payment->amount }}
                            <br>
                        @endforeach
                    </td>
                    <td>
                        {{ $sale->due_amount }}
                    </td>
                    <td>
                        {{ $sale->saleReturns->sum('return_amount') }}
                    </td>
                    <td>
                        {{ $sale->due_amount == 0 ? 'Paid' : 'Due' }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-center">
                    <b> {{ __('Total') }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['total_amount'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['paid_amount'] }}</b>
                </td>
                <td colspan="1"></td>
                <td colspan="1">
                    <b>{{ $data['due_amount'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['return_amount'] }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
