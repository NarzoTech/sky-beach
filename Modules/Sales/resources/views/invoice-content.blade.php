<section class="page">
    <div class="row justify-content-between">
        <div class="col-5">
            <div>
                <div>
                    <p class="title">{{ $setting->app_name ?? 'Company Name' }}</p>
                    <div class="property">

                        <span class="value">
                            <p>{{ $setting->address ?? '' }}</p>
                        </span>
                    </div>

                    <div class="property">
                        <span class="key">Mobile:</span>
                        <span class="value">
                            {{ $setting->mobile ?? '' }}
                        </span>
                    </div>
                    <div class="property">
                        <span class="key">Email:</span>
                        <span class="value">{{ $setting->email ?? '' }}</span>
                    </div>




                </div>
                <div class="property">
                    <span class="key">Sold By:</span>
                    <span class="value">{{ $sale->createdBy->name ?? 'Staff' }}</span>
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
                    <th style="width: 45%; border-left: none !important; border-right: none !important; padding-left: 3px;"
                        class="text-left" colspan="2">
                        Item
                    </th>
                    <th style="width: 15%; border-left: none !important; border-right: none !important;"
                        class="text-center">
                        Price
                    </th>
                    <th style="width: 15%; border-left: none !important; border-right: none !important;"
                        class="text-center">

                        Quantity
                    </th>
                    <th style="width: 20%; border-left: none !important; border-right: none !important;"
                        class="text-right">
                        Total
                    </th>
                </tr>
            </thead>
            <tbody>
                @php $index = 0; @endphp
                @foreach ($sale->details as $detail)
                    @php
                        $index++;
                        // Determine item name and image based on type
                        $itemName = 'Unknown Item';
                        $itemImage = null;

                        if ($detail->menu_item_id && $detail->menuItem) {
                            $itemName = $detail->menuItem->name;
                            $itemImage = $detail->menuItem->image;
                        } elseif ($detail->service_id && $detail->service) {
                            $itemName = $detail->service->name;
                            $itemImage = $detail->service->singleImage ?? null;
                        } elseif ($detail->ingredient_id && $detail->product) {
                            $itemName = $detail->product->name;
                            $itemImage = $detail->product->singleImage ?? null;
                        }
                    @endphp
                    <tr>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center">
                            {{ $index }}
                        </td>
                        <td colspan="2"
                            style="border-left: none !important; border-right: none !important; border-top: none !important;"
                            class="text-left">
                            {{ $itemName }}
                            @if($detail->attributes)
                                <br><small class="text-muted">{{ $detail->attributes }}</small>
                            @endif
                            @if(!empty($detail->addons))
                                <br><small class="text-info">+ @foreach($detail->addons as $addon){{ $addon['name'] }}@if(!$loop->last), @endif @endforeach</small>
                            @endif
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center">
                            {{ currency($detail->price) }}
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-center qty">
                            {{ $detail->quantity }}
                        </td>
                        <td style="border-left: none !important; border-right: none !important; border-top: none !important"
                            class="text-right">
                            {{ currency($detail->sub_total) }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4"
                        style="border-left: none !important; border-right: none !important; border-top: none !important"
                        class="text-right">
                        Total Qty:
                    </td>
                    <td colspan="2"
                        style="border-left: none !important; border-right: none !important; border-top: none !important"
                        class="text-center">
                        {{ $sale->quantity }}
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
                            <td class="text-right pb-0"
                                style="border:none !important; border-bottom: 1px solid #fff !important;">
                                <b>{{ currency($sale->total_price) }}</b>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" style="border: none !important"></td>
                            <td class="text-right  ps-0"
                                style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important ">
                                Discount:</td>
                            <td class="text-right"
                                style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important ">
                                {{ currency($sale->order_discount) }}
                            </td>
                        </tr>

                        @if($sale->total_tax > 0)
                        <tr>
                            <td colspan="5" style="border: none !important"></td>
                            <td class="text-right  ps-0"
                                style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important ">
                                Tax:</td>
                            <td class="text-right"
                                style="border:none !important; border-bottom: 1px solid rgb(136 136 136) !important ">
                                {{ currency($sale->total_tax) }}
                            </td>
                        </tr>
                        @endif

                        <tr>
                            <td colspan="5" style="border: none !important"></td>
                            <td class="text-right ps-0 pb-0"
                                style="border:none !important; border-bottom: 1px solid #fff !important">
                                <b>Total:</b>
                            </td>
                            <td class="text-right pb-0"
                                style="border:none !important; border-bottom: 1px solid #fff !important;">

                                <b>{{ currency($sale->grand_total) }}</b>
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
                                {{ currency($sale->paid_amount) }}
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" style="border: none !important">
                            </td>
                            <td class="text-right ps-0">

                                Due:
                            </td>

                            <td class="text-right">
                                {{ currency($sale->due_amount) }}
                            </td>
                        </tr>
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
                    {{ numberToWord($sale->grand_total) }} {{ $setting->currency_name ?? 'TK' }}
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
