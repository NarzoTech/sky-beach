@extends('admin.layouts.pdf-layout')

@section('title', __('Expenses Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid;">
        <thead>
            @php
                $list = [
                    __('Invoice'),
                    __('Date'),
                    __('Supplier'),
                    __('Type'),
                    __('Amount'),
                    __('Paid'),
                    __('Due'),
                    __('Status'),
                    __('Note'),
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
            @php
                $totalAmount = 0;
                $totalPaid = 0;
                $totalDue = 0;
            @endphp
            @foreach ($expenses as $index => $expense)
                @php
                    $totalAmount += $expense->amount;
                    $totalPaid += $expense->paid_amount;
                    $totalDue += $expense->due_amount;
                    $status = $expense->payment_status_label;
                @endphp
                <tr>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $expense->invoice ?? '-' }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ formatDate($expense->date) }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $expense->expenseSupplier->name ?? '-' }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $expense->expenseType->name }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ currency($expense->amount) }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ currency($expense->paid_amount) }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ currency($expense->due_amount) }}</td>
                    <td style="border: 1px solid #ccc; padding: 6px;">
                        @if($status == 'paid')
                            {{ __('Paid') }}
                        @elseif($status == 'partial')
                            {{ __('Partial') }}
                        @else
                            {{ __('Due') }}
                        @endif
                    </td>
                    <td style="border: 1px solid #ccc; padding: 6px;">{{ $expense->note }}</td>
                </tr>
            @endforeach
            @if (count($expenses) > 0)
                <tr style="font-weight: bold; background-color: #f0f0f0;">
                    <td colspan="5" style="border: 1px solid #ccc; padding: 6px; text-align: center;">
                        <b>{{ __('Total') }}</b>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 6px;">
                        <b>{{ currency($totalAmount) }}</b>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 6px;">
                        <b>{{ currency($totalPaid) }}</b>
                    </td>
                    <td style="border: 1px solid #ccc; padding: 6px;">
                        <b>{{ currency($totalDue) }}</b>
                    </td>
                    <td colspan="2"></td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
