@extends('admin.layouts.pdf-layout')

@section('title', __('Categories Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Category Name'), __('Purchase'), __('Sold'), __('Purchase Amount'), __('Sold Amount')];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>

            @foreach ($categories as $index => $category)
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->PurchaseSummary['count'] }}</td>
                    <td>{{ $category->sales_count }}</td>
                    <td>{{ currency($category->PurchaseSummary['amount']) }}</td>
                    <td>{{ currency($category->sales_amount) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: bold;">
                <td colspan="2" style="text-align: right;">{{ __('Total') }}</td>
                <td>{{ $data['totalPurchaseCount'] }}</td>
                <td>{{ $data['totalSalesCount'] }}</td>
                <td>{{ currency($data['totalPurchaseAmount']) }}</td>
                <td>{{ currency($data['totalSalesAmount']) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
