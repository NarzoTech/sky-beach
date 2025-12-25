@extends('admin.layouts.pdf-layout')

@section('title', __('Purchase Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Date'),
                    __('Invoice'),
                    __('Supplier'),
                    __('Purchased By'),
                    __('Invoice Qty'),
                    __('Total'),
                    __('Paid'),
                    __('Due'),
                    __('Payment Status'),
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
                $data['total_amount'] = 0;
                $data['paid_amount'] = 0;
                $data['due_amount'] = 0;
            @endphp
            @foreach ($purchases as $index => $purchase)
                @php
                    $data['total_amount'] += $purchase->total_amount;
                    $data['paid_amount'] += $purchase->paid_amount;
                    $data['due_amount'] += $purchase->due_amount;

                @endphp

                <tr>
                    <td>{{ ++$index }}</td>
                    <td>
                        {{ formatDate($purchase->purchase_date) }}
                    </td>
                    <td>
                        {{ $purchase->invoice_number }}
                    </td>
                    <td>
                        {{ $purchase->supplier->name ?? 'Guest' }}
                    </td>
                    <td>
                        {{ $purchase->createdBy->name }}
                    </td>
                    <td>
                        {{ $purchase->purchaseDetails->sum('quantity') }}
                    </td>
                    <td>
                        {{ (int) $purchase->total_amount }}
                    </td>
                    <td>
                        {{ (int) $purchase->paid_amount }}
                    </td>
                    <td>
                        {{ (int) $purchase->due_amount }}
                    </td>
                    <td>
                        <span class="badge {{ $purchase->due_amount == 0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $purchase->due_amount == 0 ? 'Paid' : 'Due' }}
                        </span>
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="6" class="text-center">
                    <b> {{ __('Total') }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['total_amount'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['paid_amount'] }}</b>
                </td>
                <td colspan="1">
                    <b>{{ $data['due_amount'] }}</b>
                </td>
            </tr>
        </tbody>
    </table>
@endsection
