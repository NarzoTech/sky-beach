@extends('admin.layouts.pdf-layout')

@section('title', __('Salary Report'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [__('Employee Name'), __('Total Salary'), __('Total Paid Amount')];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($employees as $index => $employee)
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->total_salary }}</td>
                    <td>{{ $employee->paid_salary }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection
