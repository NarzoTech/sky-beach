@extends('admin.layouts.pdf-layout')

@section('title', __('Asset List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Name'), __('Date'), __('Type'), __('Pay By'), __('Note'), __('Amount')];
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
            @foreach ($lists as $index => $type)
                @php
                    $totalAmount += $type->amount;
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $type->name }}</td>
                    <td>
                        {{ formatDate($type->date) }}
                    </td>
                    <td>{{ $type->type->name }}</td>
                    <td>{{ $type->account->account_type }}</td>
                    <td>
                        {{ $type->note }}
                    </td>
                    <td>
                        {{ $type->amount }}
                    </td>
                </tr>
            @endforeach
            @if ($lists->count() > 0)
                <tr>
                    <td colspan="6" style="text-align: center; font-weight: bold">
                        <b>{{ __('Total') }}</b>
                    </td>
                    <td>
                        <b>{{ $totalAmount }}</b>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
