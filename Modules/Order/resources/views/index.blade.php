@extends('admin.layouts.master')
@section('title', $title)
@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr>
                                            <th width="5%">{{ __('SN') }}</th>
                                            <th width="10%">{{ __('Customer') }}</th>
                                            <th width="10%">{{ __('Order Id') }}</th>
                                            <th width="10%">{{ __('Date') }}</th>
                                            <th width="10%">{{ __('Quantity') }}</th>
                                            <th width="10%">{{ __('Amount') }}</th>
                                            <th width="10%">{{ __('Order Status') }}</th>
                                            <th width="10%">{{ __('Payment') }}</th>
                                            <th width="10%">{{ __('Created By') }}</th>
                                            <th width="15%">{{ __('Action') }}</th>
                                        </tr>
                                        @forelse ($orders as $index => $order)
                                            <tr>
                                                <td>{{ ++$index }}</td>
                                                <td>
                                                    @if ($order->walk_in_customer)
                                                        {{ __('Walk In Customer') }}
                                                    @else
                                                        {{ $order->user?->name }}
                                                    @endif
                                                </td>
                                                <td>{{ $order->order_id }}</td>
                                                <td>{{ formatDate($order->created_at) }}</td>
                                                <td>{{ $order->quantity }}</td>
                                                <td>{{ $order->amount }}</td>

                                                <td>
                                                    <span
                                                        class="badge badge-{{ statusColor($order->delivery_status) }}">{{ getOrderStatus($order->delivery_status) }}
                                                    </span>

                                                </td>
                                                <td>
                                                    @if ($order->payment_status == 'success')
                                                        <span class="badge badge-success">{{ __('success') }} </span>
                                                    @else
                                                        <span class="badge badge-danger">{{ __('Pending') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($order->createdBy)
                                                        {{ $order->createdBy?->name }}
                                                    @else
                                                        {{ __('Customer') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.order.show', $order->id) }}"
                                                        class="btn btn-primary btn-sm"><i class="fa fa-eye"
                                                            aria-hidden="true"></i></a>

                                                    <a href="javascript:;" data-bs-toggle="modal" {{-- data-bs-target="#deleteModal" --}}
                                                        class="btn btn-danger btn-sm"
                                                        onclick="deleteData({{ $order->order_id }})"><i class="fa fa-trash"
                                                            aria-hidden="true"></i></a>

                                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#status"
                                                        class="btn btn-warning btn-sm status-btn"
                                                        data-id="{{ $order->order_id }}"
                                                        data-status="{{ $order->delivery_status }}"
                                                        data-method="{{ $order->delivery_method }}"
                                                        data-payment="{{ $order->payment_status }}"><i class="fas fa-truck"
                                                            aria-hidden="true"></i></a>
                                                </td>
                                            </tr>
                                        @empty
                                            <x-empty-table :name="__('Customer')" route="" create="no" :message="__('No data found!')"
                                                colspan="7"></x-empty-table>
                                        @endforelse
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('components.admin.preloader')

    <div class="modal fade" tabindex="-1" role="dialog" id="status">
        <div class="modal-dialog" role="document">
            <form action="javascript:;" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Status') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="order_id">
                        <div class="form-group">
                            <label for="delivery_status">{{ __('Order Status') }}</label>
                            <select name="delivery_status" class="form-control" id="delivery_status">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment_status">{{ __('Payment Status') }}</label>
                            <select name="payment_status" class="form-control" id="payment_status">
                            </select>
                        </div>
                        <div class="form-group d-none cancel_note">
                            <label for="cancel_note">{{ __('Cancel Note') }}<span class="text-danger">*</span></label>
                            <textarea name="cancel_note" class="form-control height_50" id="cancel_note"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="button" class="btn btn-success" id="update">{{ __('Update') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            'use strict'

            $(document).ready(function() {
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
                                $('#status').modal('hide');
                                $('.preloader_area').addClass('d-none');
                                return;
                            }
                            toastr.success(response.success);
                            $('#status').modal('hide');
                            $('.preloader_area').addClass('d-none');
                            location.reload()
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            $('.preloader_area').addClass('d-none');
                        }
                    });
                });

                $('.status-btn').on('click', function(e) {

                    // get the delivery method
                    const deliveryMethod = $(this).data('method');
                    e.preventDefault();
                    const delivery_status = {
                        1: 'Pending',
                        2: 'Accepted',
                        3: 'Progress',
                        5: 'Delivered',
                        6: 'Cancelled',
                    };
                    if (deliveryMethod == 1) {
                        delivery_status[4] = 'On the way';
                    }
                    if (deliveryMethod == 2) {
                        delivery_status[4] = 'Ready for Pickup';
                        delivery_status[5] = 'Picked Up';
                    }
                    const orderId = $(this).data('id');
                    const status = $(this).data('status');
                    const payment = $(this).data('payment');
                    $('[name="order_id"]').val(orderId);

                    $('#delivery_status').empty();
                    $.each(delivery_status, function(key, value) {
                        if (status <= key) {
                            let option =
                                `<option value="${key}" ${status == key ? 'selected' : ''}>${value}</option>`;
                            if (status >= 5) {
                                if (status == 5 && key == 5) {
                                    $('#delivery_status').html(option);
                                } else if (status == 6 && key == 6) {
                                    $('#delivery_status').html(option);
                                }
                            } else {
                                $('#delivery_status').append(option);
                            }
                        }
                    });
                    const paymentStatus = [
                        'pending', 'success', 'rejected'
                    ]

                    $('#payment_status').html('');
                    $.each(paymentStatus, function(key, value) {
                        let option =
                            `<option class="text-capitalize" value="${value}" ${payment == value ? 'selected' : ''}>${capitalizeFirstLetter(value)}</option>`;

                        if (payment == 'success') {
                            if (value == 'pending' || value == 'rejected') {
                                return;
                            }
                        }
                        if (payment == 'rejected') {
                            if (value == 'success' || value == 'pending') {
                                return;
                            }
                        }

                        $('#payment_status').append(option);
                    });
                })
                $('#delivery_status').on('change', function() {
                    if ($(this).val() == 6) {
                        $('.cancel_note').removeClass('d-none');
                    } else {
                        $('.cancel_note').addClass('d-none');
                    }
                })
            })

            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            function deleteData(id) {
                const modal = $('#deleteModal');
                $('#deleteForm').attr('action', "{{ url('admin/order-delete') }}/" + id);
                modal.modal('show');
            }
        </script>
    @endpush
@endsection
