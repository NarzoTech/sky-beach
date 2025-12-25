@extends('admin.layouts.pdf-layout')

@section('title', __('Barcode Wise Product Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Product Name'),
                    __('Attribute'),
                    __('Barcode'),
                    __('Brand Name'),
                    __('Sale'),
                    __('Sale Return'),
                    __('Purchase'),
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
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->attribute }}</td>
                    <td>{{ $product->barcode }}</td>
                    <td>{{ $product->brand->name ?? 'N/A' }}</td>
                    <td>{{ currency((int) $product->sales['price']) }}({{ $product->sales['qty'] }})
                    </td>
                    <td>{{ currency((int) $product->sales_return['price']) }}({{ $product->sales_return['qty'] }})
                    </td>
                    <td>{{ currency((int) $product->total_purchase['price']) }}({{ $product->total_purchase['qty'] }})
                    </td>
                </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="5" style="text-align: right;">{{ __('Total') }}</td>
                <td>{{ currency($data['totalSalePrice']) }}({{ $data['totalSaleQty'] }})</td>
                <td>{{ currency($data['totalReturnPrice']) }}({{ $data['totalReturnQty'] }})</td>
                <td>{{ currency($data['totalPurchasePrice']) }}({{ $data['totalPurchaseQty'] }})</td>
            </tr>
        </tbody>
    </table>
@endsection
