@extends('admin.layouts.pdf-layout')

@section('title', __('Suppliers Payment Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Date'), __('Invoice'), __('Suppliers'), __('Total'), __('Paid'), __('Due'), __('Return')];
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
                $data['total'] = 0;
                $data['paid_amount'] = 0;
                $data['due_amount'] = 0;
                $data['return_amount'] = 0;
            @endphp
            @foreach ($supplierPayments as $index => $supplierPayment)
                @php
                    $data['total'] += $supplierPayment->total_amount;
                    $data['paid_amount'] += $supplierPayment->paid_amount;
                    $data['due_amount'] +=
                        $supplierPayment->due_amount -
                        $supplierPayment->purchaseReturn->sum('return_amount') +
                        $supplierPayment->purchaseReturn->sum('received_amount');
                    $data['return_amount'] += $supplierPayment->purchaseReturn->sum('return_amount');
                @endphp

                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ formatDate($supplierPayment->purchase_date) }}</td>
                    <td>{{ $supplierPayment->invoice_number }}</td>
                    <td>{{ $supplierPayment->supplier->name }}</td>
                    <td>{{ $supplierPayment->total_amount }}</td>
                    <td>{{ $supplierPayment->paid_amount }}</td>
                    <td>{{ $supplierPayment->due_amount - $supplierPayment->purchaseReturn->sum('return_amount') + $supplierPayment->purchaseReturn->sum('received_amount') }}
                    </td>
                    <td>{{ $supplierPayment->purchaseReturn->sum('return_amount') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-center">
                    <b> {{ __('Total') }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['total'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['paid_amount'] }}</b>
                </td>
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
