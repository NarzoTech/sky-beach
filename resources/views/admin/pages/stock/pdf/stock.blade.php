@extends('admin.layouts.pdf-layout')

@section('title', __('Stock List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Name'),
                    __('Avg P.P'),
                    __('L. P.P'),
                    __('In Quantity'),
                    __('Out Quantity'),
                    __('Stock'),
                    __('Stock P.P'),
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

            @foreach ($products as $index => $product)
                @php
                    $stock = $product->stock < 0 ? 0 : $product->stock;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->avg_purchase_price }}</td>
                    <td>{{ $product->last_purchase_price }}</td>
                    <td>{{ round($product->stockDetails->sum('base_in_quantity'), 4) }} {{ $product->purchaseUnit->ShortName ?? '' }}</td>
                    <td>{{ round($product->stockDetails->sum('base_out_quantity'), 4) }} {{ $product->purchaseUnit->ShortName ?? '' }}</td>
                    <td>{{ $product->stock }} {{ $product->purchaseUnit->ShortName ?? '' }}</td>
                    <td>{{ remove_comma($stock) * remove_comma($product->avg_purchase_price) }}</td>
                </tr>
            @endforeach

            <tr style="font-weight: bold; background-color: #f0f0f0;">
                <td colspan="4" style="text-align: right; padding: 8px;">{{ __('Total') }}</td>
                <td style="padding: 8px;">{{ $totals['totalInQty'] ?? 0 }}</td>
                <td style="padding: 8px;">{{ $totals['totalOutQty'] ?? 0 }}</td>
                <td style="padding: 8px;">{{ $totals['totalStock'] ?? 0 }}</td>
                <td style="padding: 8px;">{{ number_format($totals['totalStockPP'] ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
