@extends('admin.layouts.pdf-layout')

@section('title', 'Product List')

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Name') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Barcode') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Stock Qty') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Price') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('After Disc. P.') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Brand') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Category') }}</th>
            </tr>
        </thead>
        <tbody>

            @php
                $i = 1;
            @endphp

            @foreach ($products as $index => $product)
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $i++ }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $product->name }} </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $product->barcode }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $product->stock }}{{ $product->unit->ShortName }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $product->current_price }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $product->current_price }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $product->brand->name }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $product->category->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
