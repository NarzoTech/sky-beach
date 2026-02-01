@extends('admin.layouts.master')
@section('title', $title)

@push('css')
<style>
/* Payment Modal CSS Variables */
:root {
    --pm-primary: #696cff;
    --pm-primary-light: #e7e7ff;
    --pm-success: #71dd37;
    --pm-success-light: #e8fadf;
    --pm-danger: #ff3e1d;
    --pm-danger-light: #ffe0db;
    --pm-info: #03c3ec;
    --pm-info-light: #d7f5fc;
    --pm-warning: #ffab00;
    --pm-warning-light: #fff2d6;
    --pm-dark: #233446;
    --pm-gray: #697a8d;
    --pm-gray-light: #f5f5f9;
    --pm-border: #e0e0e0;
    --pm-radius: 12px;
    --pm-radius-lg: 16px;
}

.payment-modal .modal-content {
    border: none;
    border-radius: var(--pm-radius-lg);
    overflow: hidden;
}

.payment-modal .header-dark {
    background: linear-gradient(135deg, #232333, #1a1a2e);
    color: white;
    padding: 16px 20px;
}

.payment-modal .modal-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 600;
}

.pm-section-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--pm-gray);
    margin-bottom: 12px;
}

.pm-divider {
    height: 1px;
    background: var(--pm-border);
    margin: 16px 0;
}

.pm-divider-dashed {
    height: 1px;
    border-top: 1px dashed var(--pm-border);
    margin: 16px 0;
}

.btn-complete-payment {
    background: linear-gradient(135deg, var(--pm-success), #5cb52a);
    color: white;
    border: none;
    padding: 12px 32px;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.btn-complete-payment:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(113, 221, 55, 0.4);
    color: white;
}

.rop-tax-section {
    background: var(--pm-gray-light);
    padding: 12px;
    border-radius: 8px;
}
.sales-stats-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    border: 1px solid #e9ecef;
}
.sales-stats-card .stat-value {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 5px;
}
.sales-stats-card .stat-label {
    font-size: 13px;
    color: #697a8d;
}
.sales-stats-card.total .stat-value { color: #696cff; }
.sales-stats-card.paid .stat-value { color: #71dd37; }
.sales-stats-card.due .stat-value { color: #ff3e1d; }

.status-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
.status-tab {
    padding: 10px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    border: 2px solid #e9ecef;
    background: #fff;
    color: #697a8d;
    transition: all 0.2s;
}
.status-tab:hover {
    border-color: #696cff;
    color: #696cff;
}
.status-tab.active {
    background: #696cff;
    border-color: #696cff;
    color: #fff;
}
.status-tab.active.due-tab {
    background: #ff3e1d;
    border-color: #ff3e1d;
}
.status-tab.active.paid-tab {
    background: #71dd37;
    border-color: #71dd37;
}
.status-tab .count {
    background: rgba(255,255,255,0.2);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    margin-left: 8px;
}

.sales-table {
    width: 100%;
}
.sales-table th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 13px;
    color: #566a7f;
    padding: 12px 15px;
    border-bottom: 2px solid #e9ecef;
}
.sales-table td {
    padding: 12px 15px;
    vertical-align: middle;
    border-bottom: 1px solid #f0f0f0;
}
.sales-table tbody tr:hover {
    background: #f8f9fa;
}

.invoice-cell {
    font-weight: 600;
    color: #696cff;
}
.customer-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}
.customer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e7e7ff;
    color: #696cff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
    flex-shrink: 0;
}
.customer-info .name {
    font-weight: 500;
    color: #566a7f;
}
.customer-info .meta {
    font-size: 12px;
    color: #a1acb8;
}

.amount-cell {
    text-align: right;
}
.amount-cell .amount {
    font-weight: 600;
    color: #566a7f;
}
.amount-cell .label {
    font-size: 11px;
    color: #a1acb8;
}

.payment-progress {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 6px;
}
.payment-progress .bar {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s;
}
.payment-progress .bar.full { background: #71dd37; }
.payment-progress .bar.partial { background: #ffab00; }
.payment-progress .bar.none { background: #ff3e1d; width: 0 !important; }

.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}
.status-badge.paid { background: #e8fadf; color: #71dd37; }
.status-badge.partial { background: #fff2d6; color: #ffab00; }
.status-badge.due { background: #ffe0db; color: #ff3e1d; }

.action-btn {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 18px;
}
.action-btn.payment { background: #71dd37; color: #fff; }
.action-btn.view { background: #e7e7ff; color: #696cff; }
.action-btn.invoice { background: #fff2d6; color: #ffab00; }
.action-btn.delete { background: #ffe0db; color: #ff3e1d; }
.action-btn:hover { opacity: 0.85; transform: scale(1.05); }

.filter-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

.table-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #697a8d;
}
.table-info .badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 500;
}

</style>
@endpush

@section('content')
    {{-- Summary Stats --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="sales-stats-card total">
                <div class="stat-value">{{ currency($data['total_amount']) }}</div>
                <div class="stat-label">{{ __('Total Sales') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="sales-stats-card">
                <div class="stat-value">{{ currency($data['sale_amount']) }}</div>
                <div class="stat-label">{{ __('Subtotal') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="sales-stats-card paid">
                <div class="stat-value">{{ currency($data['paid_amount']) }}</div>
                <div class="stat-label">{{ __('Total Paid') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="sales-stats-card due">
                <div class="stat-value">{{ currency($data['due_amount']) }}</div>
                <div class="stat-label">{{ __('Total Due') }}</div>
            </div>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="status-tabs">
        <a href="{{ request()->fullUrlWithQuery(['payment_status' => 'due']) }}"
           class="status-tab due-tab {{ request('payment_status', 'due') === 'due' ? 'active' : '' }}">
            {{ __('Due Payments') }}
        </a>
        <a href="{{ request()->fullUrlWithQuery(['payment_status' => 'paid']) }}"
           class="status-tab paid-tab {{ request('payment_status') === 'paid' ? 'active' : '' }}">
            {{ __('Paid Sales') }}
        </a>
        <a href="{{ request()->fullUrlWithQuery(['payment_status' => 'all']) }}"
           class="status-tab {{ request('payment_status') === 'all' ? 'active' : '' }}">
            {{ __('All Sales') }}
        </a>
    </div>

    {{-- Filters --}}
    <div class="filter-card">
        <form class="search_form" action="" method="GET">
            <input type="hidden" name="payment_status" value="{{ request('payment_status', 'due') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small text-muted">{{ __('Search') }}</label>
                    <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                        class="form-control" placeholder="{{ __('Invoice, customer...') }}" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">{{ __('Waiter') }}</label>
                    <select name="waiter_id" class="form-select">
                        <option value="">{{ __('All Waiters') }}</option>
                        @foreach($waiters ?? [] as $waiter)
                            <option value="{{ $waiter->id }}" {{ request('waiter_id') == $waiter->id ? 'selected' : '' }}>
                                {{ $waiter->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">{{ __('From Date') }}</label>
                    <input type="text" class="form-control datepicker" name="from_date"
                        value="{{ request()->get('from_date') }}" placeholder="{{ __('From') }}" autocomplete="off">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">{{ __('To Date') }}</label>
                    <input type="text" class="form-control datepicker" name="to_date"
                        value="{{ request()->get('to_date') }}" placeholder="{{ __('To') }}" autocomplete="off">
                </div>
                <div class="col-md-1">
                    <label class="form-label small text-muted">{{ __('Per Page') }}</label>
                    <select name="par-page" class="form-select">
                        <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>10</option>
                        <option value="25" {{ '25' == request('par-page') ? 'selected' : '' }}>25</option>
                        <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>50</option>
                        <option value="100" {{ '100' == request('par-page') ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="bx bx-search me-1"></i>{{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-reset"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Sales Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                {{ __('Sales List') }}
                @if(request('payment_status', 'due') === 'due')
                    <span class="badge bg-danger ms-2">{{ __('Due') }}</span>
                @elseif(request('payment_status') === 'paid')
                    <span class="badge bg-success ms-2">{{ __('Paid') }}</span>
                @endif
            </h5>
            <div class="d-flex gap-2">
                @adminCan('sales.excel.download')
                    <button type="button" class="btn btn-sm btn-outline-success export">
                        <i class="bx bx-spreadsheet me-1"></i>{{ __('Excel') }}
                    </button>
                @endadminCan
                @adminCan('sales.pdf.download')
                    <button type="button" class="btn btn-sm btn-outline-danger export-pdf">
                        <i class="bx bx-file-blank me-1"></i>{{ __('PDF') }}
                    </button>
                @endadminCan
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('Invoice') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Table / Guest') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
                            <th class="text-center" style="width: 160px;">{{ __('Payment') }}</th>
                            <th class="text-center" style="width: 180px;">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $key => $sale)
                            @php
                                $paidPercent = $sale->grand_total > 0 ? min(100, ($sale->paid_amount / $sale->grand_total) * 100) : 0;
                                $isPaid = (float)$sale->paid_amount >= (float)$sale->grand_total;
                                $isPartial = (float)$sale->paid_amount > 0 && !$isPaid;
                            @endphp
                            <tr>
                                <td class="text-muted">{{ $key + 1 }}</td>
                                <td>
                                    <span class="invoice-cell">{{ $sale->invoice }}</span>
                                    @if($sale->order_type)
                                        <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $sale->order_type)) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="customer-cell">
                                        <div class="customer-avatar">
                                            {{ strtoupper(substr($sale?->customer?->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <div class="customer-info">
                                            <div class="name">{{ $sale?->customer?->name ?? __('Guest') }}</div>
                                            @if($sale->waiter)
                                                <div class="meta">{{ __('Waiter') }}: {{ $sale->waiter->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-info">
                                        @if($sale->table)
                                            <span class="badge bg-primary">{{ $sale->table->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                        @if($sale->guest_count)
                                            <span class="badge bg-secondary">
                                                <i class="bx bx-user me-1"></i>{{ $sale->guest_count }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>{{ \Carbon\Carbon::parse($sale->order_date)->format('d M Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}</small>
                                </td>
                                <td class="amount-cell">
                                    <div class="amount">{{ currency($sale->grand_total) }}</div>
                                    @if($sale->order_discount > 0)
                                        <div class="label">{{ __('Disc') }}: {{ currency($sale->order_discount) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">{{ currency($sale->paid_amount) }}</small>
                                        <span class="status-badge {{ $isPaid ? 'paid' : ($isPartial ? 'partial' : 'due') }}">
                                            {{ $isPaid ? __('Paid') : ($isPartial ? __('Partial') : __('Due')) }}
                                        </span>
                                    </div>
                                    <div class="payment-progress">
                                        <div class="bar {{ $isPaid ? 'full' : ($isPartial ? 'partial' : 'none') }}"
                                             style="width: {{ $paidPercent }}%"></div>
                                    </div>
                                    @if(!$isPaid)
                                        <small class="text-danger">{{ __('Due') }}: {{ currency($sale->due_amount) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        @if ((float)$sale->paid_amount < (float)$sale->grand_total)
                                            <button type="button" class="action-btn payment receive-payment"
                                                    data-id="{{ $sale->id }}"
                                                    data-invoice="{{ $sale->invoice }}"
                                                    data-total="{{ $sale->grand_total }}"
                                                    data-paid="{{ $sale->paid_amount }}"
                                                    data-due="{{ $sale->due_amount }}"
                                                    title="{{ __('Receive Payment') }}">
                                                <i class="bx bx-dollar"></i>
                                            </button>
                                        @endif
                                        @adminCan('sales.view')
                                            <button type="button" class="action-btn view view-sale" data-id="{{ $sale->id }}" title="{{ __('View') }}">
                                                <i class="bx bx-show"></i>
                                            </button>
                                        @endadminCan
                                        @adminCan('sales.invoice')
                                            <a href="{{ route('admin.sales.invoice', $sale->id) }}" class="action-btn invoice" title="{{ __('Invoice') }}">
                                                <i class="bx bx-file"></i>
                                            </a>
                                        @endadminCan
                                        @adminCan('sales.delete')
                                            <button type="button" class="action-btn delete" onclick="deleteData({{ $sale->id }})" title="{{ __('Delete') }}">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        @endadminCan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bx bx-inbox" style="font-size: 48px;"></i>
                                        <p class="mt-2 mb-0">{{ __('No sales found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if (request()->get('par-page') !== 'all' && method_exists($sales, 'hasPages') && $sales->hasPages())
                <div class="p-3 border-top">
                    {{ $sales->onEachSide(0)->links() }}
                </div>
            @endif
        </div>
    </div>

    @include('components.admin.preloader')

    {{-- View Sale Modal --}}
    <div class="modal fade bd-example-modal-xl" id="salemodal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 100%">
            <div class="modal-content" id="modalcontent" style="width: 100%">
            </div>
        </div>
    </div>

    {{-- Include POS Running Order Payment Modal --}}
    @include('pos::modals.running-order-payment', ['accounts' => $accounts, 'posSettings' => $posSettings ?? null])
@endsection

@push('js')
<script>
    'use strict'

    var currencyIcon = '{{ currency_icon() }}';

    $(document).ready(function() {
        $(document).on('click', '.view-sale', function() {
            var id = $(this).data('id');
            $.ajax({
                type: "GET",
                url: "{{ route('admin.sales.show', '') }}/" + id,
                success: function(data) {
                    $('#modalcontent').html(data);
                    $('#salemodal').modal('show');
                }
            });
        })
    })

    function deleteData(id) {
        const modal = $('#deleteModal');
        $('#deleteForm').attr('action', "{{ route('admin.sales.destroy', '') }}/" + id);
        modal.modal('show');
    }

    // Receive Payment - use POS running order payment modal
    $(document).on('click', '.receive-payment', function() {
        var id = $(this).data('id');
        if (typeof openRunningOrderPayment === 'function') {
            openRunningOrderPayment(id);
        } else {
            toastr.error('{{ __("Payment modal not available") }}');
        }
    });
</script>
@endpush
