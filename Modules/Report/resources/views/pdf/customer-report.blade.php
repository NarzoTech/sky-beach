@extends('admin.layouts.pdf-layout')

@section('title', __('Customer Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Name'), __('Phone'), __('Total Sales'), __('Total'), __('Paid'), __('Due')];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $index => $customer)
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->sales->count() }}</td>
                    <td>{{ currency($customer->sales->sum('grand_total')) }}</td>
                    <td>{{ currency($customer->total_paid) }}</td>
                    <td>{{ currency($customer->total_due) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right;">{{ __('Total') }}</td>
                <td>{{ $data['totalSales'] }}</td>
                <td>{{ currency($data['totalAmount']) }}</td>
                <td>{{ currency($data['totalPaid']) }}</td>
                <td>{{ currency($data['totalDue']) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
