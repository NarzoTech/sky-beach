@extends('admin.layouts.master')
@section('title')
    <title>{{ __('Order Details') }}</title>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">


            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive text-center">
                                    <table class="table table-striped">

                                        <tr>
                                            <td>{{ __('Name') }}</td>
                                            <td><a
                                                    href="{{ route('admin.customer-show', $order->user_id) }}">{{ $order?->user?->name }}</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ __('Price') }}</td>
                                            <td>{{ currency($order->total_amount) }}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ __('Payment Method') }}</td>
                                            <td>{{ $order->payment_method }}</td>
                                        </tr>

                                        <tr>
                                            <td>{{ __('Payment Status') }}</td>
                                            <td>
                                                @if ($order->payment_status == 'success')
                                                    <div class="badge badge-success">{{ __('Success') }}</div>
                                                @elseif ($order->payment_status == 'rejected')
                                                    <div class="badge badge-danger">{{ __('Rejected') }}</div>
                                                @else
                                                    <div class="badge badge-danger">{{ __('Pending') }}</div>
                                                @endif
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>{{ __('Status') }}</td>
                                            <td>
                                                @if ($order->order_status == 'success')
                                                    <div class="badge badge-success">{{ __('Success') }}</div>
                                                @else
                                                    <div class="badge badge-danger">{{ __('Pending') }}</div>
                                                @endif
                                            </td>

                                        </tr>

                                        <tr>
                                            <td>{{ __('Transaction') }}</td>
                                            <td>{!! nl2br($order->transaction_id) !!}</td>
                                        </tr>

                                    </table>
                                </div>

                                @if ($order->payment_status == 'pending')
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#rejectPayment"
                                        class="btn btn-danger">{{ __('Make reject payment') }}</a>
                                @endif

                                @if ($order->payment_status == 'rejected' || $order->payment_status == 'pending')
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#approvePayment"
                                        class="btn btn-success">{{ __('Make approved payment') }}</a>
                                @endif


                                <a href="" data-url="{{ route('admin.order-delete', $order->id) }}"
                                    class="btn btn-danger delete">{{ __('Delete Order') }}</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="delete">
        <div class="modal-dialog" role="document">
            <form action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Delete Order') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger">{{ __('Are You Sure to Delete this order ?') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!--Payment Reject Modal -->
    <div class="modal fade" id="rejectPayment" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Payment Reject') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="{{ route('admin.order-payment-reject', $order->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="">{{ __('Subject') }}</label>
                                <input type="text" name="subject" class="form-control">
                            </div>
                            @php($default_value = '[[name]]')
                            <div class="form-group">
                                <label for="">{{ __('Description') }} <span data-bs-toggle="tooltip"
                                        data-placement="top" class="fa fa-info-circle text--primary"
                                        title="Don't remove the [[name]] keyword, user name will be dynamic using it">
                                </label>
                                <textarea name="description" class="form-control text-area-5" cols="30" rows="10">{{ 'Dear ' . $default_value }}</textarea>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Data') }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!--Payment Approved Modal -->
    <div class="modal fade" id="approvePayment" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Payment Approved') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form action="{{ route('admin.order-payment-approved', $order->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="">{{ __('Subject') }}</label>
                                <input type="text" name="subject" class="form-control">
                            </div>
                            @php($default_value = '[[name]]')
                            <div class="form-group">
                                <label for="">{{ __('Description') }} <span data-bs-toggle="tooltip"
                                        data-placement="top" class="fa fa-info-circle text--primary"
                                        title="Don't remove the [[name]] keyword, user name will be dynamic using it">
                                </label>
                                <textarea name="description" class="form-control text-area-5" cols="30" rows="10">{{ 'Dear ' . $default_value }}</textarea>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Data') }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>




    @push('js')
        <script>
            $(function() {
                'use strict'

                $('.delete').on('click', function(e) {
                    e.preventDefault();
                    const modal = $('#delete');
                    modal.find('form').attr('action', $(this).data('url'));
                    modal.modal('show');
                })
            })
        </script>
    @endpush
@endsection
