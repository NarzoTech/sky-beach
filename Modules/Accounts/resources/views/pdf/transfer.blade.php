@extends('admin.layouts.pdf-layout')

@section('title', __('Balance Transfer List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('From Account'), __('To Account'), __('Amount'), __('Added By'), __('Date')];
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
                    <td>{{ ++$index }}</td>
                    <td>{{ accountList()[$balanceTransfer->fromAccount->account_type] }}
                    </td>
                    <td>{{ accountList()[$balanceTransfer->toAccount->account_type] }}</td>
                    <td>{{ $balanceTransfer->amount }}</td>
                    <td>{{ $balanceTransfer->createdBy->name }}</td>
                    <td>{{ formatDate($balanceTransfer->date) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
