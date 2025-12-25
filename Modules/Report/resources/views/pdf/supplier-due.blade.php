@extends('admin.layouts.pdf-layout')

@section('title', __('Supplier Other Due List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Name'), __('Company'), __('Phone'), __('Total'), __('Paid'), __('Due')];
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
                $data['total_paid'] = 0;
                $data['total_due'] = 0;
            @endphp
            @foreach ($summeries as $index => $summery)
                @php
                    $data['total_amount'] += $summery->otherSummery->sum('amount');
                    $data['total_paid'] += $summery->otherSummery->sum('paid');
                    $data['total_due'] += $summery->otherSummery->sum('due');
                @endphp
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $summery->name }}</td>
                    <td>{{ $summery->company }}</td>
                    <td>{{ $summery->phone }}</td>
                    <td>{{ $summery->otherSummery->sum('amount') }}</td>
                    <td>{{ $summery->otherSummery->sum('paid') }}</td>
                    <td>{{ $summery->otherSummery->sum('due') }}</td>
                </tr>
            @endforeach
            @if ($summeries->count() > 0)
                <tr>
                    <td colspan="4" style="text-align: center; font-weight: bold">
                        <b>{{ __('Total') }}</b>
                    </td>
                    <td>
                        <b>{{ $data['total_amount'] }}</b>
                    </td>
                    <td>
                        <b>{{ $data['total_paid'] }}</b>
                    </td>
                    <td>
                        <b>{{ $data['total_due'] }}</b>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
