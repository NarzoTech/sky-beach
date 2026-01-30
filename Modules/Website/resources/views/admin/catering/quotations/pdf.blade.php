<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Quotation') }} - {{ $quotation->quotation_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .container {
            padding: 20px;
        }
        .header {
            width: 100%;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #003366;
        }
        .header-table {
            width: 100%;
        }
        .company-logo {
            max-height: 50px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 4px;
        }
        .company-details {
            font-size: 10px;
            color: #666;
        }
        .quotation-title {
            font-size: 24px;
            font-weight: bold;
            color: #003366;
            text-align: right;
            margin-bottom: 8px;
        }
        .quotation-number {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            text-align: right;
        }
        .quotation-date {
            font-size: 11px;
            color: #666;
            text-align: right;
            margin-top: 4px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-quoted { background: #e3f2fd; color: #1976d2; }
        .status-confirmed { background: #e8f5e9; color: #388e3c; }
        .status-pending { background: #fff3e0; color: #f57c00; }
        .status-cancelled { background: #ffebee; color: #d32f2f; }

        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ddd;
        }
        .info-table {
            width: 100%;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .info-label {
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 12px;
            font-weight: 500;
            color: #333;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th, .items-table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .items-table thead th {
            background: #003366;
            color: #fff;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .totals-table {
            width: 250px;
            float: right;
            margin-top: 10px;
        }
        .totals-table td {
            padding: 6px 10px;
        }
        .totals-table .subtotal-row td {
            border-top: 1px solid #ddd;
        }
        .totals-table .total-row td {
            background: #003366;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
        }
        .discount-text { color: #28a745; }

        .notes-section {
            clear: both;
            background: #f8f9fa;
            padding: 12px;
            margin-top: 20px;
        }
        .notes-section h4 {
            font-size: 10px;
            color: #003366;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        .notes-section p {
            font-size: 10px;
            color: #666;
        }

        .validity-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-size: 11px;
            color: #856404;
        }
        .validity-notice.expired {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #888;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width: 60%; vertical-align: top;">
                        @if($setting && $setting->logo)
                            <img src="{{ public_path($setting->logo) }}" alt="Logo" class="company-logo">
                        @endif
                        <div class="company-name">{{ $setting->app_name ?? config('app.name') }}</div>
                        <div class="company-details">
                            @if($setting)
                                {{ $setting->address }}<br>
                                {{ __('Tel') }}: {{ $setting->mobile }} | {{ __('Email') }}: {{ $setting->email }}
                            @endif
                        </div>
                    </td>
                    <td style="width: 40%; vertical-align: top;">
                        <div class="quotation-title">{{ __('QUOTATION') }}</div>
                        <div class="quotation-number">{{ $quotation->quotation_number }}</div>
                        <div class="quotation-date">{{ __('Date') }}: {{ $quotation->quoted_at ? $quotation->quoted_at->format('F d, Y') : $quotation->created_at->format('F d, Y') }}</div>
                        <div style="text-align: right; margin-top: 6px;">
                            <span class="status-badge status-{{ $quotation->status }}">{{ $quotation->status_label }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Validity Notice -->
        @if($quotation->quotation_valid_until)
            <div class="validity-notice {{ $quotation->quotation_valid_until->isPast() ? 'expired' : '' }}">
                @if($quotation->quotation_valid_until->isPast())
                    <strong>{{ __('Notice') }}:</strong> {{ __('This quotation expired on') }} {{ $quotation->quotation_valid_until->format('F d, Y') }}
                @else
                    <strong>{{ __('Valid Until') }}:</strong> {{ $quotation->quotation_valid_until->format('F d, Y') }}
                @endif
            </div>
        @endif

        <!-- Customer & Event Info -->
        <table class="info-table">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                    <div class="section">
                        <div class="section-title">{{ __('Customer Information') }}</div>
                        <table style="width: 100%;">
                            <tr>
                                <td>
                                    <div class="info-label">{{ __('Name') }}</div>
                                    <div class="info-value">{{ $quotation->name }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="info-label">{{ __('Email') }}</div>
                                    <div class="info-value">{{ $quotation->email }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="info-label">{{ __('Phone') }}</div>
                                    <div class="info-value">{{ $quotation->phone }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div class="section">
                        <div class="section-title">{{ __('Event Details') }}</div>
                        <table style="width: 100%;">
                            <tr>
                                <td>
                                    <div class="info-label">{{ __('Event Type') }}</div>
                                    <div class="info-value">{{ $quotation->event_type_label }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="info-label">{{ __('Event Date') }}</div>
                                    <div class="info-value">{{ $quotation->event_date->format('F d, Y') }}{{ $quotation->event_time ? ' at ' . \Carbon\Carbon::parse($quotation->event_time)->format('g:i A') : '' }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="info-label">{{ __('Number of Guests') }}</div>
                                    <div class="info-value">{{ $quotation->guest_count }} {{ __('persons') }}</div>
                                </td>
                            </tr>
                            @if($quotation->venue_address)
                                <tr>
                                    <td>
                                        <div class="info-label">{{ __('Venue') }}</div>
                                        <div class="info-value">{{ $quotation->venue_address }}</div>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <div class="section">
            <div class="section-title">{{ __('Quotation Items') }}</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">#</th>
                        <th>{{ __('Description') }}</th>
                        <th class="text-center" style="width: 60px;">{{ __('Qty') }}</th>
                        <th class="text-right" style="width: 100px;">{{ __('Unit Price') }}</th>
                        <th class="text-right" style="width: 100px;">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($quotation->quotation_items && count($quotation->quotation_items) > 0)
                        @foreach($quotation->quotation_items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['description'] }}</td>
                                <td class="text-center">{{ $item['quantity'] }}</td>
                                <td class="text-right">{{ currency($item['unit_price']) }}</td>
                                <td class="text-right">{{ currency($item['total'] ?? $item['quantity'] * $item['unit_price']) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>

            <!-- Totals -->
            <table class="totals-table">
                <tr class="subtotal-row">
                    <td>{{ __('Subtotal') }}</td>
                    <td class="text-right">{{ currency($quotation->quotation_subtotal) }}</td>
                </tr>
                @if($quotation->quotation_discount > 0)
                    <tr>
                        <td class="discount-text">{{ __('Discount') }} @if($quotation->quotation_discount_type === 'percentage')({{ $quotation->quotation_discount }}%)@endif</td>
                        <td class="text-right discount-text">-{{ currency($quotation->quotation_discount_type === 'percentage' ? ($quotation->quotation_subtotal * $quotation->quotation_discount / 100) : $quotation->quotation_discount) }}</td>
                    </tr>
                @endif
                @if($quotation->quotation_tax_rate > 0)
                    <tr>
                        <td>{{ __('Tax') }} ({{ $quotation->quotation_tax_rate }}%)</td>
                        <td class="text-right">{{ currency($quotation->quotation_tax_amount) }}</td>
                    </tr>
                @endif
                @if($quotation->quotation_delivery_fee > 0)
                    <tr>
                        <td>{{ __('Delivery Fee') }}</td>
                        <td class="text-right">{{ currency($quotation->quotation_delivery_fee) }}</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>{{ __('Grand Total') }}</td>
                    <td class="text-right">{{ currency($quotation->quoted_amount) }}</td>
                </tr>
            </table>
        </div>

        <div class="clearfix"></div>

        <!-- Notes & Terms -->
        @if($quotation->quotation_notes || $quotation->quotation_terms)
            <div class="notes-section">
                @if($quotation->quotation_notes)
                    <h4>{{ __('Notes') }}</h4>
                    <p>{{ $quotation->quotation_notes }}</p>
                @endif
                @if($quotation->quotation_notes && $quotation->quotation_terms)
                    <br>
                @endif
                @if($quotation->quotation_terms)
                    <h4>{{ __('Terms & Conditions') }}</h4>
                    <p>{{ $quotation->quotation_terms }}</p>
                @endif
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('Thank you for your business!') }}</p>
            <p>{{ __('This is a computer-generated document. No signature is required.') }}</p>
        </div>
    </div>
</body>
</html>
