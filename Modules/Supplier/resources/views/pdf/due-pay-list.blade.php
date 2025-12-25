@extends('admin.layouts.pdf-layout')

@section('title', __('Supplier Due Pay List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            @php
                $list = [__('Date'), __('Invoice No'), __('Supplier'), __('Amount'), __('Paid By')];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ formatDate($payment->payment_date) }}
                    </td>
                    <td>{{ $payment->purchase?->invoice_number }}</td>
                    <td>{{ $payment->supplier->name }}</td>
                    <td>{{ currency($payment->amount) }}</td>
                    <td>{{ $payment->createdBy->name }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="javascript:;" class="btn btn-danger btn-sm" onclick="deleteData({{ $payment->id }})">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            @if ($payments->count() > 0)
                <tr>
                    <td colspan="4" class="text-center fw-bold">
                        {{ __('Total') }}
                    </td>
                    <td colspan="3" class="fw-bold">
                        {{ currency($data['total']) }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
