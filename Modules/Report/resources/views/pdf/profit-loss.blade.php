@extends('admin.layouts.pdf-layout')

@section('title', __('Profit/Loss Report'))

@section('content')
    <div style="text-align: center; margin-bottom: 20px;">
        <h3>{{ __('Profit/Loss Report') }}</h3>
        <p style="color: #666;">{{ $data['fromDate'] }} - {{ $data['toDate'] }}</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background-color: #003366; color: white;">
                <th style="border: 1px solid #003366; padding: 8px; text-align: left;">{{ __('Description') }}</th>
                <th style="border: 1px solid #003366; padding: 8px; text-align: right;">{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            <!-- Income Section -->
            <tr style="background-color: #d4edda;">
                <td colspan="2" style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">
                    {{ __('INCOME') }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ __('Total Sales') }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ currency($data['totalSales']) }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ __('Purchase Returns') }} ({{ __('Refund from Supplier') }})</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ currency($data['purchaseReturns']) }}</td>
            </tr>
            <tr style="background-color: #c3e6cb;">
                <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">{{ __('Total Income') }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; color: green;">{{ currency($data['totalIncome']) }}</td>
            </tr>

            <!-- Expense Section -->
            <tr style="background-color: #f8d7da;">
                <td colspan="2" style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">
                    {{ __('EXPENSES') }}
                </td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ __('Cost of Goods Sold (COGS)') }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ currency($data['cogs']) }}</td>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">{{ __('Gross Profit') }} ({{ __('Net Sales - COGS') }})</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold;">{{ currency($data['grossProfit']) }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ __('Operating Expenses') }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ currency($data['expenses']) }}</td>
            </tr>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">{{ __('Employee Salaries') }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">{{ currency($data['salaries']) }}</td>
            </tr>
            <tr style="background-color: #f5c6cb;">
                <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold;">{{ __('Total Expenses') }}</td>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right; font-weight: bold; color: red;">{{ currency($data['totalExpenses']) }}</td>
            </tr>

            <!-- Profit/Loss -->
            <tr style="background-color: {{ $data['profitLoss'] >= 0 ? '#28a745' : '#dc3545' }}; color: white;">
                <td style="border: 1px solid #ddd; padding: 12px; font-weight: bold; font-size: 14px;">
                    {{ __('NET PROFIT / LOSS') }}
                </td>
                <td style="border: 1px solid #ddd; padding: 12px; text-align: right; font-weight: bold; font-size: 14px;">
                    {{ currency($data['profitLoss']) }}
                    @if($data['profitLoss'] >= 0)
                        ({{ __('Profit') }})
                    @else
                        ({{ __('Loss') }})
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
@endsection
