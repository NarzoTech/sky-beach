@extends('admin.layouts.pdf-layout')

@section('title', __('Menu Item Sales Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Menu Item'),
                    __('Category'),
                    __('Qty Sold'),
                    __('Revenue'),
                    __('Cost (COGS)'),
                    __('Profit'),
                    __('Profit %'),
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
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->menuItem->name ?? 'N/A' }}</td>
                    <td>{{ $item->menuItem->category->name ?? 'N/A' }}</td>
                    <td>{{ $item->total_qty }}</td>
                    <td>{{ currency($item->total_revenue) }}</td>
                    <td>{{ currency($item->total_cogs) }}</td>
                    <td>{{ currency($item->total_profit) }}</td>
                    <td>{{ $item->total_revenue > 0 ? round(($item->total_profit / $item->total_revenue) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-end"><b>{{ __('Total') }}</b></td>
                <td><b>{{ $data['totalQty'] }}</b></td>
                <td><b>{{ currency($data['totalRevenue']) }}</b></td>
                <td><b>{{ currency($data['totalCogs']) }}</b></td>
                <td><b>{{ currency($data['totalProfit']) }}</b></td>
                <td><b>{{ $data['totalRevenue'] > 0 ? round(($data['totalProfit'] / $data['totalRevenue']) * 100, 1) : 0 }}%</b></td>
            </tr>
        </tbody>
    </table>
@endsection
