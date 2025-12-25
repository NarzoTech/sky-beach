@extends('admin.layouts.pdf-layout')

@section('title', __('Received Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Date'),
                    __('Invoice Type'),
                    __('Invoice'),
                    __('Customer'),
                    __('Total Amount'),
                    __('Pay By'),
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
                $data['receive'] = 0;
            @endphp
            @foreach ($totalReceive as $index => $receive)
                @php
                    $data['receive'] += $receive->amount;

                @endphp

                <tr>
                    <td>{{ ++$index }}</td>
                    <td>
                        {{ formatDate($receive->payment_date) }}
                    </td>
                    <td>
                        {{ ucwords($receive->payment_type) }}
                    </td>
                    <td>
                        {{ $receive->invoice ?? $receive->sale->invoice }}
                    </td>
                    <td>
                        {{ $receive->customer->name }}
                    </td>
                    <td>
                        {{ $receive->amount }}
                    </td>
                    <td>
                        {{ $receive->account->account_type }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-center">
                    <b>{{ __('Total Amount') }}</b>
                </td>
                <td>
                    <b>{{ $data['receive'] }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
