@extends('admin.layouts.pdf-layout')

@section('title', __('Paid Salary List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Employee'), __('Paid'), __('Date'), __('Note')];
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
                $totalAmount = 0;
            @endphp
            @foreach ($payments as $index => $payment)
                @php
                    $totalAmount += $payment->amount;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $payment->employee?->name }}</td>
                    <td>{{ $payment->amount }}</td>
                    <td>{{ formatDate($payment->date) }}</td>
                    <td>{{ $payment->note }}</td>
                </tr>
            @endforeach
            @if ($payments->count() > 0)
                <tr>
                    <td colspan="2" style="text-align: center; font-weight: bold">
                        <b>{{ __('Total') }}</b>
                    </td>
                    <td>
                        <b>{{ $totalAmount }}</b>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
