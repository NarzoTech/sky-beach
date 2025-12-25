@extends('admin.layouts.pdf-layout')

@section('title', $title)

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            @php
                $list = [
                    __('Invoice No'),
                ];
                if ($title == 'Supplier Ledger') {
                    $list[] = __('Memo No');
                }
                $list = array_merge($list, [
                    __('Date'),
                    __('Description'),
                    $title == 'Supplier Ledger'
                        ? __('Paid') . ' (' . __('CREDIT') . ')'
                        : __('Received') . ' (' . __('DEBIT') . ')',
                    $title == 'Supplier Ledger'
                        ? __('Product') . ' (' . __('DEBIT') . ')'
                        : __('Sales') . ' (' . __('CREDIT') . ')',
                    __('Balance') . ($title == 'Supplier Ledger' ? ' (' . __('DUE') . ')' : ''),
                ]);
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
                $opening = 0;
                $credit = 0;
                $debit = 0;
            @endphp
            <tr>
                <td colspan="{{ $title == 'Supplier Ledger' ? 6 : 5 }}" class="text-center fw-bold">{{ __('Opening Balance') }}</td>
                <td></td>
                <td colspan="1" class="text-end fw-bold">{{ currency($opening) }}</td>
            </tr>
            @foreach ($ledgers as $index => $ledger)
                @php
                    $opening += $ledger->due_amount;
                    $credit += $ledger->amount;
                    $debit += $ledger->total_amount;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $ledger->invoice_no }}</td>
                    @if ($title == 'Supplier Ledger')
                        <td>{{ $ledger->purchase?->memo_no }}</td>
                    @endif
                    <td>{{ formatDate($ledger->date) }}</td>
                    <td class="text-capitalize">{{ $ledger->invoice_type }}</td>
                    <td>{{ currency($ledger->amount) }}</td>
                    <td>{{ currency($ledger->total_amount) }}</td>
                    <td class="text-end">{{ currency($opening) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="{{ $title == 'Supplier Ledger' ? 5 : 4 }}" class="text-center fw-bold">
                    {{ __('Total') }}
                </td>
                <td colspan="1" class="fw-bold">{{ currency($credit) }}</td>
                <td colspan="1" class="fw-bold">{{ currency($debit) }}</td>
                <td class="text-end fw-bold">{{ currency($opening) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
