@extends('admin.layouts.pdf-layout')

@section('title', 'Expense Supplier List')

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Name') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Company') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Phone') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Total Expense') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Total Paid') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Total Due') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Advance') }}</th>
            </tr>
        </thead>
        <tbody>

            @php
                $i = 1;
            @endphp

            @foreach ($suppliers as $index => $supplier)
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $i++ }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $supplier->name }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $supplier->company ?? '-' }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $supplier->phone ?? '-' }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">
                        {{ currency($supplier->total_expense) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($supplier->total_paid) }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($supplier->total_due) }}
                    </td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($supplier->advance) }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="4" class="text-right font-weight-bold" style="border: 1px solid #ccc; padding: 8px;">
                    {{ __('Total') }}
                </td>
                <td colspan="1" style="border: 1px solid #ccc; padding: 8px;">
                    {{ currency($data['totalExpense']) }}
                </td>
                <td colspan="1" style="border: 1px solid #ccc; padding: 8px;">
                    {{ currency($data['pay']) }}
                </td>
                <td colspan="1" style="border: 1px solid #ccc; padding: 8px;">
                    {{ currency($data['total_due']) }}
                </td>
                <td colspan="1" style="border: 1px solid #ccc; padding: 8px;">
                    {{ currency($data['total_advance']) }}
                </td>
            </tr>
        </tbody>
    </table>
@endsection
