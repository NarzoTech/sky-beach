@extends('admin.layouts.pdf-layout')

@section('title', $title . ' - ' . $supplier->name)

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Invoice No') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Date') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Description') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Paid') }} ({{ __('CREDIT') }})</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Expense') }} ({{ __('DEBIT') }})</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Balance') }} ({{ __('DUE') }})</th>
            </tr>
        </thead>
        <tbody>
            @php
                $opening = 0;
                $credit = 0;
                $debit = 0;
            @endphp
            <tr>
                <td colspan="6" class="text-center fw-bold" style="border: 1px solid #ccc; padding: 8px;">{{ __('Opening Balance') }}</td>
                <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($opening) }}</td>
            </tr>
            @foreach ($ledgers as $index => $ledger)
                @php
                    $opening += $ledger->due_amount;
                    $credit += $ledger->amount;
                    $debit += $ledger->total_amount;
                @endphp
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $loop->iteration }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $ledger->invoice_no }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ formatDate($ledger->date) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;" class="text-capitalize">{{ $ledger->invoice_type }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($ledger->amount) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($ledger->total_amount) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($opening) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-center fw-bold" style="border: 1px solid #ccc; padding: 8px;">
                    {{ __('Total') }}
                </td>
                <td colspan="1" class="fw-bold" style="border: 1px solid #ccc; padding: 8px;">{{ currency($credit) }}</td>
                <td colspan="1" class="fw-bold" style="border: 1px solid #ccc; padding: 8px;">{{ currency($debit) }}</td>
                <td class="fw-bold" style="border: 1px solid #ccc; padding: 8px;">{{ currency($opening) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
