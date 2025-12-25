@extends('admin.layouts.pdf-layout')

@section('title', __('Customer List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Name'),
                    __('Phone'),
                    __('Area'),
                    __('Total Sale'),
                    __('Sale Payment'),
                    __('Sale Due'),
                    __('Advance'),
                    __('Total Due'),
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
            @foreach ($users as $index => $user)
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->area->name }}</td>
                    <td>{{ currency($user->sales->sum('grand_total')) }}</td>
                    <td>{{ currency($user->total_paid) }}</td>
                    <td>{{ currency($user->total_due) }}</td>
                    <td>{{ currency($user->advances()) }}</td>
                    <td>{{ currency($user->total_due - $user->total_sale_return_due) }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4" class="text-center fw-bold">
                    {{ __('Total') }}
                </td>
                <td class="fw-bold">
                    {{ currency($data['totalSale']) }}
                </td>
                <td class="fw-bold">
                    {{ currency($data['pay']) }}
                </td>
                <td class="fw-bold">
                    {{ currency($data['total_due']) }}
                </td>
                <td class="fw-bold">
                    {{ currency($data['total_advance']) }}
                </td>
                <td class="fw-bold" colspan="2">
                    {{ currency($data['total_due'] - $data['total_return_due']) }}
                </td>
            </tr>
        </tbody>
    </table>
@endsection
