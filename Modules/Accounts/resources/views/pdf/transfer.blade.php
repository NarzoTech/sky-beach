@extends('admin.layouts.pdf-layout')

@section('title', __('Balance Transfer List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; page-break-inside: avoid;">
        <thead>
            @php
                $list = [__('From Account'), __('To Account'), __('Amount'), __('Added By'), __('Date'), __('Remark')];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($transfers as $index => $balanceTransfer)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ ++$index }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ accountList()[$balanceTransfer->fromAccount->account_type] ?? '-' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ accountList()[$balanceTransfer->toAccount->account_type] ?? '-' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ currency($balanceTransfer->amount) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $balanceTransfer->createdBy->name ?? '-' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ formatDate($balanceTransfer->date) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $balanceTransfer->note ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
