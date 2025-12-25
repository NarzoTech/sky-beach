@extends('admin.layouts.pdf-layout')

@section('title', __('Supplier Group'))

@section('content')
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            @php
                $list = [__('Name'), __('Discount'), __('Status')];
            @endphp
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('SN') }}</th>
                @foreach ($list as $st)
                    <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ $st }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($supplierGroups as $index => $group)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $group->name }}</td>
                    <td>{{ $group->discount }}</td>
                    <td>
                        @if ($group->status == 1)
                            <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge badge-danger">{{ __('Inactive') }}</span>
                        @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
