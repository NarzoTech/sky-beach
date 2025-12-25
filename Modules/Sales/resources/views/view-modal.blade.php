<div class="modal-header card-header">
    <h4 class="section_title">View Sale</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body pt-0">

    <div class="row invoice-info">
        <div class="col-lg-3 invoice-col">
            <span style="display: none;">{{ __('Business') }}:</span>
            <address>
                <strong>{{ $setting->app_name }}</strong>
            </address>
        </div>

        <div class="col-lg-3 invoice-col">
            <b>{{ __('Customer') }}:</b>
            <p>{{ $sale?->customer?->name ?? 'Guest' }}</p>
            <p>{{ $sale?->customer?->phone ? 'Mobile: ' . $sale->customer->phone : '' }}</p>
            <p>{{ $sale?->customer?->sale_note ? 'Remark: ' . $sale->customer->sale_note : '' }}</p>
        </div>

        <div class="col-lg-6 invoice-col">
            <b class="me-2">{{ __('Invoice No') }}:</b> {{ $sale->invoice }}<br>
            <b class="me-2">{{ __('Date') }}:</b>{{ formatDate($sale->order_date) }}<br>
            <b class="me-2">{{ __('Created By') }}</b>: {{ $sale->createdBy->name }} <br>
            <b class="me-2">{{ __('Created At') }}</b>{{ __(':') }}
            {{ formatDate($sale->created_at, 'd-m-Y h:i A') }}
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-sm-12 col-xs-12">
            <div class="table-responsive text-center">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('SL') }}</th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Product Name') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Unit Price') }}</th>
                            <th>{{ __('Total') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $subTotal = 0;
                        @endphp
                        @foreach ($sale->details as $index => $details)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if ($details->product_id)
                                        <a href="{{ asset($details->product->single_image) }}">
                                            <img style="height: 40px; width: 40px;"
                                                src='{{ asset($details->product->single_image) }}' alt="Image">
                                        </a>
                                    @else
                                        <a href="{{ asset($details->service->single_image) }}">
                                            <img style="height: 40px; width: 40px;"
                                                src='{{ asset($details->service->single_image) }}' alt="Image">
                                    @endif
                                    </a>
                                </td>
                                <td>
                                    @if ($details->product_id)
                                        @if ($details->source == 2)
                                            Other Income (Parts-Local market)
                                        @else
                                            {{ $details->product->name }}
                                        @endif
                                    @else
                                        {{ $details->service->name }}
                                    @endif
                                </td>
                                <td>
                                    {{ $details->quantity }}
                                    @if ($details->product_id)
                                        {{ $details->product?->unit?->name }}
                                    @endif
                                </td>
                                <td>{{ $details->price }}</td>
                                @php
                                    $subTotal += $details->sub_total;
                                @endphp
                                <td>{{ $details->sub_total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12 col-xs-12">
        </div>

        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>{{ __('Sub Total') }}: </th>
                            <td><span class="display_currency pull-right"
                                    data-currency_symbol="true">{{ currency($subTotal) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Discount') }}: </th>
                            <td><span class="display_currency pull-right"
                                    data-currency_symbol="true">{{ currency($sale->order_discount) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Total Amount') }}: </th>
                            <td><span class="display_currency pull-right"
                                    data-currency_symbol="true">{{ currency($sale->total_price) }}
                                </span></td>
                        </tr>
                        <tr>
                            <th>{{ __('Total Pay') }}: </th>
                            <td><span class="display_currency pull-right"
                                    data-currency_symbol="true">{{ currency($sale->paid_amount) }}</span></td>
                        </tr>
                        <tr>
                            <th>{{ __('Final Due') }}: </th>
                            <td><span class="display_currency pull-right"
                                    data-currency_symbol="true">{{ currency($sale->due_amount) }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-danger no-print" data-bs-dismiss="modal">{{ __('Close') }}</button>
    </div>
</div>
