<section class="page">
    <div class="row justify-content-between">
        <div class="col-5">
            <div>
                <div>
                    <p class="title">{{ $setting->app_name }}</p>
                    <div class="property">

                        <span class="value">
                            <p>{{ $setting->address }}</p>
                        </span>
                    </div>

                    <div class="property">
                        <span class="key">Mobile:</span>
                        <span class="value">
                            {{ $setting->mobile }}
                        </span>
                    </div>
                    <div class="property">
                        <span class="key">Email:</span>
                        <span class="value">{{ $setting->email }}</span>
                    </div>




                </div>
                <div class="property">
                    <span class="key">Sold By:</span>
                    <span class="value">{{ $sale->createdBy->name }}</span>
                </div>
                <div class="property">
                    <span class="value">
                        <span class="key">Remark:</span>
                        {{ $sale->notes }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-5">
            <div>
                <p class="title" style="font-weight: 600;">Invoice</p>
                <div class="property">
                    <span class="value">
                        Invoice No:
                    </span>
                    <span class="value" style="font-weight: bold">
                        {{ $sale->invoice }}
                    </span>
                </div>
                <div class="property">
                    <span class="value">
                        Date:
                    </span>
                    <span class="value">
                        {{ formatDate($sale->order_date) }}
                    </span>
                </div>
                <div class="property">
                    <span class="value">
                        Time:
                    </span>
                    <span class="value">
                        {{ formatDate($sale->created_at, 'h:i A') }}
                    </span>
                </div>

                <p class="billing-badge">Billing To</p>
                <div class="property">
                    <span class="key">
                        Name:
                    </span>
                    <span class="value">
                        {{ $sale->customer->name ?? 'Guest' }}
                    </span>
                </div>


                <div class="property">
                    <span class="key">
                        Mobile:
                    </span>
                    <span class="value">
                        {{ $sale->customer->phone ?? '' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%; border-left: none !important; border-right: none !important;"
                        class="text-center">
                        SL.
                    </th>
                    <th style="width: 5%; border-left: none !important; border-right: none !important; padding-left: 3px;"
                        class="text-left">
                        Item
                    </th>
                    <th style="width: 40%; border-left: none !important; border-right: none !important;"
                        class="text-left">

                    </th>
                    <th style="width: 10%; border-left: none !important; border-right: none !important; text-align:center"
                        class="text-center">
                        Warranty
                    </th>
                    <th style="width: 10%; border-left: none !important; border-right: none !important;"
                        class="text-center">
                        Price
                    </th>
                    <th style="width: 15%; border-left: none !important; border-right: none !important;"
                        class="text-center">

                        Quantity
                    </th>
                    <th style="width: 15%; border-left: none !important; border-right: none !important;"
                        class="text-right">
                        Total
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->products as $index => $details)
                    <tr>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center">
                            {{ $index + 1 }}
                        </td>
                        <td
                            style="border-left: none !important; border-right: none !important; border-top: none !important;">
                            <img src="{{ asset($details->product->singleImage) }}"
                                style="width: 30px; height: 30px;" />
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-left">
                            {{ $details->product->name }}
                        </td>

                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center">
                            {{ $details->product->warranty ?? 'N/A' }}
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center">
                            {{ $details->price }}
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center qty" id="qty1" data-qty="1">
                            {{ $details->quantity }} {{ $details->product?->unit->name ?? '' }}
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-right" id="totalPriceInvoice1">
                            {{ $details->sub_total }}
                        </td>
                    </tr>
                @endforeach
                @foreach ($sale->services as $index => $details)
                    <tr>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center">
                            {{ $index + 1 }}
                        </td>
                        <td
                            style="border-left: none !important; border-right: none !important; border-top: none !important;">
                            <img src="{{ asset($details->service->singleImage) }}"
                                style="width: 30px; height: 30px;" />
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-left">
                            {{ $details->service->name }}
                        </td>

                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center">
                            N/A
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center">
                            {{ $details->price }}
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center qty" id="qty1" data-qty="1">
                            {{ $details->quantity }}
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-right" id="totalPriceInvoice1">
                            {{ $details->sub_total }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="5"
                        style="border-left: none !important; border-right: none !important; border-top: none !important"
                        class="text-right">
                        Total Qty:
                    </td>
                    <td colspan="1"
                        style="border-left: none !important; border-right: none !important; border-top: none !important"
                        class="text-center">
                        @php
                            $unitName = $details->product->unit->name;
                            $unitQty = isset($unit[$unitName]) ? $unit[$unitName] : 0;
                            $newQty = $details->quantity + $unitQty;
                            $unit[$unitName] = $newQty;

                        @endphp
                        {{ $sale->quantity }} {{ $unitName }}
                    </td>
                </tr>
            </tbody>
        </table>


        <div class="row">
            <div class="col-6">
                <div class="invoice-watermark">
                </div>
            </div>
            <div class="col-6">
                <table class="summary-table" style="margin-bottom: 10px">
                    <tbody>
                        <tr>
                            <td colspan="5" style="border: none !important">
                            </td>
                            <td class="text-right ps-0 pb-0"
                                style="border:none !important; border-bottom: 1px solid #fff !important">
                                <b>Subtotal :</b>
                            </td>
                            @php
                                $subTotal = array_sum($sale->details->pluck('sub_total')->toArray());
                            @endphp
                            <td class="text-right pb-0"
                                style="border:none !important; border-bottom: 1px solid #fff !important;">
                                <b>TK
                                    {{ $subTotal }}</b>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" style="border: none !important"></td>
                            <td class="text-right  ps-0"
                                style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important ">
                                Discount:</td>
                            <td class="text-right"
                                style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important ">
                                TK
                                {{ $sale->order_discount }}
                            </td>
                        </tr>
                        {{-- <tr>
                                        <td colspan="5" style="border: none !important"></td>
                                        <td class="text-right  ps-0"
                                            style="border:none !important; border-bottom: 1px solid #fff !important">
                                            Previous Due:</td>
                                        <td class="text-right"
                                            style="border:none !important; border-bottom: 1px solid #fff !important;">
                                            TK
                                            {{ $sale->customer->due->sum('due_amount') }}
                                        </td>
                                    </tr> --}}


                        <tr>
                            <td colspan="5" style="border: none !important"></td>
                            <td class="text-right ps-0 pb-0"
                                style="border:none !important; border-bottom: 1px solid #fff !important">
                                <b>Total:</b>
                            </td>
                            <td class="text-right pb-0"
                                style="border:none !important; border-bottom: 1px solid #fff !important;">

                                <b>TK
                                    {{ $subTotal - $sale->order_discount }}</b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="border: none !important"></td>
                            <td class="text-right ps-0"
                                style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important ">
                                Paid:
                            </td>
                            <td class="text-right"
                                style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important">
                                TK
                                {{ $sale->paid_amount }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" style="border: none !important">
                            </td>
                            <td class="text-right ps-0">

                                Due:
                            </td>

                            <td class="text-right">
                                TK {{ $sale->due_amount }}
                            </td>
                        </tr>


                        {{-- @if ($sale->customer->due->count())
                            <tr>
                                <td colspan="5" style="border: none !important">
                                </td>
                                <td class="text-right ps-0"
                                    style="border:none !important; border-bottom: 1px solid #fff !important">
                                    Due Remaining:
                                </td>

                                <td class="text-right"
                                    style="border:none !important; border-bottom: 1px solid #fff !important;">
                                    TK {{ $sale->customer->due->sum('due_amount') }}
                                </td>
                            </tr>
                        @endif --}}
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3 payment-details">
            <span class="block bold" style="font-size: 12px">
                <b>
                    <span style="font-weight: bold; letter-spacing: 0.1px; font-size: 13px;">
                        In Words:
                    </span>
                    {{ numberToWord($sale->grand_total) }} TK
                    Only


                </b>
            </span>
        </div>
        <div class="d-flex justify-content-between" style="margin-top: 80px">
            <div>
                <p class="signature">
                    Received By
                </p>
            </div>
            <div>
            </div>
            <div>
                <p class="signature">
                    Authorised By
                </p>
            </div>
        </div>
    </div>
    @if (!isRoute('admin.place-order'))
        <div class="print-btn pos-share-btns d-print-none">
            <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light">
                <i class="fa fa-print"></i> Print
            </a>
        </div>
    @endif
</section>



@if (request()->print)
    <script>
        window.print();
    </script>
@endif
