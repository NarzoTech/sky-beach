@extends('admin.layouts.pdf-layout')

@section('title', __('Barcode Wise Sale Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Product Name'),
                    __('Barcode'),
                    __('Brand Name'),
                    __('Stock Qty'),
                    __('Selling Qty'),
                    __('Selling Price'),
                    __('Purchase Price'),
                    __('Profit/Loss'),
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
                    $sellQty = $product->sales['qty'] - $product->sales_return['qty'];
                    $sellingPrice = $sellQty > 0 ? $product->sales['price'] / $sellQty : 0;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->barcode }}</td>
                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                    <td>{{ $product->stock_count }}</td>
                    <td>{{ $sellQty }}</td>
                    <td>{{ $sellingPrice }}</td>
                    <td>{{ $product->purchase_price }}</td>
                    <td>{{ $sellQty * $sellingPrice - $sellQty * $product->purchase_price }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-end">
                    <b>Total</b>
                </td>
                <td>
                    <b>{{ $data['totalStock'] }}</b>
                </td>
                <td>
                    <b>{{ $data['sellCount'] }}</b>
                </td>
                <td>
                    <b>{{ $data['sellPrice'] }}</b>
                </td>
                <td>
                    <b>{{ $data['totalPurchasePrice'] }}</b>
                </td>
                <td>
                    <b>{{ $data['totalProfitLoss'] }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
