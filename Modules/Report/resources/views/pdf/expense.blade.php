@extends('admin.layouts.pdf-layout')

@section('title', __('Expense Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Date'), __('Created By'), __('Type'), __('Note'), __('Amount')];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp
            @foreach ($expenses as $index => $expense)
                @php
                    $totalAmount += $expense->amount;
                @endphp

                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ formatDate($expense->date) }}</td>
                    <td>{{ $expense->createdBy->name }}</td>
                    <td>{{ $expense->expenseType->name }}</td>
                    <td>{{ $expense->note }}</td>
                    <td>{{ $expense->amount }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" class="text-center">
                    <b>{{ __('Total Amount') }}</b>
                </td>
                <td>
                    <b>{{ $totalAmount }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
