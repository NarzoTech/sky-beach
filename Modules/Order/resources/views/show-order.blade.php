@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Invoice') }}</title>
@endsection
<style>
    @media print {

        .section-header,
        .order-status,
        #sidebar-wrapper,
        .print-area,
        .main-footer,
        .additional_info {
            display: none !important;
        }

    }
</style>
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">

            <div class="section-body">
                <div class="invoice">
                    <div class="invoice-print">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="invoice-title">
                                    <h2><img src="{{ asset($setting->logo) }}" alt="" width="120px"></h2>
                                    <div class="invoice-number">{{ __('Order') }} #{{ $order->order_id }}</div>
                                </div>
                                <hr>
                                @php
                                    // $orderAddress = $order->orderAddress;
                                @endphp
                                <div class="row">
                                    <div class="col-md-6">
                                        <address>
                                            <strong>{{ __('Billing Information') }}:</strong><br>
                                            {{-- {{ $orderAddress->billing_name }}<br>
                            @if ($orderAddress->billing_email)
                            {{ $orderAddress->billing_email }}<br>
                            @endif
                            @if ($orderAddress->billing_phone)
                            {{ $orderAddress->billing_phone }}<br>
                            @endif
                            {{ $orderAddress->billing_address }},
                            {{ $orderAddress->billing_city.', '. $orderAddress->billing_state.', '.$orderAddress->billing_country }}<br> --}}
                                        </address>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <address>
                                            <strong>{{ __('Shipping Information') }} :</strong><br>
                                            {{ $order->customer_name }}<br>
                                            @if ($order->phone)
                                                {{ $order->phone }}<br>
                                            @endif
                                            {{ $order->address }},
                                        </address>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <address>
                                            <strong>{{ __('Payment Information') }}:</strong><br>
                                            {{ __('Method') }}: {{ allPaymentMethods($order->payment_method) }}<br>
                                            {{ __('Status') }} :
                                            @if ($order->payment_status == 'success')
                                                <span class="badge badge-success">{{ __('Success') }}</span>
                                            @elseif($order->payment_status == 'pending')
                                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('Cancelled') }}</span>
                                            @endif <br>

                                            @if (!$order->created_by)
                                                {{ __('Transaction') }}: {!! clean(nl2br($order->transection_id)) !!}
                                            @else
                                                {{ __('Payment Details') }}: {!! clean(nl2br($order->payment_details)) !!}
                                            @endif
                                        </address>
                                    </div>
                                    <div class="col-md-6 text-md-right">
                                        <address>
                                            <strong>{{ __('Order Information') }}:</strong><br>
                                            {{ __('Date') }}: {{ formatDate($order->created_at) }}<br>
                                            {{ __('Shipping') }}:
                                            {{ $order->delivery_method == 1 ? __('Delivery') : __('Pick up') }}<br>
                                            {{ __('Status') }} :
                                            <span
                                                class="badge badge-{{ statusColor($order->delivery_status) }}">{{ getOrderStatus($order->delivery_status) }}
                                            </span>
                                        </address>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="section-title">{{ __('Order Summary') }}</div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-md">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="25%">{{ __('Product') }}</th>
                                            <th width="20%">{{ __('Variant') }}</th>
                                            <th width="10%" class="text-center">{{ __('Unit Price') }}</th>
                                            <th width="10%" class="text-center">{{ __('Quantity') }}</th>
                                            <th width="10%" class="text-right">{{ __('Total') }}</th>
                                        </tr>
                                        @php
                                            $subTotal = 0;
                                        @endphp
                                        @foreach ($order->orderDetails as $index => $details)
                                            <tr>
                                                <td>{{ ++$index }}</td>
                                                <td><a href="">{{ $details->product->name }}</a></td>
                                                <td>
                                                    {{ $details->attributes }}
                                                </td>
                                                <td class="text-center">{{ currency($details->price) }}</td>
                                                <td class="text-center">{{ $details->quantity }}</td>
                                                @php
                                                    $total = $details->price * $details->quantity;
                                                @endphp
                                                <td class="text-right">{{ currency($total) }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                @if ($order->order_note)
                                    <div class="row additional_info">
                                        <div class="col">
                                            <hr>
                                            <h5>{{ __('Additional Information') }}: </h5>
                                            <p>{!! clean(nl2br($order->order_note)) !!}</p>
                                            <hr>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mt-3">
                                    <div class="col-lg-6 order-status">
                                        <div class="section-title">{{ __('Order Status') }}</div>

                                        @php
                                            $status = [
                                                1 => 'Pending',
                                                2 => 'Accepted',
                                                3 => 'Progress',
                                                4 => 'On the way',
                                                5 => 'Delivered',
                                                6 => 'Cancelled',
                                            ];
                                            if ($order->delivery_method == 2) {
                                                $status[4] = 'Ready for Pickup';
                                                $status[5] = 'Picked Up';
                                            }

                                            if ($order->delivery_status == 5) {
                                                // remove last item
                                                array_pop($status);
                                            }

                                            // payment status
                                            $paymentStatus = ['pending', 'success', 'rejected'];

                                            if ($order->payment_status == 'success') {
                                                $paymentStatus = ['success'];
                                            }
                                            if (
                                                $order->payment_status == 'rejected' ||
                                                $order->payment_status == 'cancelled'
                                            ) {
                                                $paymentStatus = [$order->payment_status];
                                            }

                                        @endphp
                                        <form action="javascript:;" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-body">

                                                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                                                    <div class="form-group">
                                                        <label for="delivery_status">{{ __('Order Status') }}</label>

                                                        <select name="delivery_status" class="form-control"
                                                            id="delivery_status">

                                                            @foreach ($status as $key => $sta)
                                                                @php
                                                                    if ($key < $order->delivery_status) {
                                                                        continue;
                                                                    }
                                                                @endphp

                                                                <option value="{{ $key }}">{{ $sta }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="payment_status">{{ __('Payment Status') }}</label>
                                                        <select name="payment_status" class="form-control"
                                                            id="payment_status">
                                                            @foreach ($paymentStatus as $payment)
                                                                <option class="text-capitalize"
                                                                    value="{{ $payment }}">
                                                                    {{ ucfirst($payment) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group d-none cancel_note">
                                                        <label for="cancel_note">{{ __('Cancel Note') }}<span
                                                                class="text-danger">*</span></label>
                                                        <textarea name="cancel_note" class="form-control height_50" id="cancel_note"></textarea>
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success"
                                                        id="update">{{ __('Update') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-lg-6 text-right">
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">{{ __('Subtotal') }} :
                                                {{ currency($order->order_amount) }}</div>
                                        </div>
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">{{ __('Discount') }}(-) :
                                                {{ currency($order->discount) }}</div>
                                        </div>
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">{{ __('Shipping') }} :
                                                {{ currency($order->delivery_fee) }}</div>
                                        </div>
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">{{ __('Tax') }} :
                                                {{ currency($order->tax) }}</div>
                                        </div>

                                        <hr class="mt-2 mb-2">
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-value invoice-detail-value-lg">{{ __('Total') }} :
                                                {{ currency($order->total_amount) }}</div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-md-right print-area">
                        <hr>
                        <button class="btn btn-success btn-icon icon-left" onclick="window.print()"><i
                                class="fas fa-print"></i>
                            {{ __('Print') }}</button>
                        <button class="btn btn-danger btn-icon icon-left" data-bs-toggle="modal"
                            data-bs-target="#deleteModal" onclick="deleteData({{ $order->order_id }})"><i
                                class="fas fa-times"></i>
                            {{ __('Delete') }}</button>
                    </div>
                </div>
            </div>

        </section>
    </div>
    @include('components.admin.preloader')
    <script>
        function deleteData(id) {
            $("#deleteForm").attr("action", "{{ route('admin.order-delete', '') }}" + "/" + id)
        }
    </script>
@endsection


@push('js')
    <script>
        'use strict';

        $(document).ready(function() {
            $('#delivery_status').on('change', function() {
                if ($(this).val() == 6) {
                    $('.cancel_note').removeClass('d-none');
                } else {
                    $('.cancel_note').addClass('d-none');
                }
            })

            $("#update").on('click', function(e) {
                $('.preloader_area').removeClass('d-none');
                e.preventDefault()
                var status = $('#delivery_status').val();
                if (status == 6) {
                    var cancel_note = $('#cancel_note').val();
                    if (cancel_note == '') {
                        toastr.error('Cancel note is required');
                        $('.preloader_area').addClass('d-none');
                        return;
                    }
                }
                var orderId = $('[name="order_id"]').val();
                const payment = $('#payment_status').val();
                $.ajax({
                    url: "{{ route('admin.order.status') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status,
                        orderId: orderId,
                        payment: payment,
                        cancel_note: cancel_note
                    },
                    success: function(response) {
                        if (response.error) {
                            toastr.error(response.error);
                            $('.preloader_area').addClass('d-none');
                            return;
                        }
                        toastr.success(response.success);
                        $('.preloader_area').addClass('d-none');
                        location.reload()
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        $('.preloader_area').addClass('d-none');
                    }
                });
            });
        })
    </script>
@endpush
