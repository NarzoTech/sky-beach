@extends('admin.layouts.pdf-layout')

@section('title', __('Table Performance Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Table Name'),
                    __('Floor'),
                    __('Capacity'),
                    __('Total Orders'),
                    __('Total Revenue'),
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
            @foreach ($tables as $index => $table)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $table->table->name ?? 'N/A' }}</td>
                    <td>{{ $table->table->floor ?? 'N/A' }}</td>
                    <td>{{ $table->table->capacity ?? 'N/A' }}</td>
                    <td>{{ $table->total_orders }}</td>
                    <td>{{ currency($table->net_revenue ?? ($table->total_revenue - ($table->total_tax ?? 0))) }}</td>
                    <td>{{ currency($table->total_orders > 0 ? ($table->net_revenue ?? ($table->total_revenue - ($table->total_tax ?? 0))) / $table->total_orders : 0) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-end"><b>{{ __('Total') }}</b></td>
                <td><b>{{ $data['totalOrders'] }}</b></td>
                <td><b>{{ currency($data['totalRevenue']) }}</b></td>
                <td><b>{{ currency($data['totalOrders'] > 0 ? $data['totalRevenue'] / $data['totalOrders'] : 0) }}</b></td>
            </tr>
        </tbody>
    </table>
@endsection
