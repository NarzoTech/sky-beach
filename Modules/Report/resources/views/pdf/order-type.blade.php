@extends('admin.layouts.pdf-layout')

@section('title', __('Order Type Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Order Type'),
                    __('Total Orders'),
                    __('Revenue') . ' (' . __('incl. Tax') . ')',
                    __('Tax'),
                    __('Net Revenue') . ' (' . __('excl. Tax') . ')',
                    __('Total Cost'),
                    __('Profit'),
                    __('% of Total'),
                ];
                $orderTypeLabels = [
                    'dine_in' => __('Dine In'),
                    'take_away' => __('Take Away'),
                    'website' => __('Website'),
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
            @foreach ($orderTypes as $index => $type)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $orderTypeLabels[$type->order_type] ?? ucfirst(str_replace('_', ' ', $type->order_type)) }}</td>
                    <td>{{ $type->total_orders }}</td>
                    <td>{{ currency($type->total_revenue) }}</td>
                    <td style="color: red;">{{ currency($type->total_tax) }}</td>
                    <td>{{ currency($type->total_revenue - $type->total_tax) }}</td>
                    <td>{{ currency($type->total_cogs) }}</td>
                    <td>{{ currency($type->total_revenue - $type->total_tax - $type->total_cogs) }}</td>
                    <td>{{ $grandTotalOrders > 0 ? round(($type->total_orders / $grandTotalOrders) * 100, 1) : 0 }}%</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="text-end"><b>{{ __('Total') }}</b></td>
                <td><b>{{ $data['totalOrders'] }}</b></td>
                <td><b>{{ currency($data['totalRevenue']) }}</b></td>
                <td style="color: red;"><b>{{ currency($data['totalTax']) }}</b></td>
                <td><b>{{ currency($data['totalNetRevenue']) }}</b></td>
                <td><b>{{ currency($data['totalCogs']) }}</b></td>
                <td><b>{{ currency($data['totalProfit']) }}</b></td>
                <td><b>100%</b></td>
            </tr>
        </tbody>
    </table>
@endsection
