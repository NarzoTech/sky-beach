@extends('admin.layouts.pdf-layout')

@section('title', __('Employee List'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;" page-break-inside: avoid>
        <thead>
            @php
                $list = [
                    __('Employee Name'),
                    __('Designation'),
                    __('Phone'),
                    __('Email'),
                    __('Base Salary'),
                    __('Joining Date'),
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
            @foreach ($employees as $index => $employee)
                <tr>
                    <td>{{ ++$index }}</td>
                    <td>{{ $employee->name }}</td>
                    <td>{{ $employee->designation }}</td>
                    <td>{{ $employee->mobile }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->salary }}</td>
                    <td>{{ formatDate($employee->join_date) ?? __('N/A') }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection
