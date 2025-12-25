@extends('admin.layouts.pdf-layout')

@section('title', __('Supplier Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Name'),
                    __('Company'),
                    __('Phone'),
                    __('Total Purchase'),
                    __('Total'),
                    __('Paid'),
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
                $data['purchase_count'] = 0;
                $data['totalPurchase'] = 0;
                $data['pay'] = 0;
                $data['total_due'] = 0;
            @endphp
            @foreach ($suppliers as $index => $supplier)
                @php
                    $data['purchase_count'] += $supplier->purchases->count();
                    $data['totalPurchase'] += $supplier->purchases->sum('total_amount');
                    $data['pay'] += $supplier->total_paid;
                    $data['total_due'] += $supplier->total_due;
                @endphp

                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->company }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>{{ $supplier->purchases->count() }}</td>
                    <td>{{ $supplier->purchases->sum('total_amount') }}</td>

                    <td>{{ $supplier->total_paid ?? 0 }}</td>
                    <td>{{ $supplier->total_due }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-center">
                    <b> {{ __('Total') }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['purchase_count'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['totalPurchase'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['pay'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['total_due'] }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
