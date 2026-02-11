@extends('admin.layouts.pdf-layout')

@section('title', __('Waiter Performance Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Waiter Name'),
                    __('Total Orders'),
                    __('Total Revenue'),
                    __('Total Cost'),
                    __('Profit'),
                    __('Avg Order Value'),
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
            @foreach ($waiters as $index => $waiter)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $waiter->waiter->name ?? 'N/A' }}</td>
                    <td>{{ $waiter->total_orders }}</td>
                    <td>{{ currency($waiter->net_revenue ?? ($waiter->total_revenue - ($waiter->total_tax ?? 0))) }}</td>
                    <td>{{ currency($waiter->total_cogs) }}</td>
                    <td>{{ currency(($waiter->net_revenue ?? ($waiter->total_revenue - ($waiter->total_tax ?? 0))) - ($waiter->total_cogs ?? 0)) }}</td>
                    <td>{{ currency($waiter->total_orders > 0 ? ($waiter->net_revenue ?? ($waiter->total_revenue - ($waiter->total_tax ?? 0))) / $waiter->total_orders : 0) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="text-end"><b>{{ __('Total') }}</b></td>
                <td><b>{{ $data['totalOrders'] }}</b></td>
                <td><b>{{ currency($data['totalRevenue']) }}</b></td>
                <td><b>{{ currency($data['totalCogs']) }}</b></td>
                <td><b>{{ currency($data['totalProfit']) }}</b></td>
                <td><b>{{ currency($data['totalOrders'] > 0 ? $data['totalRevenue'] / $data['totalOrders'] : 0) }}</b></td>
            </tr>
        </tbody>
    </table>
@endsection
