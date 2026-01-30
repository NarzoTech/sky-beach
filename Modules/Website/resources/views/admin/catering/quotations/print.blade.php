<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Quotation') }} - {{ $quotation->quotation_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #003366;
        }
        .company-info {
            flex: 1;
        }
        .company-logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 12px;
            color: #666;
        }
        .quotation-info {
            text-align: right;
        }
        .quotation-title {
            font-size: 28px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 10px;
        }
        .quotation-number {
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .quotation-date {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 8px;
        }
        .status-quoted { background: #e3f2fd; color: #1976d2; }
        .status-confirmed { background: #e8f5e9; color: #388e3c; }
        .status-pending { background: #fff3e0; color: #f57c00; }
        .status-cancelled { background: #ffebee; color: #d32f2f; }

        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #003366;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        thead th {
            background: #003366;
            color: #fff;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tbody tr:hover {
            background: #f8f9fa;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .totals-table {
            width: 300px;
            margin-left: auto;
        }
        .totals-table td {
            padding: 8px 12px;
            border: none;
        }
        .totals-table .subtotal td {
            border-top: 1px solid #ddd;
        }
        .totals-table .total td {
            background: #003366;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
        }
        .discount-row { color: #28a745; }

        .notes-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .notes-section h4 {
            font-size: 12px;
            color: #003366;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .notes-section p {
            font-size: 12px;
            color: #666;
            white-space: pre-line;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #888;
        }
        .validity-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #856404;
        }
        .validity-notice.expired {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        @media print {
            body {
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .container {
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                @if($setting && $setting->logo)
                    <img src="{{ asset($setting->logo) }}" alt="Logo" class="company-logo">
                @endif
                <div class="company-name">{{ $setting->app_name ?? config('app.name') }}</div>
                <div class="company-details">
                    @if($setting)
                        {{ $setting->address }}<br>
                        {{ __('Tel') }}: {{ $setting->mobile }}<br>
                        {{ __('Email') }}: {{ $setting->email }}
                    @endif
                </div>
            </div>
            <div class="quotation-info">
                <div class="quotation-title">{{ __('QUOTATION') }}</div>
                <div class="quotation-number">{{ $quotation->quotation_number }}</div>
                <div class="quotation-date">{{ __('Date') }}: {{ $quotation->quoted_at ? $quotation->quoted_at->format('F d, Y') : $quotation->created_at->format('F d, Y') }}</div>
                <span class="status-badge status-{{ $quotation->status }}">{{ $quotation->status_label }}</span>
            </div>
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
        <div class="info-grid">
            <div class="section">
                <div class="section-title">{{ __('Customer Information') }}</div>
                <div class="info-item">
                    <div class="info-label">{{ __('Name') }}</div>
                    <div class="info-value">{{ $quotation->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('Email') }}</div>
                    <div class="info-value">{{ $quotation->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('Phone') }}</div>
                    <div class="info-value">{{ $quotation->phone }}</div>
                </div>
            </div>
            <div class="section">
                <div class="section-title">{{ __('Event Details') }}</div>
                <div class="info-item">
                    <div class="info-label">{{ __('Event Type') }}</div>
                    <div class="info-value">{{ $quotation->event_type_label }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('Event Date') }}</div>
                    <div class="info-value">{{ $quotation->event_date->format('F d, Y') }}{{ $quotation->event_time ? ' at ' . \Carbon\Carbon::parse($quotation->event_time)->format('g:i A') : '' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">{{ __('Number of Guests') }}</div>
                    <div class="info-value">{{ $quotation->guest_count }} {{ __('persons') }}</div>
                </div>
                @if($quotation->venue_address)
                    <div class="info-item">
                        <div class="info-label">{{ __('Venue') }}</div>
                        <div class="info-value">{{ $quotation->venue_address }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="section">
            <div class="section-title">{{ __('Quotation Items') }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>{{ __('Description') }}</th>
                        <th class="text-center" style="width: 80px;">{{ __('Qty') }}</th>
                        <th class="text-right" style="width: 120px;">{{ __('Unit Price') }}</th>
                        <th class="text-right" style="width: 120px;">{{ __('Total') }}</th>
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
                <tr class="subtotal">
                    <td>{{ __('Subtotal') }}</td>
                    <td class="text-right">{{ currency($quotation->quotation_subtotal) }}</td>
                </tr>
                @if($quotation->quotation_discount > 0)
                    <tr class="discount-row">
                        <td>{{ __('Discount') }} @if($quotation->quotation_discount_type === 'percentage')({{ $quotation->quotation_discount }}%)@endif</td>
                        <td class="text-right">-{{ currency($quotation->quotation_discount_type === 'percentage' ? ($quotation->quotation_subtotal * $quotation->quotation_discount / 100) : $quotation->quotation_discount) }}</td>
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
                <tr class="total">
                    <td>{{ __('Grand Total') }}</td>
                    <td class="text-right">{{ currency($quotation->quoted_amount) }}</td>
                </tr>
            </table>
        </div>

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

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
