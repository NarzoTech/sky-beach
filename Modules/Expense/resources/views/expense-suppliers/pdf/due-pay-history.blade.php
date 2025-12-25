@extends('admin.layouts.pdf-layout')

@section('title', __('Expense Supplier Due Pay History'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Payment Date') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Expense ID') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Supplier') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Amount') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Note') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Created By') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $loop->iteration }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ formatDate($payment->payment_date) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">EXP-{{ $payment->expense_id }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $payment->expenseSupplier->name }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ currency($payment->amount) }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $payment->note ?? '-' }}</td>
                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $payment->createdBy->name }}</td>
                </tr>
            @endforeach
            @if ($payments->count() > 0)
                <tr>
                    <td colspan="4" class="text-center fw-bold" style="border: 1px solid #ccc; padding: 8px;">
                        {{ __('Total') }}
                    </td>
                    <td colspan="3" class="fw-bold" style="border: 1px solid #ccc; padding: 8px;">
                        {{ currency($data['total']) }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
